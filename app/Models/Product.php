<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'created_by',
        'name',
        'sku',
        'description',
        'unit',
        'purchase_price',
        'selling_price',
        'minimum_stock',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get total stock across all warehouses.
     */
    public function totalStock(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }

    /**
     * Check if product stock is below minimum_stock threshold.
     */
    public function isLowStock(): bool
    {
        return $this->totalStock() < $this->minimum_stock;
    }
}
