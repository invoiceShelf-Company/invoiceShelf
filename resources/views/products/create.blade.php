@extends('layouts.app')

@section('title', 'إضافة منتج')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">إضافة منتج جديد</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> رجوع
        </a>
    </div>
</div>

<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">معلومات المنتج</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم المنتج</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU / رمز المنتج</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">الوحدة (Unit)</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">Harga & الحد الأدنى للمخزون</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">سعر الشراء (Rp)</label>
                            <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', 0) }}" required>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selling_price" class="form-label">سعر البيع (Rp)</label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', 0) }}" required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="minimum_stock" class="form-label">الحد الأدنى للمخزون</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" required>
                            <small class="text-muted">سيظهر تنبيه انخفاض المخزون إذا كان أقل من هذا الرقم.</small>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">Relasi & الحالة</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">التصنيف</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- اختر تصنيف --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">المورد</label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- اختر موردًا --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 py-2">حفظ المنتج</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
