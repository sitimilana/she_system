<!DOCTYPE html>
<html>
<head>
    <title>Reward & Recognition - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar { 
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4; 
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; /* DITAMBAHKAN: Diperbesar agar di atas elemen lain saat di mobile */
            transition: transform 0.3s ease-in-out; /* DITAMBAHKAN: Animasi mulus */
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT (DITAMBAHKAN transisi) */
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        
        /* TOP PERFORMER CARD */
        .performer-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white; border-radius: 16px; padding: 20px; position: relative; overflow: hidden;
        }
        .performer-card::after {
            content: "\F5A2"; font-family: "bootstrap-icons"; position: absolute;
            right: -10px; bottom: -20px; font-size: 8rem; color: rgba(255,255,255,0.05);
        }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        
        /* Pagination */
        .pagination { margin-bottom: 0; }

        /* --- TAMBAHAN UNTUK MOBILE RESPONSIVE --- */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 15px !important; }
            .sidebar-overlay.show { display: block; }

            /* Menyesuaikan lebar form filter agar tumpuk di HP */
            .filter-form .col-auto { width: 100%; margin-bottom: 10px; }
            .filter-form .form-select, .filter-form .btn { width: 100%; }
            .filter-form .d-flex { flex-direction: column; gap: 10px; }
        }
        /* --------------------------------------- */
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <button class="btn text-white position-absolute top-0 end-0 mt-3 me-2 d-md-none fs-4" id="closeSidebarBtn">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.cuti') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Manajemen Cuti</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.reward') }}" class="nav-link active"><i class="bi bi-gift"></i> Reward & Recognition</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link"><i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link"><i class="bi bi-geo-alt"></i> Pengaturan Lokasi</a></li>
        <li class="nav-item mt-4"><a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Reward & Recognition</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Reward & Recognition</h2>
            <p class="text-muted m-0">Evaluasi dan berikan penghargaan untuk karyawan berprestasi.</p>
        </div>
        <button class="btn btn-outline-danger fw-bold shadow-sm"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF</button>
    </div>

    <div class="d-md-none mb-4">
        <button class="btn btn-outline-danger fw-bold shadow-sm w-100"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF</button>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 p-3 bg-light">
        <form method="GET" action="{{ route('pimpinan.reward') }}" class="row g-3 align-items-center filter-form">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama karyawan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <select class="form-select" name="bulan">
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select class="form-select" name="tahun">
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-filter"></i> Tampilkan</button>
                @if(request('search'))
                    <a href="{{ route('pimpinan.reward') }}?bulan={{ now()->month }}&tahun={{ now()->year }}" class="btn btn-outline-secondary text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-star-fill text-warning me-2"></i>Kandidat Top Performer Bulan {{ $bulanList[$bulan] }} {{ $tahun }}</h5>
    <div class="row mb-4">
        @forelse($topKandidat as $kandidat)
        <div class="col-md-12 col-lg-6">
            <div class="performer-card shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-end mt-4 flex-wrap gap-3">
                    <div>
                        <small class="d-block text-white-50">Skor Kinerja Tertinggi</small>
                        <h3 class="m-0 text-warning fw-bold">{{ $kandidat->total_skor }}/100</h3>
                    </div>
                    
                    <form action="{{ route('pimpinan.reward.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_karyawan" value="{{ $kandidat->id_karyawan }}">
                        <input type="hidden" name="id_penilaian" value="{{ $kandidat->id_penilaian }}">
                        <input type="hidden" name="keterangan" value="Karyawan Terbaik Periode {{ $bulan }} / {{ $tahun }}">
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="bi bi-trophy-fill me-1"></i> Berikan Reward
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        @endforelse
    </div>

    <div class="card card-custom p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-list-ol me-2 text-primary"></i>Daftar Peringkat Karyawan</h5>
        <div class="table-responsive">
            <table class="table table-hover table-custom m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Peringkat</th>
                        <th width="25%">Nama Karyawan</th>
                        <th width="20%">Divisi / Jabatan</th>
                        <th width="20%" class="text-center">Periode Penilaian</th>
                        <th width="15%" class="text-center">Skor Kinerja</th>
                        <th width="15%" class="text-center">Status Reward</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarReward as $index => $reward)
                    <tr>
                        <td class="text-center fw-bold">{{ $daftarReward->firstItem() + $index }}</td>
                        <td class="fw-bold">{{ $reward->karyawan->nama ?? '-' }}</td>
                        <td>{{ $reward->karyawan->divisi ?? '-' }}</td>
                        <td class="text-center">{{ $bulanList[$reward->bulan] }} {{ $reward->tahun }}</td>
                        <td class="text-center fw-bold {{ $reward->total_skor >= 90 ? 'text-success' : 'text-dark' }}">{{ $reward->total_skor }}</td>
                        <td class="text-center">
                            @if(($daftarReward->firstItem() + $index) == 1)
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-trophy-fill me-1"></i> Penerima Reward</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data karyawan yang dinilai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarReward->hasPages())
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 border-top pt-3 gap-2">
            <div class="text-muted small">
                Menampilkan <strong>{{ $daftarReward->firstItem() }}</strong> s/d <strong>{{ $daftarReward->lastItem() }}</strong> dari <strong>{{ $daftarReward->total() }}</strong> data
            </div>
            <div>
                {{ $daftarReward->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('show-mobile');
            overlay.classList.add('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('show');
        }

        if(openBtn) openBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>

@include('auth.logout')
</body>
</html>