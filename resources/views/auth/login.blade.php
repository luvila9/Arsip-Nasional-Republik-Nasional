<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Bukti Dukung ANRI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-light: #f1f5f9;
            --text-main: #334155;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body {
            background-color: var(--bg-light);
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--text-main);
        }

        div:where(.swal2-container) {
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo-container img { width: 80px; margin-bottom: 15px; }
        .app-title { font-size: 1.4rem; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .app-subtitle { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 30px; }

        .alert-error { background-color: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; text-align: left; }
        .alert-error ul { margin-left: 20px; margin-top: 5px; }

        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-toggle { position: absolute; right: 15px; cursor: pointer; color: var(--text-muted); transition: color 0.2s; }
        .password-toggle:hover { color: var(--primary); }

        .form-control { width: 100%; padding: 12px 15px; font-size: 0.95rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

        .btn-login { width: 100%; padding: 12px; background-color: var(--primary); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-login:hover { background-color: var(--primary-hover); transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('images/logo 1.png') }}" alt="Logo ANRI">
        </div>        
        <h1 class="app-title">PORTAL BUKTI DUKUNG</h1>
        <p class="app-subtitle">Arsip Nasional Republik Indonesia</p>
        @if ($errors->any())
            <div class="alert-error">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> Login Gagal!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Contoh: admin@anri.go.id" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" style="padding-right: 40px;" required>
                    <i class="fa-solid fa-eye password-toggle" id="togglePassword" title="Tampilkan Sandi"></i>
                </div>
            </div>
            <div class="form-group" style="display: flex; justify-content: center; margin-bottom: 10px;">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
            </button>
        </form>        
        <p style="margin-top: 25px; font-size: 0.75rem; color: #94a3b8;">
            &copy; {{ date('Y') }} Arsip Nasional RI. Hak Cipta Dilindungi.
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            var recaptchaResponse = grecaptcha.getResponse();
            if (recaptchaResponse.length === 0) {
                event.preventDefault(); 
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Verifikasi Diperlukan',
                    text: "Harap centang kotak CAPTCHA (I'm not a robot) terlebih dahulu sebelum masuk ke sistem.",
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Tutup'
                });
            }
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            
            passwordInput.setAttribute('type', type);            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>