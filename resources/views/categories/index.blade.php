@extends('layouts.app')

@section('title', 'قائمة التصنيفات')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">التصنيف</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> إضافة تصنيف
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('categories.index') }}" method="GET" class="row g-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ابحث عن تصنيف..." value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">بحث</button>
                @if($search)
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">إعادة تعيين</a>
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
                        <th>اسم التصنيف</th>
                        <th>المعرف (الرابط)</th>
                        <th>الوصف</th>
                        <th width="100">الحالة</th>
                        <th width="150" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                        <td class="fw-bold">{{ $category->name }}</td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">مn-نشط</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">لا توجد بيانات تصنيفات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $categories->links() }}
    </div>
</div>
@endsection
