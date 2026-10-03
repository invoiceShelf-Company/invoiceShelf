<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('laraventry.locales.'.app()->getLocale().'.dir', 'rtl') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <title>{{ \App\Support\LocalizedText::get('إنشاء حساب جديد') }} | {{ config('app.name', 'Laraventry') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if(app()->getLocale() === 'ar')<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">@else<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">@endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --ink: #162033;
            --muted: #718096;
            --line: #dbe3ee;
            --soft: #f6f8fc;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 10% 10%, #dbeafe 0, transparent 30%),
                radial-gradient(circle at 90% 90%, #e0e7ff 0, transparent 32%),
                var(--soft);
            color: var(--ink);
            font-family: 'Cairo', system-ui, sans-serif;
        }

        .register-shell {
            width: 100%;
            max-width: 940px;
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            overflow: hidden;
            border: 1px solid #e6edf6;
            border-radius: 28px;
            background: #fff;
            box-shadow: 0 28px 90px rgba(30, 64, 175, .13);
            animation: cardIn .55s ease both;
        }

        .register-info {
            position: relative;
            padding: 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(145deg, #eff6ff, #eef2ff);
        }

        .register-info::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            border: 24px solid rgba(37, 99, 235, .06);
            left: -75px;
            bottom: -65px;
        }

        .brand-mark {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            margin-bottom: 22px;
            border-radius: 18px;
            background: linear-gradient(135deg, #60a5fa, #2563eb);
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 14px 28px rgba(37,99,235,.28);
        }

        .info-list { list-style: none; padding: 0; margin: 28px 0 0; }
        .info-list li { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px; color: #475569; }
        .info-list i { color: var(--brand); font-size: 1.1rem; margin-top: 2px; }

        .register-form { padding: 42px; }
        .form-control {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .78rem 1rem;
            min-height: 48px;
            transition: border-color .2s, box-shadow .2s, transform .2s;
        }

        .form-control:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 .22rem rgba(37,99,235,.12);
        }

        .field-wrap { position: relative; }
        .password-toggle {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #64748b;
            padding: 7px;
            z-index: 3;
        }

        .password-input { padding-left: 48px !important; }

        .btn-register {
            min-height: 50px;
            border: 0;
            border-radius: 12px;
            background: var(--brand);
            font-weight: 700;
            transition: transform .18s, background .18s, box-shadow .18s;
        }

        .btn-register:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(37,99,235,.22);
        }

        .btn-register:disabled { opacity: .75; transform: none; }

        .strength {
            height: 6px;
            margin-top: 9px;
            border-radius: 999px;
            overflow: hidden;
            background: #e2e8f0;
        }

        .strength-bar {
            width: 0;
            height: 100%;
            border-radius: inherit;
            transition: width .25s ease;
        }

        .strength-text { font-size: .78rem; margin-top: 5px; color: var(--muted); }

        .requirements {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 12px;
            margin-top: 10px;
            font-size: .76rem;
            color: #94a3b8;
        }

        .requirements .ok { color: #16a34a; }
        .requirements i { margin-left: 3px; }

        .form-check-input:checked {
            background-color: var(--brand);
            border-color: var(--brand);
        }

        .alert { border-radius: 12px; }
        .login-link { color: var(--brand); font-weight: 700; text-decoration: none; }
        .login-link:hover { text-decoration: underline; }

        .is-valid { border-color: #86efac !important; }
        .is-invalid { border-color: #fca5a5 !important; }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 800px) {
            body { padding: 12px; }
            .register-shell { grid-template-columns: 1fr; max-width: 560px; }
            .register-info { display: none; }
            .register-form { padding: 30px 24px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>

<div class="register-shell">
    <aside class="register-info">
        <div class="brand-mark"><i class="bi bi-box-seam-fill"></i></div>
        <h1 class="h2 fw-bold mb-2">{{ \App\Support\LocalizedText::get('انضم إلى Laraventry') }}</h1>
        <p class="text-secondary mb-0">
            {{ \App\Support\LocalizedText::get('أنشئ حسابك وابدأ بإدارة المنتجات والمخزون والموردين من مكان واحد.') }}
        </p>

        <ul class="info-list">
            <li><i class="bi bi-check-circle-fill"></i><span>{{ \App\Support\LocalizedText::get('لوحة تحكم واضحة وسهلة الاستخدام') }}</span></li>
            <li><i class="bi bi-check-circle-fill"></i><span>{{ \App\Support\LocalizedText::get('إدارة المنتجات والمخزون وحركات المخزون') }}</span></li>
            <li><i class="bi bi-check-circle-fill"></i><span>{{ \App\Support\LocalizedText::get('حساب آمن مع حماية للطلبات') }}</span></li>
        </ul>
    </aside>

    <main class="register-form">
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">إنشاء حساب جديد</h2>
                    <p class="text-muted mb-0">{{ \App\Support\LocalizedText::get('أدخل بياناتك للبدء') }}</p>
                </div>
                <div class="d-md-none brand-mark mb-0" style="width:46px;height:46px;border-radius:14px;font-size:1.15rem;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 small" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> {{ \App\Support\LocalizedText::get('يرجى مراجعة البيانات') }}</div>
                <ul class="mb-0 pe-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="registerForm" action="{{ route('register.store') }}" method="POST" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">{{ \App\Support\LocalizedText::get('الاسم الكامل') }}</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="{{ \App\Support\LocalizedText::get('مثال: أحمد محمد') }}"
                    autocomplete="name"
                    minlength="2"
                    maxlength="100"
                    required
                    autofocus
                >
                <div class="invalid-feedback" id="nameFeedback">
                    @error('name'){{ $message }}@else {{ \App\Support\LocalizedText::get('يرجى إدخال اسم صحيح.') }}@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">{{ \App\Support\LocalizedText::get('البريد الإلكتروني') }}</label>
                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="name@company.com"
                    autocomplete="email"
                    required
                >
                <div class="invalid-feedback">
                    @error('email'){{ $message }}@else {{ \App\Support\LocalizedText::get('يرجى إدخال بريد إلكتروني صحيح.') }}@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">{{ \App\Support\LocalizedText::get('كلمة المرور') }}</label>
                <div class="field-wrap">
                    <input
                        type="password"
                        class="form-control password-input @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="{{ \App\Support\LocalizedText::get('8 أحرف على الأقل') }}"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="password" aria-label="{{ \App\Support\LocalizedText::get('إظهار كلمة المرور') }}">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <div class="strength" aria-hidden="true"><div id="strengthBar" class="strength-bar"></div></div>
                <div id="strengthText" class="strength-text">{{ \App\Support\LocalizedText::get('قوة كلمة المرور') }}</div>

                <div class="requirements" id="requirements">
                    <span data-rule="length"><i class="bi bi-circle"></i> {{ \App\Support\LocalizedText::get('8 أحرف على الأقل') }}</span>
                    <span data-rule="lower"><i class="bi bi-circle"></i> {{ \App\Support\LocalizedText::get('حرف صغير') }}</span>
                    <span data-rule="upper"><i class="bi bi-circle"></i> {{ \App\Support\LocalizedText::get('حرف كبير') }}</span>
                    <span data-rule="number"><i class="bi bi-circle"></i> {{ \App\Support\LocalizedText::get('رقم') }}</span>
                </div>

                <div class="invalid-feedback d-block">
                    @error('password'){{ $message }}@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">{{ \App\Support\LocalizedText::get('تأكيد كلمة المرور') }}</label>
                <div class="field-wrap">
                    <input
                        type="password"
                        class="form-control password-input"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="{{ \App\Support\LocalizedText::get('أعد كتابة كلمة المرور') }}"
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="{{ \App\Support\LocalizedText::get('إظهار تأكيد كلمة المرور') }}">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="matchFeedback" class="small mt-1"></div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                <label class="form-check-label small text-muted" for="terms">
                    {{ \App\Support\LocalizedText::get('أوافق على شروط الاستخدام وسياسة الخصوصية.') }}
                </label>
                <div class="invalid-feedback">يجب الموافقة قبل إنشاء الحساب.</div>
            </div>

            <button id="submitButton" type="submit" class="btn btn-primary btn-register w-100">
                <span class="button-text">{{ \App\Support\LocalizedText::get('إنشاء الحساب') }}</span>
                <span class="button-loading d-none">
                    <span class="spinner-border spinner-border-sm ms-1" aria-hidden="true"></span>
                    {{ \App\Support\LocalizedText::get('جارٍ إنشاء الحساب...') }}
                </span>
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            {{ \App\Support\LocalizedText::get('لديك حساب بالفعل؟') }}
            <a class="login-link" href="{{ route('login') }}">{{ \App\Support\LocalizedText::get('تسجيل الدخول') }}</a>
        </p>
    </main>
</div>

<script>
(() => {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const matchFeedback = document.getElementById('matchFeedback');
    const submitButton = document.getElementById('submitButton');
    const translations = {
        strength: @json(\App\Support\LocalizedText::get('قوة كلمة المرور')),
        weak: @json(\App\Support\LocalizedText::get('ضعيفة')),
        medium: @json(\App\Support\LocalizedText::get('متوسطة')),
        good: @json(\App\Support\LocalizedText::get('جيدة')),
        strong: @json(\App\Support\LocalizedText::get('قوية')),
        matched: @json(\App\Support\LocalizedText::get('كلمتا المرور متطابقتان ✓')),
        mismatched: @json(\App\Support\LocalizedText::get('كلمتا المرور غير متطابقتين')),
        show: @json(\App\Support\LocalizedText::get('إظهار كلمة المرور')),
        hide: @json(\App\Support\LocalizedText::get('إخفاء كلمة المرور')),
    };

    const rules = {
        length: value => value.length >= 8,
        lower: value => /[a-z]/.test(value),
        upper: value => /[A-Z]/.test(value),
        number: value => /\d/.test(value)
    };

    function updateStrength() {
        const value = password.value;
        let score = 0;

        Object.entries(rules).forEach(([name, check]) => {
            const el = document.querySelector(`[data-rule="${name}"]`);
            const icon = el.querySelector('i');
            const ok = check(value);
            if (ok) score++;
            el.classList.toggle('ok', ok);
            icon.className = ok ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        });

        const width = [0, 25, 50, 75, 100][score];
        strengthBar.style.width = width + '%';

        const labels = [translations.strength, translations.weak, translations.medium, translations.good, translations.strong];
        strengthText.textContent = labels[score];
        strengthText.className = 'strength-text ' + (score >= 3 ? 'text-success' : score >= 2 ? 'text-warning' : '');
    }

    function updateMatch() {
        if (!confirmation.value) {
            confirmation.classList.remove('is-valid', 'is-invalid');
            matchFeedback.textContent = '';
            return;
        }

        const matched = password.value === confirmation.value;
        confirmation.classList.toggle('is-valid', matched);
        confirmation.classList.toggle('is-invalid', !matched);
        matchFeedback.textContent = matched ? translations.matched : translations.mismatched;
        matchFeedback.className = 'small mt-1 ' + (matched ? 'text-success' : 'text-danger');
    }

    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            button.setAttribute('aria-label', show ? translations.hide : translations.show);
        });
    });

    password.addEventListener('input', () => {
        updateStrength();
        updateMatch();
    });

    confirmation.addEventListener('input', updateMatch);

    form.addEventListener('submit', event => {
        if (!form.checkValidity() || password.value !== confirmation.value) {
            event.preventDefault();
            event.stopPropagation();
            updateMatch();
            form.classList.add('was-validated');
            return;
        }

        submitButton.disabled = true;
        submitButton.querySelector('.button-text').classList.add('d-none');
        submitButton.querySelector('.button-loading').classList.remove('d-none');
    });
})();
</script>

</body>
</html>
