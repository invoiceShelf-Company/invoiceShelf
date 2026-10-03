<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

class ProductController extends Controller
{
    /**
     * Display a listing of products with optional search and filters.
     */
    public function index()
    {
        $search = request('search');
        $categoryId = request('category_id');
        $supplierId = request('supplier_id');
        $isActive = request('is_active');
        $lowStock = request('low_stock');

        $products = Product::with(['category', 'supplier', 'stocks'])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
            ))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($isActive !== null && $isActive !== '', fn ($q) => $q->where('is_active', (bool) $isActive))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Filter low-stock after fetching (because totalStock() requires PHP-side calculation)
        if ($lowStock) {
            $products->setCollection(
                $products->getCollection()->filter(fn (Product $p) => $p->isLowStock())
            );
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'suppliers',
            'search',
            'categoryId',
            'supplierId',
            'isActive',
            'lowStock',
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
            'is_active' => $request->boolean('is_active', true),
            'unit' => $request->input('unit', 'pcs'),
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product with stock per warehouse and recent movements.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'creator', 'stocks.warehouse']);

        $recentMovements = $product->stockMovements()
            ->with(['warehouse', 'creator'])
            ->latest()
            ->take(20)
            ->get();

        return view('products.show', compact('product', 'recentMovements'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'unit' => $request->input('unit', 'pcs'),
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     * Prevents deletion when product has stock movement history.
     */
    public function destroy(Product $product)
    {
        if ($product->stockMovements()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat pergerakan stok.');
        }

        // Remove orphan product_stocks first
        $product->stocks()->delete();
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
