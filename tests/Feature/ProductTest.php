<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function validProductData(array $overrides = []): array
    {
        $category = Category::factory()->create();

        return array_merge([
            'category_id' => $category->id,
            'supplier_id' => null,
            'name' => 'Produk Test',
            'sku' => 'SKU-TEST-001',
            'unit' => 'pcs',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 5,
            'is_active' => true,
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_product_list(): void
    {
        $response = $this->actingAsAdmin()->get(route('products.index'));
        $response->assertOk();
        $response->assertViewIs('products.index');
    }

    public function test_user_can_create_product_with_valid_category(): void
    {
        $data = $this->validProductData();

        $response = $this->actingAsAdmin()->post(route('products.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-TEST-001',
            'name' => 'Produk Test',
        ]);
    }

    public function test_product_sku_must_be_unique(): void
    {
        $existing = Product::factory()->create(['sku' => 'SKU-DUPLIKAT']);

        $response = $this->actingAsAdmin()->post(route('products.store'), $this->validProductData([
            'sku' => 'SKU-DUPLIKAT',
            'category_id' => $existing->category_id,
        ]));

        $response->assertSessionHasErrors('sku');
    }

    public function test_product_requires_existing_category(): void
    {
        $response = $this->actingAsAdmin()->post(route('products.store'), $this->validProductData([
            'category_id' => 99999,
        ]));

        $response->assertSessionHasErrors('category_id');
    }

    public function test_product_purchase_price_cannot_be_negative(): void
    {
        $response = $this->actingAsAdmin()->post(route('products.store'), $this->validProductData([
            'purchase_price' => -100,
        ]));

        $response->assertSessionHasErrors('purchase_price');
    }

    public function test_user_can_view_product_detail(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAsAdmin()->get(route('products.show', $product));

        $response->assertOk();
        $response->assertViewIs('products.show');
    }

    public function test_user_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAsAdmin()->put(route('products.update', $product), [
            'category_id' => $product->category_id,
            'name' => 'Nama Baru',
            'sku' => $product->sku,
            'unit' => 'box',
            'purchase_price' => 20000,
            'selling_price' => 30000,
            'minimum_stock' => 10,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_user_cannot_delete_product_with_stock_movements(): void
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 10,
            'stock_before' => 0,
            'stock_after' => 10,
            'movement_date' => now()->toDateString(),
        ]);

        $response = $this->actingAsAdmin()->delete(route('products.destroy', $product));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
