@extends('layouts.app')

@section('title', 'قائمة الموردين')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">المورد</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة مورد
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="row g-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ابحث عن مورد..." value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">بحث</button>
                @if($search)
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">إعادة تعيين</a>
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
                        <th>اسم الشركة</th>
                        <th>شخص الاتصال</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th width="100">الحالة</th>
                        <th width="150" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                        <td class="fw-bold">{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_name }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>
                            @if($supplier->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">مn-نشط</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
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
                        <td colspan="7" class="text-center py-4 text-muted">لا توجد بيانات موردين.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
