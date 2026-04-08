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
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
        }
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        
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
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link active"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
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

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="form-card">
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

                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-calendar-month me-2"></i>Bulan & Tahun Penilaian</label>
                        <input type="month" class="form-control" name="periode" required value="{{ date('Y-m') }}">
                    </div>

                    <hr class="mb-4 border-secondary">

                    <div class="alert alert-light border mb-4 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Masukkan nilai dari skala <strong>0 hingga 100</strong>.
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-sm-7"><label class="form-label m-0">Disiplin</label></div>
                        <div class="col-sm-5"><input type="number" class="form-control calc-score" name="disiplin" min="0" max="100" value="0" required></div>
                    </div>
                    
                    <div class="row align-items-center mb-3">
                        <div class="col-sm-7"><label class="form-label m-0">Produktivitas</label></div>
                        <div class="col-sm-5"><input type="number" class="form-control calc-score" name="produktivitas" min="0" max="100" value="0" required></div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-sm-7"><label class="form-label m-0">Tanggung Jawab</label></div>
                        <div class="col-sm-5"><input type="number" class="form-control calc-score" name="tanggung_jawab" min="0" max="100" value="0" required></div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-sm-7"><label class="form-label m-0">Sikap Kerja</label></div>
                        <div class="col-sm-5"><input type="number" class="form-control calc-score" name="sikap_kerja" min="0" max="100" value="0" required></div>
                    </div>

                    <div class="row align-items-center mb-4">
                        <div class="col-sm-7"><label class="form-label m-0">Loyalitas</label></div>
                        <div class="col-sm-5"><input type="number" class="form-control calc-score" name="loyalitas" min="0" max="100" value="0" required></div>
                    </div>

                    <div class="score-box mt-4 mb-4">
                        <p class="text-muted fw-bold mb-0">TOTAL SKOR RATA-RATA</p>
                        <input type="text" class="input-total" id="total_skor" name="total_skor" value="0" readonly>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: #1e293b;">Riwayat Penilaian</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover table-custom m-0">
                        <thead>
                            <tr>
                                <th width="25%">Bulan</th>
                                <th width="35%">Karyawan</th>
                                <th width="20%" class="text-center">Total Skor</th>
                                <th width="20%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPenilaian as $rp)
                            <tr>
                                <td>
                                    @php
                                        setlocale(LC_TIME, 'id_ID');
                                        $monthName = \Carbon\Carbon::create()->month($rp->bulan)->translatedFormat('F');
                                    @endphp
                                    <span class="fw-medium">{{ $monthName }} {{ $rp->tahun }}</span>
                                </td>
                                <td>{{ $rp->karyawan->nama ?? 'Tidak Ditemukan' }}</td>
                                <td class="text-center fw-bold text-primary">{{ $rp->total_skor }}</td>
                                <td class="text-center">
                                    @if($rp->total_skor >= 85)
                                        <span class="badge bg-success">Sangat Baik</span>
                                    @elseif($rp->total_skor >= 70)
                                        <span class="badge bg-primary">Baik</span>
                                    @elseif($rp->total_skor >= 60)
                                        <span class="badge bg-warning text-dark">Cukup</span>
                                    @else
                                        <span class="badge bg-danger">Kurang</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat penilaian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const inputs = document.querySelectorAll('.calc-score');
    const totalEl = document.getElementById('total_skor');

    function hitungRataRata() {
        let total = 0;
        let count = 0;

        inputs.forEach(input => {
            let val = parseFloat(input.value);
            // Cegah angka lebih dari 100
            if(val > 100) { val = 100; input.value = 100; }
            if(val < 0) { val = 0; input.value = 0; }
            
            total += (val || 0);
            count++;
        });

        // Hitung rata-rata dan bulatkan 1 angka di belakang koma (jika ada)
        let rataRata = count > 0 ? (total / count) : 0;
        
        // Cek apakah angkanya bulat atau desimal untuk format tampilan
        totalEl.value = Number.isInteger(rataRata) ? rataRata : rataRata.toFixed(1);
        
        // Ubah warna text jika nilai di atas/di bawah standar (opsional UX)
        if(rataRata >= 85) {
            totalEl.style.color = '#10b981'; // Hijau jika bagus
        } else if (rataRata < 60) {
            totalEl.style.color = '#ef4444'; // Merah jika buruk
        } else {
            totalEl.style.color = '#3b82f6'; // Biru standar
        }
    }

    // Pasang listener di setiap input
    inputs.forEach(input => input.addEventListener('input', hitungRataRata));
</script>

</body>
</html>