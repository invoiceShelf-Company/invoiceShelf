<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('laraventry.locales.'.app()->getLocale().'.dir', 'rtl') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laraventry') }} | {{ \App\Support\LocalizedText::get(trim($__env->yieldContent('title', 'لوحة التحكم'))) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if(app()->getLocale() === 'ar')<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">@else<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">@endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --ink:#162033; --muted:#718096; --brand:#2563eb; --brand-dark:#1d4ed8; --surface:#fff; --bg:#f4f7fb; --line:#e7edf5; --navy:#111827; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink); font-family:'Cairo',system-ui,sans-serif; font-size:.92rem; }
        .app-shell { min-height:100vh; display:flex; }
        .sidebar { width:264px; flex:0 0 264px; background:var(--navy); color:#dbeafe; min-height:100vh; position:sticky; top:0; align-self:flex-start; z-index:1030; }
        .brand { display:flex; align-items:center; gap:.75rem; padding:1.5rem 1.35rem 1.35rem; color:#fff; text-decoration:none; font-weight:800; font-size:1.2rem; border-bottom:1px solid rgba(255,255,255,.08); }
        .brand-mark { width:38px; height:38px; display:grid; place-items:center; border-radius:12px; background:linear-gradient(135deg,#60a5fa,#2563eb); box-shadow:0 8px 20px rgba(37,99,235,.35); }
        .nav-section { padding:.85rem 1.2rem .35rem; color:#64748b; font-size:.68rem; font-weight:700; letter-spacing:.08em; }
        .sidebar .nav-link { color:#94a3b8; margin:.18rem .7rem; padding:.72rem .9rem; border-radius:10px; display:flex; align-items:center; gap:.7rem; transition:.2s ease; }
        .sidebar .nav-link i { font-size:1.05rem; width:20px; text-align:center; }
        .sidebar .nav-link:hover { color:#fff; background:rgba(255,255,255,.07); transform:translateX(-2px); }
        .sidebar .nav-link.active { color:#fff; background:linear-gradient(90deg,rgba(37,99,235,.95),rgba(37,99,235,.58)); box-shadow:0 7px 18px rgba(0,0,0,.16); }
        .main-wrap { min-width:0; flex:1; }
        .topbar { height:74px; background:rgba(255,255,255,.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; padding:0 2rem; position:sticky; top:0; z-index:1000; }
        .page-content { padding:2rem; max-width:1600px; margin:auto; }
        .eyebrow { color:var(--brand); font-size:.75rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
        .page-title { font-size:1.7rem; font-weight:800; margin:.25rem 0 0; }
        .card { border:1px solid var(--line); border-radius:16px; box-shadow:0 8px 28px rgba(15,23,42,.045); background:var(--surface); }
        .card-header { border-bottom:1px solid var(--line); background:transparent; padding:1.1rem 1.25rem; }
        .table > :not(caption) > * > * { padding:.9rem 1rem; border-bottom-color:#eef2f7; vertical-align:middle; }
        .table thead th { color:#64748b; font-size:.75rem; font-weight:700; white-space:nowrap; }
        .btn { border-radius:10px; font-weight:600; padding:.6rem 1rem; }
        .btn-primary { background:var(--brand); border-color:var(--brand); }
        .btn-primary:hover { background:var(--brand-dark); border-color:var(--brand-dark); }
        .form-control,.form-select { border-color:#dbe3ee; border-radius:10px; padding:.68rem .85rem; }
        .form-control:focus,.form-select:focus { border-color:#93c5fd; box-shadow:0 0 0 .2rem rgba(37,99,235,.12); }
        .avatar { width:38px; height:38px; border-radius:50%; display:grid; place-items:center; color:#1d4ed8; background:#dbeafe; font-weight:800; }
        .alert { border:0; border-radius:12px; }
        .mobile-menu { display:none; }
        .icon-box{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;font-size:1.15rem}.icon-box.blue{background:#dbeafe;color:#2563eb}.icon-box.violet{background:#ede9fe;color:#7c3aed}.icon-box.green{background:#dcfce7;color:#16a34a}.icon-box.orange{background:#ffedd5;color:#ea580c}.icon-box.red{background:#fee2e2;color:#dc2626}.icon-box.indigo{background:#e0e7ff;color:#4f46e5}.icon-box.teal{background:#ccfbf1;color:#0f766e}.icon-box.pink{background:#fce7f3;color:#db2777}.metric-row{display:flex;justify-content:space-between;padding:.8rem 0;border-bottom:1px solid var(--line)}.metric-row:last-child{border-bottom:0}.nav-badge{margin-right:auto;font-size:.68rem;background:rgba(255,255,255,.1);padding:.15rem .4rem;border-radius:999px}
        @media (max-width: 991.98px) { .sidebar { position:fixed; right:-280px; transition:right .25s ease; } .sidebar.show { right:0; } html[dir="ltr"] .sidebar { right:auto; left:-280px; } html[dir="ltr"] .sidebar.show { right:auto; left:0; } .mobile-menu { display:inline-flex; } .topbar { padding:0 1rem; } .page-content { padding:1.25rem; } }
        @media (max-width:575.98px) { .page-title { font-size:1.35rem; } .topbar .user-label { display:none; } }
    </style>
    @yield('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="appSidebar">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark"><i class="bi bi-buildings-fill"></i></span><span>Laraventry Pro</span></a>
        <div class="nav-section">الرئيسية</div><nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>لوحة التحكم</span></a>
            <a class="nav-link {{ request()->routeIs('facilities.*') ? 'active' : '' }}" href="{{ route('facilities.index') }}"><i class="bi bi-buildings"></i><span>المنشآت والعقارات</span></a>
            <a class="nav-link {{ request()->routeIs('people.*') ? 'active' : '' }}" href="{{ route('people.index') }}"><i class="bi bi-people"></i><span>الأشخاص والموظفون</span></a>
            <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}"><i class="bi bi-diagram-3"></i><span>الأقسام</span></a>
            <a class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}" href="{{ route('shifts.index') }}"><i class="bi bi-calendar2-week"></i><span>الورديات وساعات الدوام</span></a>
        </nav>
        <div class="nav-section mt-3">الحضور والأمن</div><nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}"><i class="bi bi-clock-history"></i><span>الحضور والغياب</span></a>
            <a class="nav-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}" href="{{ route('visitors.index') }}"><i class="bi bi-person-vcard"></i><span>الزوار</span></a>
            <a class="nav-link {{ request()->routeIs('access-logs.*') ? 'active' : '' }}" href="{{ route('access-logs.index') }}"><i class="bi bi-door-open"></i><span>الدخول والخروج</span></a>
        </nav>
        @if(auth()->user()->isAdmin())
        <div class="nav-section mt-3">{{ __('Administration') }}</div><nav class="nav flex-column"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-shield-check"></i><span>{{ __('Users & Permissions') }}</span></a><a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}"><i class="bi bi-credit-card-2-front"></i><span>{{ __('Payments') }}</span></a><a class="nav-link {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}" href="{{ route('payment-methods.index') }}"><i class="bi bi-wallet2"></i><span>{{ __('Payment Methods') }}</span></a></nav>
        @endif
        <div class="nav-section mt-3">المخزون</div><nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i><span>المنتجات</span></a>
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags"></i><span>التصنيفات</span></a>
            <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-truck"></i><span>الموردون</span></a>
            <a class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}"><i class="bi bi-boxes"></i><span>المستودعات</span></a>
            <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}"><i class="bi bi-arrow-left-right"></i><span>حركة المخزون</span></a>
        </nav>
    </aside>
    <div class="main-wrap">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3"><button class="btn btn-light mobile-menu" type="button" onclick="document.getElementById('appSidebar').classList.toggle('show')"><i class="bi bi-list fs-5"></i></button><span class="text-muted d-none d-md-inline">{{ __('Smart Business & Facility Management') }}</span></div>
            @auth
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2" data-bs-toggle="dropdown"><i class="bi bi-translate"></i><span>{{ config('laraventry.locales.'.app()->getLocale().'.name') }}</span></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        @foreach(config('laraventry.locales') as $code => $lang)
                            <li><a class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ route('language.switch', $code) }}">{{ $lang['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown"><button class="btn d-flex align-items-center gap-2 p-0 border-0" data-bs-toggle="dropdown"><span class="avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span><span class="user-label text-end"><strong class="d-block">{{ Auth::user()->name }}</strong><small class="text-muted">{{ Auth::user()->role?->label() ?? 'مستخدم' }}</small></span><i class="bi bi-chevron-down text-muted"></i></button><ul class="dropdown-menu dropdown-menu-start shadow-sm border-0"><li><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right ms-2"></i>تسجيل الخروج</button></form></li></ul></div></div>
            @endauth
        </header>
        <main class="page-content">
            @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle ms-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-circle ms-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
