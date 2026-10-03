@extends('layouts.app')

@section('title', 'قائمة المنتجات')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">المنتجات</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة منتج
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-3"><i class="bi bi-filter me-1"></i> تصفية المنتجات</h6>
        <form action="{{ route('products.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ابحث بالاسم أو SKU..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">كل الموردين</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="low_stock" class="form-select form-select-sm">
                    <option value="">كل المخزون</option>
                    <option value="1" {{ $lowStock ? 'selected' : '' }}>مخزون منخفض</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">تصفية</button>
                @if($search || $categoryId || $supplierId || $lowStock)
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">إعادة تعيين</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">م</th>
                        <th>SKU</th>
                        <th>اسم المنتج</th>
                        <th>التصنيف</th>
                        <th class="text-end">سعر البيع</th>
                        <th class="text-center">المخزون</th>
                        <th width="100">الحالة</th>
                        <th width="150" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <small class="text-muted">{{ $product->supplier->name ?? '-' }}</small>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @php $totalStock = $product->totalStock(); @endphp
                            <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-info' }}">
                                {{ $totalStock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">مn-نشط</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">لا توجد بيانات منتجات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $products->links() }}
    </div>
</div>
@endsection
