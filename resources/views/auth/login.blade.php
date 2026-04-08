<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - HRIS SHE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            /* Background gradien modern yang halus */
            background: linear-gradient(135deg, #f4f7f6 0%, #8fa1c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px; /* Sedikit lebih lebar agar proporsional */
            padding: 40px;
        }

        .brand-logo {
            width: 130px;
            margin-bottom: 25px;
        }

        /* Styling Input Box agar ada ikon di dalamnya */
        .form-control {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px 15px;
            border-radius: 12px;
            font-size: 0.95rem;
            padding-left: 45px; /* Memberi ruang untuk ikon */
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #8fa1c7;
            box-shadow: 0 0 0 4px rgba(143, 161, 199, 0.15);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            z-index: 10;
        }

        /* Styling Tombol Login */
        .btn-login {
            background-color: #ef4444; /* Warna merah bawaan Anda */
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>

<body>
    <div class="login-card">
        
        <div class="text-center mb-4">
            <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo SHE" class="brand-logo">
            <h4 class="fw-bold" style="color: #1e293b;">Selamat Datang</h4>
            <p class="text-muted small">Silakan masuk menggunakan kredensial Anda.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 small py-2 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> 
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="position-relative mb-3">
                <i class="bi bi-person-fill input-icon"></i>
                <input type="text" 
                       name="username" 
                       class="form-control @error('username') is-invalid @enderror" 
                       placeholder="Username"
                       value="{{ old('username') }}" 
                       required autocomplete="off">
            </div>

            <div class="position-relative mb-4">
                <i class="bi bi-lock-fill input-icon"></i>
                <input type="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Password" 
                       required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label text-muted" for="rememberMe" style="cursor: pointer;">Ingat Saya</label>
                </div>
                <a href="#" class="text-decoration-none" style="color: #8fa1c7; font-weight: 600;">Lupa Sandi?</a>
            </div>

            <button type="submit" class="btn btn-danger btn-login w-100 text-white shadow-sm">
                MASUK SEKARANG
            </button>
        </form>
        
        <div class="text-center mt-4 pt-4 border-top">
            <p class="text-muted small m-0">Sistem HRIS Terintegrasi &copy; {{ date('Y') }}</p>
        </div>

    </div>
</body>
</html>