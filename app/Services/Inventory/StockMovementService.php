<?php

namespace App\Services\Inventory;

use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    /**
     * Create a stock movement and update the corresponding product stock.
     *
     * Rules:
     * - 'in'         : quantity added to current stock
     * - 'out'        : quantity deducted from current stock (cannot go negative)
     * - 'adjustment' : quantity becomes the new absolute stock value
     *
     * @param  array  $data  Validated data from StoreStockMovementRequest
     * @param  User|null  $user  Authenticated user performing the action
     *
     * @throws ValidationException When an 'out' movement would cause negative stock
     */
    public function createMovement(array $data, ?User $user = null): StockMovement
    {
        return DB::transaction(function () use ($data, $user) {
            // Lock the row so concurrent requests don't cause race conditions
            $stock = ProductStock::query()
                ->where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->first();

            // Create the stock record with 0 quantity if it doesn't exist yet
            if (! $stock) {
                $stock = ProductStock::create([
                    'product_id' => $data['product_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'quantity' => 0,
                ]);
            }

            $before = (int) $stock->quantity;

            $after = match ($data['type']) {
                'in' => $before + (int) $data['quantity'],
                'out' => $before - (int) $data['quantity'],
                'adjustment' => (int) $data['quantity'],
            };

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => ['Stok tidak mencukupi. Stok tersedia: '.$before.', stok keluar: '.$data['quantity'].'.'],
                ]);
            }

            $stock->update(['quantity' => $after]);

            return StockMovement::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'created_by' => $user?->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'stock_before' => $before,
                'stock_after' => $after,
                'reference_number' => $data['reference_number'] ?? null,
                'movement_date' => $data['movement_date'],
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
