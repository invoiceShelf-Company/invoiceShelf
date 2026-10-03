@extends('layouts.app')

@section('title', 'تفاصيل منتج')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">تفاصيل المنتج: {{ $product->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> رجوع
        </a>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i> تعديل منتج
        </a>
    </div>
</div>

<div class="row">
    <!-- Product Info -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">معلومات المنتج</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="120" class="text-muted">SKU</td>
                        <td class="fw-bold">{{ $product->sku }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">التصنيف</td>
                        <td>{{ $product->category?->name ?? 'بدون تصنيف' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">المورد</td>
                        <td>{{ $product->supplier?->name ?? 'بدون مورد' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">الوحدة</td>
                        <td>{{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">سعر الشراء</td>
                        <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">سعر البيع</td>
                        <td class="text-primary fw-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">الحد الأدنى للمخزون</td>
                        <td>{{ $product->minimum_stock }} {{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">الحالة</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">مn-نشط</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <hr>
                <div class="mt-3">
                    <h6 class="text-muted small text-uppercase">الوصف</h6>
                    <p class="mb-0">{{ $product->description ?: 'لا يوجد وصف.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Levels -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">المخزون حسب المستودع</span>
                <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-primary' }} fs-6">
                    إجمالي المخزون: {{ $product->totalStock() }} {{ $product->unit }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المستودع</th>
                                <th>الموقع</th>
                                <th class="text-center">كمية المخزون</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->stocks as $stock)
                            <tr>
                                <td class="fw-bold">{{ $stock->warehouse?->name ?? 'مستودع محذوف' }}</td>
                                <td>{{ $stock->warehouse?->location ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info fs-6">{{ $stock->quantity }} {{ $product->unit }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('stock-movements.create', ['product_id' => $product->id, 'warehouse_id' => $stock->warehouse_id]) }}" class="btn btn-sm btn-outline-primary">
                                        تحديث المخزون
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">لا يوجد مخزون في أي مستودع.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('stock-movements.create', ['product_id' => $product->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> إضافة إلى مستودع جديد
                </a>
            </div>
        </div>

        <!-- Recent Movements -->
        <div class="card">
            <div class="card-header bg-white fw-bold">أحدث حركات المخزون</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>المستودع</th>
                                <th>النوع</th>
                                <th class="text-end">الكمية</th>
                                <th>ملاحظات</th>
                                <th>بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $movement->warehouse?->name ?? 'مستودع محذوف' }}</td>
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
                                <td><small>{{ $movement->reference_number ?: '-' }} - {{ $movement->notes }}</small></td>
                                <td>{{ $movement->creator?->name ?? 'النظام' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">لا يوجد سجل حركات بعد.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
