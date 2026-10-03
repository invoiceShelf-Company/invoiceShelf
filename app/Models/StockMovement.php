<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'created_by',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_number',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
