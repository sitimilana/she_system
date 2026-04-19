<!DOCTYPE html>
<html>
<head>
    <title>Penilaian Kinerja - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR (Konsisten) */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT & FORM */
        .content { margin-left: 250px; padding: 40px; }
        .form-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 40px;
            max-width: 600px; /* Form dibuat tidak terlalu lebar agar rapi */
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.95rem; }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e0;
            padding: 10px 15px;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff; border-color: #8f9fc4; box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15);
        }
        
        /* TOTAL SKOR STYLING */
        .score-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .input-total {
            background: transparent;
            border: none;
            font-size: 3rem;
            font-weight: 800;
            color: #3b82f6;
            text-align: center;
            width: 100%;
            outline: none;
            pointer-events: none; /* Mencegah user mengetik manual */
        }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item mt-4">
            <a href="{{ route('logout') }}" class="nav-link text-white-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="mb-4">
        <h2 class="fw-bold m-0" style="color: #1e293b;">Penilaian Kinerja</h2>
        <p class="text-muted m-0">Evaluasi kinerja bulanan karyawan di departemen Anda.</p>
    </div>

    <div class="form-card">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kabag.penilaian.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="form-label text-primary"><i class="bi bi-person-badge me-2"></i>Pilih Karyawan</label>
                <select class="form-select form-select-lg" name="id_karyawan" required>
                    <option value="" disabled selected>-- Pilih Nama Karyawan --</option>
                    @foreach($karyawan as $k)
                        <option value="{{ $k->id_karyawan }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="mb-4 border-secondary">

            <div class="alert alert-light border mb-4 text-muted small">
                <i class="bi bi-info-circle me-1"></i> Masukkan nilai menggunakan <strong>Skala Likert 1 sampai 5</strong>.
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-sm-7"><label class="form-label m-0">Disiplin</label></div>
                <div class="col-sm-5">
                    <select class="form-select calc-score" name="disiplin" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div class="row align-items-center mb-3">
                <div class="col-sm-7"><label class="form-label m-0">Produktivitas</label></div>
                <div class="col-sm-5">
                    <select class="form-select calc-score" name="produktivitas" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-sm-7"><label class="form-label m-0">Tanggung Jawab</label></div>
                <div class="col-sm-5">
                    <select class="form-select calc-score" name="tanggung_jawab" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-sm-7"><label class="form-label m-0">Sikap Kerja</label></div>
                <div class="col-sm-5">
                    <select class="form-select calc-score" name="sikap_kerja" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row align-items-center mb-4">
                <div class="col-sm-7"><label class="form-label m-0">Loyalitas</label></div>
                <div class="col-sm-5">
                    <select class="form-select calc-score" name="loyalitas" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="score-box mt-4 mb-4">
                <p class="text-muted fw-bold mb-0">TOTAL SKOR TERTIMBANG</p>
                <input type="text" class="input-total" id="total_skor" name="total_skor" value="0" readonly>
            </div>

            <div class="mb-4">
                <label class="form-label">Catatan Evaluasi</label>
                <textarea class="form-control" name="catatan_evaluasi" rows="4" placeholder="Tambahkan catatan evaluasi (opsional)"></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Simpan Penilaian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const inputs = document.querySelectorAll('.calc-score');
    const totalEl = document.getElementById('total_skor');
    const bobot = {
        disiplin: 0.20,
        produktivitas: 0.30,
        tanggung_jawab: 0.20,
        sikap_kerja: 0.15,
        loyalitas: 0.15
    };

    function hitungSkorTertimbang() {
        let total = 0;

        inputs.forEach(input => {
            let val = parseFloat(input.value);
            total += (val || 0) * (bobot[input.name] || 0);
        });

        const totalRounded = Math.round(total);
        totalEl.value = totalRounded;

        if(totalRounded >= 4) {
            totalEl.style.color = '#10b981';
        } else if (totalRounded < 3) {
            totalEl.style.color = '#ef4444';
        } else {
            totalEl.style.color = '#3b82f6';
        }
    }

    inputs.forEach(input => input.addEventListener('change', hitungSkorTertimbang));
    hitungSkorTertimbang();
</script>

</body>
</html>
