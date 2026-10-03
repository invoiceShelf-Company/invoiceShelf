<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Inventory\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function makeProduct(int $minStock = 0): Product
    {
        return Product::factory()->create(['minimum_stock' => $minStock]);
    }

    private function makeWarehouse(): Warehouse
    {
        return Warehouse::factory()->create();
    }

    private function service(): StockMovementService
    {
        return app(StockMovementService::class);
    }

    // ── Service layer tests (no HTTP — auth not required) ─────────────────

    public function test_stock_in_increases_product_stock(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 50,
            'movement_date' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50,
        ]);
    }

    public function test_stock_out_decreases_product_stock(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 100,
            'movement_date' => now()->toDateString(),
        ]);

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'out',
            'quantity' => 30,
            'movement_date' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 70,
        ]);
    }

    public function test_stock_out_fails_when_quantity_exceeds_stock(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 10,
            'movement_date' => now()->toDateString(),
        ]);

        $this->expectException(ValidationException::class);

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'out',
            'quantity' => 20,
            'movement_date' => now()->toDateString(),
        ]);
    }

    public function test_movement_records_stock_before_and_after(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $movement = $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 40,
            'movement_date' => now()->toDateString(),
        ]);

        $this->assertEquals(0, $movement->stock_before);
        $this->assertEquals(40, $movement->stock_after);
        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'stock_before' => 0,
            'stock_after' => 40,
        ]);
    }

    public function test_adjustment_sets_stock_to_absolute_value(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 50,
            'movement_date' => now()->toDateString(),
        ]);

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'adjustment',
            'quantity' => 25,
            'movement_date' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 25,
        ]);
    }

    public function test_stock_out_with_zero_stock_fails(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $this->expectException(ValidationException::class);

        $this->service()->createMovement([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'out',
            'quantity' => 1,
            'movement_date' => now()->toDateString(),
        ]);
    }

    // ── HTTP controller tests (auth required) ─────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('stock-movements.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_stock_movement_list(): void
    {
        $response = $this->actingAsAdmin()->get(route('stock-movements.index'));
        $response->assertOk();
        $response->assertViewIs('stock-movements.index');
    }

    public function test_user_can_submit_stock_movement_via_http(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $response = $this->actingAsAdmin()->post(route('stock-movements.store'), [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 20,
            'movement_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('stock-movements.index'));
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 20,
        ]);
    }

    public function test_stock_movement_requires_valid_type(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $response = $this->actingAsAdmin()->post(route('stock-movements.store'), [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'invalid_type',
            'quantity' => 10,
            'movement_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_stock_movement_quantity_must_be_at_least_one(): void
    {
        $product = $this->makeProduct();
        $warehouse = $this->makeWarehouse();

        $response = $this->actingAsAdmin()->post(route('stock-movements.store'), [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 0,
            'movement_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('quantity');
    }
}
