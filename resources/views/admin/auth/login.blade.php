<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — {{ config('app.name', 'Shopenhood') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Subtle grid pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(212,175,55,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,175,55,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .card {
            position: relative;
            width: 100%;
            max-width: 400px;
            background: #1A1A1A;
            border: 1px solid #37474F;
            border-radius: 6px;
            overflow: hidden;
        }

        /* Gold top accent line */
        .card::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }

        .card-body {
            padding: 36px 32px 32px;
        }

        /* Logo / Brand */
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: #D4AF37;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .brand-icon i { font-size: 20px; color: #000000; }
        .brand h1 {
            font-size: 18px;
            font-weight: 700;
            color: #E0E0E0;
            letter-spacing: -0.01em;
        }
        .brand p {
            font-size: 12px;
            color: #37474F;
            margin-top: 3px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #37474F;
            margin-bottom: 24px;
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: rgba(192,57,43,0.15);
            border: 1px solid rgba(192,57,43,0.4);
            color: #E74C3C;
        }
        .alert-success {
            background: rgba(39,174,96,0.12);
            border: 1px solid rgba(39,174,96,0.3);
            color: #2ECC71;
        }
        .alert ul { list-style: none; }

        /* Form */
        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #E0E0E0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: #37474F;
            pointer-events: none;
            transition: color 0.2s;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            height: 38px;
            padding: 0 12px 0 34px;
            background: #000000;
            border: 1px solid #37474F;
            border-radius: 4px;
            font-size: 13px;
            color: #E0E0E0;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
        }
        input[type="email"]::placeholder,
        input[type="password"]::placeholder { color: #37474F; }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #D4AF37;
        }
        input[type="email"]:focus + i,
        input[type="password"]:focus + i { color: #D4AF37; }

        /* Focus icon fix — icon is before input in DOM so use sibling trick via wrapper */
        .input-wrap:focus-within i { color: #D4AF37; }

        input.is-invalid { border-color: #E74C3C; }

        .invalid-feedback {
            font-size: 11px;
            color: #E74C3C;
            margin-top: 4px;
        }

        /* Remember me */
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .remember input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: #D4AF37;
            cursor: pointer;
        }
        .remember label {
            font-size: 12px;
            font-weight: 400;
            color: #37474F;
            text-transform: none;
            letter-spacing: 0;
            margin: 0;
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            height: 40px;
            background: #D4AF37;
            color: #000000;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: filter 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover { filter: brightness(1.1); }
        .btn-login:disabled { opacity: 0.65; cursor: not-allowed; filter: none; }

        /* Footer */
        .card-footer {
            padding: 12px 32px;
            border-top: 1px solid #37474F;
            text-align: center;
            font-size: 11px;
            color: #37474F;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-body">

            {{-- Brand --}}
            <div class="brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1>{{ config('app.name', 'Shopenhood') }}</h1>
                <p>Admin Control Panel</p>
            </div>

            <div class="divider"></div>

            {{-- Alerts --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation" style="flex-shrink:0;margin-top:1px;"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="admin@example.com"
                               required
                               autofocus
                               class="@error('email') is-invalid @enderror">
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               class="@error('password') is-invalid @enderror">
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket" style="font-size:12px;"></i>
                    Sign In to Admin
                </button>
            </form>

        </div>

        <div class="card-footer">
            Restricted access &mdash; authorised personnel only
        </div>
    </div>

    {{-- Double-submit prevention --}}
    <script>
    (function () {
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.hasAttribute('data-submitting')) { e.preventDefault(); return; }
            form.setAttribute('data-submitting', 'true');
            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            buttons.forEach(function (btn) {
                btn.disabled = true;
                if (btn.tagName === 'BUTTON') {
                    btn.setAttribute('data-original', btn.innerHTML);
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size:12px;"></i> Signing in…';
                }
            });
            setTimeout(function () {
                form.removeAttribute('data-submitting');
                buttons.forEach(function (btn) {
                    btn.disabled = false;
                    if (btn.tagName === 'BUTTON' && btn.hasAttribute('data-original')) {
                        btn.innerHTML = btn.getAttribute('data-original');
                    }
                });
            }, 10000);
        });
    })();
    </script>

</body>
</html>
