<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333; 
        }

        /* SIDEBAR STRUKTUR BARU */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; /* Dinaikkan agar di atas elemen lain saat di HP */
            transition: transform 0.3s ease-in-out; /* Tambahan transisi mulus */
        }
        .sidebar .logo { 
            width: 140px; 
            display: block; 
            margin: 0 auto; 
            margin-top: 20px;
        }
        .sidebar .logo img { 
            width: 100px; 
        }

        /* LINK NAVIGASI BARU */
        .sidebar .nav-link { 
            color: #fff; 
            font-size: 16px; 
            padding: 12px 25px; 
            margin: 4px 15px; 
            transition: 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .sidebar .nav-link i { 
            margin-right: 12px; 
            font-size: 1.1rem; 
        }

        /* HOVER & ACTIVE STATE BARU */
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            background-color: rgba(255,255,255,0.2); 
            border-radius: 8px; 
            font-weight: 600;
            color: #fff;
        }

        /* LOGOUT STYLE BARU */
        .sidebar .nav-link.text-white-50:hover {
            color: #fff !important;
        }

        /* CONTENT LAMA (Ditambah transisi) */
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #8f9fc4; border-radius: 15px; }
        .card { border-radius: 15px; border: none; }
        .metric-value { font-size: 2.5rem; font-weight: bold; color: #2c3e50; }
        .trend-up { color: #27ae60; font-size: 0.9rem; font-weight: bold;}
        .employee-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #ddd;}

        /* --- TAMBAHAN UNTUK MOBILE RESPONSIVE --- */
        .sidebar-overlay {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); /* Sembunyikan sidebar ke kiri */
            }
            .sidebar.show-mobile {
                transform: translateX(0); /* Tampilkan saat class aktif */
            }
            .content {
                margin-left: 0; /* Hapus margin kiri di HP */
                padding: 15px; /* Perkecil padding di HP */
            }
            .sidebar-overlay.show {
                display: block; /* Tampilkan bayangan hitam */
            }
            /* Perkecil sedikit ukuran font agar pas di HP */
            .metric-value { font-size: 2rem; }
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
        <li class="nav-item">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link {{ Request::is('pimpinan') || Request::is('pimpinan/dashboard*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.gaji') }}" class="nav-link {{ Request::is('pimpinan/gaji*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Manajemen Gaji
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.cuti') }}" class="nav-link {{ Request::is('pimpinan/cuti*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> Manajemen Cuti
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.reward') }}" class="nav-link {{ Request::is('pimpinan/reward*') ? 'active' : '' }}">
                <i class="bi bi-gift"></i> Reward & Recognition
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link {{ Request::is('pimpinan/karyawan-pending*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link {{ Request::is('pimpinan/pengaturan-lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Pengaturan Lokasi
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.hari_libur') }}" class="nav-link {{ Request::is('pimpinan/hari-libur*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Hari Libur
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm">
        <h4 class="fw-bold m-0" style="color: #2c3e50;">Dashboard</h4>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <h2 class="mb-4 fw-bold d-none d-md-block">Dashboard Strategis Pimpinan</h2>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 15px; border-left: 5px solid #0d6efd !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">TOTAL KARYAWAN</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalKaryawan ?? 0 }} <span class="fs-6 fw-normal text-muted">Aktif</span></h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-2 py-1"><i class="bi bi-calendar-event me-1"></i>{{ $karyawanCutiHariIni ?? 0 }} Cuti Hari Ini</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 15px; border-left: 5px solid #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">PERSETUJUAN KARYAWAN</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $karyawanPending ?? 0 }} <span class="fs-6 fw-normal text-muted">Menunggu</span></h3>
                    </div>
                    <div>
                        <a href="{{ route('pimpinan.karyawan_pending') }}" class="btn btn-warning btn-sm fw-bold shadow-sm">Tinjau</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @php
                $isGajiSelesai = ($belumDigaji == 0);
            @endphp
            <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 15px; border-left: 5px solid {{ $isGajiSelesai ? '#198754' : '#dc3545' }} !important; background-color: {{ $isGajiSelesai ? '#d4edda' : '#fff3cd' }};">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">STATUS GAJI ({{ \Carbon\Carbon::now()->translatedFormat('M') }})</p>
                        @if($isGajiSelesai)
                            <h5 class="fw-bold mb-0 text-success">Selesai Digaji</h5>
                        @else
                            <h5 class="fw-bold mb-0 text-danger">{{ $belumDigaji }} Belum Digaji</h5>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('pimpinan.gaji') }}" class="btn {{ $isGajiSelesai ? 'btn-success' : 'btn-danger' }} btn-sm fw-bold shadow-sm"><i class="bi bi-gear-fill me-1"></i> Kelola</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-7">
            <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0"><i class="bi bi-pie-chart text-primary me-2"></i>Insight Kehadiran</h6>
                    <form action="{{ route('pimpinan.dashboard') }}" method="GET" class="m-0">
                        <select name="filter_kehadiran" class="form-select form-select-sm border-secondary shadow-none bg-light" style="font-size: 0.8rem; border-radius: 6px; cursor: pointer; padding-top: 0.25rem; padding-bottom: 0.25rem;" onchange="this.form.submit()">
                            <option value="hari_ini" {{ $filterKehadiran == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu_ini" {{ $filterKehadiran == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="bulan_ini" {{ $filterKehadiran == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        </select>
                    </form>
                </div>
                <div class="d-flex justify-content-center align-items-center flex-grow-1" style="min-height: 150px; max-height: 180px;">
                    @if($jmlHadir == 0 && $jmlTerlambat == 0 && $jmlAlpha == 0 && $jmlCuti == 0)
                        <span class="text-muted small fst-italic">Belum ada data di periode ini.</span>
                    @else
                        <div style="position: relative; height:150px; width:100%;">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card p-3 shadow-sm h-100 border-0" style="background-color: #2c3e50; color: white; border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold m-0"><i class="bi bi-trophy text-warning me-2"></i>Top Performer ({{ $bulanTopPerformer }})</h6>
                </div>
                <hr class="mt-0 mb-3 border-secondary">

                <div class="d-flex flex-column justify-content-start h-100">
                    @forelse($topKaryawan as $reward)
                        <div class="bg-dark rounded p-2 mb-2 shadow-sm d-flex align-items-center border-start border-3 border-success">
                            <div class="employee-avatar me-2 d-flex justify-content-center align-items-center text-dark fw-bold bg-warning" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ substr($reward->karyawan->nama ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <strong class="d-block" style="font-size: 0.85rem; line-height: 1.2;">{{ $reward->karyawan->nama ?? 'Data Terhapus' }}</strong>
                                <span class="text-white" style="font-size: 0.7rem;"><i class="bi bi-award"></i> {{ $reward->keterangan ?? 'Reward' }}</span>
                            </div>
                            <div class="text-end ps-2">
                                <h6 class="text-success m-0 fw-bold">{{ $reward->penilaian->total_skor ?? 0 }}</h6>
                                <small class="text-light" style="font-size: 0.65rem;">Skor</small>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-secondary text-center mb-0 py-2" style="font-size: 0.8rem;">Belum ada top performer bulan ini maupun bulan lalu.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-calendar2-week text-warning me-2"></i>Pengajuan Cuti Terbaru</h5>
                    <a href="{{ route('pimpinan.cuti') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                </div>
                <hr class="mt-0 mb-4 border-secondary">

                <div class="row g-3">
                    @forelse($cutiTerbaru as $cuti)
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3 h-100 shadow-sm border-start border-4 border-warning">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark">{{ $cuti->karyawan->nama ?? 'Nama Tidak Ditemukan' }}</strong>
                                    <span class="badge bg-{{ strtolower($cuti->status) == 'pending' || strtolower($cuti->status) == 'pending_pimpinan' ? 'warning text-dark' : (strtolower($cuti->status) == 'approved' ? 'success' : 'danger') }}">
                                        {{ str_replace('_', ' ', $cuti->status) }}
                                    </span>
                                </div>
                                <p class="mb-0 mt-2 text-muted" style="font-size: 0.85rem;">
                                    <i class="bi bi-clock"></i> {{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}
                                    <br><small class="text-primary mt-1 d-block">Diajukan: {{ \Carbon\Carbon::parse($cuti->created_at)->diffForHumans() }}</small>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light text-center m-0">Belum ada pengajuan cuti baru.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- LOGIKA MENU BURGER HP ---
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
        // -----------------------------

        // --- LOGIKA CHART (Tidak Diubah) ---
        var canvas = document.getElementById('kehadiranChart');
        if (canvas) {
            var ctx = canvas.getContext('2d');
            var kehadiranChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Alpha', 'Cuti/Izin/Sakit'],
                    datasets: [{
                        data: [{{ $jmlHadir }}, {{ $jmlTerlambat }}, {{ $jmlAlpha }}, {{ $jmlCuti }}], 
                        backgroundColor: [
                            '#a8e6cf', '#ffd3b6', '#ffaaa5', '#a2d5f2'  
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 10,
                                font: { size: 11, family: 'Inter' }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@include('auth.logout')

</body>
</html>