<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\Inventory\StockMovementService;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function __construct(
        private readonly StockMovementService $stockMovementService
    ) {}

    /**
     * Display a listing of stock movements with optional filters.
     */
    public function index()
    {
        $productId = request('product_id');
        $warehouseId = request('warehouse_id');
        $type = request('type');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        // Sanitize filter values
        $validTypes = ['in', 'out', 'adjustment'];
        $type = in_array($type, $validTypes, true) ? $type : null;
        $dateFrom = $dateFrom && strtotime($dateFrom) ? $dateFrom : null;
        $dateTo = $dateTo && strtotime($dateTo) ? $dateTo : null;
        $productId = $productId ? (int) $productId : null;
        $warehouseId = $warehouseId ? (int) $warehouseId : null;

        $movements = StockMovement::with(['product', 'warehouse', 'creator'])
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($dateFrom, fn ($q) => $q->whereDate('movement_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('movement_date', '<=', $dateTo))
            ->latest('movement_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('stock-movements.index', compact(
            'movements',
            'products',
            'warehouses',
            'productId',
            'warehouseId',
            'type',
            'dateFrom',
            'dateTo',
        ));
    }

    /**
     * Show the form for creating a new stock movement.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('stock-movements.create', compact('products', 'warehouses'));
    }

    /**
     * Store a newly created stock movement using the service layer.
     */
    public function store(StoreStockMovementRequest $request)
    {
        try {
            $movement = $this->stockMovementService->createMovement(
                $request->validated(),
                auth()->user()
            );

            return redirect()
                ->route('stock-movements.index')
                ->with('success', 'Pergerakan stok berhasil dicatat.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }
}
