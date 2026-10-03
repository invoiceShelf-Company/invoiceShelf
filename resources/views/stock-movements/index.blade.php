@extends('layouts.app')

@section('title', 'سجل حركات المخزون')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">سجل حركات المخزون</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-movements.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> تسجيل حركة جديدة
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-3"><i class="bi bi-filter me-1"></i> تصفية السجل</h6>
        <form action="{{ route('stock-movements.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">كل المنتجات</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="warehouse_id" class="form-select form-select-sm">
                    <option value="">كل المستودعات</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">كل الأنواع</option>
                    <option value="in" {{ $type === 'in' ? 'selected' : '' }}>وارد</option>
                    <option value="out" {{ $type === 'out' ? 'selected' : '' }}>صادر</option>
                    <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>تسوية</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" placeholder="Dari التاريخ">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" placeholder="Sampai التاريخ">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">تصفية</button>
                @if($productId || $warehouseId || $type || $dateFrom || $dateTo)
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-sm btn-outline-secondary">إعادة تعيين</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>التاريخ</th>
                        <th>المنتجات</th>
                        <th>المستودع</th>
                        <th>النوع</th>
                        <th class="text-end">الكمية</th>
                        <th>مرجع / ملاحظات</th>
                        <th>بواسطة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->movement_date->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $movement->product->name }}</div>
                            <small class="text-muted">{{ $movement->product->sku }}</small>
                        </td>
                        <td>{{ $movement->warehouse->name }}</td>
                        <td>
                            @if($movement->type === 'in')
                                <span class="badge bg-success">وارد</span>
                            @elseif($movement->type === 'out')
                                <span class="badge bg-danger">صادر</span>
                            @else
                                <span class="badge bg-warning">تسوية</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $movement->type === 'in' ? 'text-success' : ($movement->type === 'out' ? 'text-danger' : '') }}">
                            {{ $movement->type === 'out' ? '-' : '+' }}{{ $movement->quantity }}
                        </td>
                        <td>
                            <div>{{ $movement->reference_number ?: '-' }}</div>
                            <small class="text-muted">{{ Str::limit($movement->notes, 30) }}</small>
                        </td>
                        <td>{{ $movement->creator->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">لا توجد حركات مخزون.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $movements->links() }}
    </div>
</div>
@endsection
