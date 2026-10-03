<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('laraventry.locales.'.app()->getLocale().'.dir', 'rtl') }}">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Support\LocalizedText::get('تسجيل الدخول') }} | {{ config('app.name', 'Laraventry') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    @if(app()->getLocale() === 'ar')<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">@else<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">@endif
    <style>
        :root{--brand:#2563eb;--ink:#162033;--muted:#718096}*{box-sizing:border-box}body{min-height:100vh;margin:0;display:grid;place-items:center;padding:1.5rem;background:radial-gradient(circle at 15% 15%,#dbeafe 0,transparent 30%),radial-gradient(circle at 90% 85%,#e0e7ff 0,transparent 32%),#f6f8fc;font-family:'Cairo',system-ui,sans-serif;color:var(--ink)}.login-card{width:100%;max-width:430px;padding:2.5rem;border:1px solid #e6edf6;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(30,64,175,.12)}.brand-mark{width:56px;height:56px;display:grid;place-items:center;margin:0 auto 1.25rem;border-radius:17px;background:linear-gradient(135deg,#60a5fa,#2563eb);color:#fff;font-size:1.5rem;box-shadow:0 12px 25px rgba(37,99,235,.3)}.form-control{border-color:#dbe3ee;border-radius:11px;padding:.75rem .9rem}.form-control:focus{border-color:#93c5fd;box-shadow:0 0 0 .2rem rgba(37,99,235,.12)}.btn-primary{border:0;border-radius:11px;padding:.78rem;background:var(--brand);font-weight:700}.btn-primary:hover{background:#1d4ed8}.form-check-input:checked{background-color:var(--brand);border-color:var(--brand)}
    </style>
</head>
<body>
<div class="position-fixed top-0 end-0 m-3 dropdown"><button class="btn btn-light border" data-bs-toggle="dropdown"><i class="bi bi-translate"></i> {{ config('laraventry.locales.'.app()->getLocale().'.name') }}</button><ul class="dropdown-menu dropdown-menu-end">@foreach(config('laraventry.locales') as $code => $lang)<li><a class="dropdown-item" href="{{ route('language.switch',$code) }}">{{ $lang['name'] }}</a></li>@endforeach</ul></div><div class="card login-card">
    <div class="text-center mb-4"><div class="brand-mark"><i class="bi bi-box-seam-fill"></i></div><h1 class="h3 fw-bold mb-2">Laraventry</h1><p class="text-muted mb-0">{{ \App\Support\LocalizedText::get('سجّل الدخول لإدارة مخزونك بكفاءة') }}</p></div>
    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf
        <div class="mb-3"><label for="email" class="form-label fw-semibold">البريد الإلكتروني</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="password" class="form-label fw-semibold">كلمة المرور</label><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="d-flex align-items-center justify-content-between mb-4"><div class="form-check"><input type="checkbox" class="form-check-input" id="remember" name="remember"><label class="form-check-label text-muted" for="remember">{{ \App\Support\LocalizedText::get('تذكرني') }}</label></div><span class="small text-muted">{{ \App\Support\LocalizedText::get('وصول آمن ومشفر') }}</span></div>
        <button type="submit" class="btn btn-primary w-100">{{ \App\Support\LocalizedText::get('تسجيل الدخول') }} <i class="bi bi-arrow-left me-2"></i></button>
        <p class="text-center text-muted small mt-4 mb-0">
            {{ \App\Support\LocalizedText::get('ليس لديك حساب؟') }}
            <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">{{ \App\Support\LocalizedText::get('إنشاء حساب جديد') }}</a>
        </p>
    </form>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
