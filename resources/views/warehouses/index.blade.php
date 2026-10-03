@extends('layouts.app')

@section('title', 'قائمة المستودعات')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">المستودع</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('warehouses.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة مستودع
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('warehouses.index') }}" method="GET" class="row g-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ابحث عن مستودع..." value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">بحث</button>
                @if($search)
                    <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-secondary">إعادة تعيين</a>
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
                        <th>رمز المستودع</th>
                        <th>اسم المستودع</th>
                        <th>العنوان / الموقع</th>
                        <th width="100">الحالة</th>
                        <th width="150" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                    <tr>
                        <td>{{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + $loop->iteration }}</td>
                        <td><code>{{ $warehouse->code }}</code></td>
                        <td class="fw-bold">{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->address }}</td>
                        <td>
                            @if($warehouse->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">مn-نشط</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gudang ini?')">
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
                        <td colspan="6" class="text-center py-4 text-muted">لا توجد بيانات مستودعات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $warehouses->links() }}
    </div>
</div>
@endsection
