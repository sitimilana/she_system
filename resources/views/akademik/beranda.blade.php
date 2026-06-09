<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR STRUKTUR BARU */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; /* Diperbesar agar di atas elemen lain saat di mobile */
            transition: transform 0.3s ease-in-out; /* Animasi mulus */
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; display: flex; align-items: center; text-decoration: none;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600; color: #fff;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .sidebar .nav-link.text-white-50:hover { color: #fff !important; }
        
        /* CONTENT LAMA (Ditambah transisi) */
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .metric-value { font-size: 2.5rem; font-weight: 800; color: #1e293b; }

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
        <li class="nav-item"><a href="{{ route('akademik.beranda') }}" class="nav-link active"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{route('akademik.absensi') }}" class="nav-link"><i class="bi bi-journal-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="{{ route('akademik.cuti') }}" class="nav-link"><i class="bi bi-calendar-range"></i> Riwayat Cuti</a></li>
        <li class="nav-item"><a href="{{ route('akademik.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Manajemen Karyawan</a></li>
        <li class="nav-item">
            <a href="{{ route('akademik.hari_libur') }}" 
            class="nav-link {{ Request::routeIs('akademik.hari_libur') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Kelola Hari Libur
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

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Dashboard</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <h2 class="fw-bold mb-4 d-none d-md-block" style="color: #1e293b;">Dashboard Akademik</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="metric-value">{{ $totalKaryawan }}</div>
                <p class="text-muted m-0 fw-medium">Jumlah Seluruh Karyawan</p>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="card card-custom p-4 h-100 text-center" style="border-bottom: 4px solid #3b82f6;">
                <div class="metric-value text-primary">{{ $hadir }}</div>
                <p class="text-muted m-0 fw-medium">Jumlah Karyawan Hadir</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3">Rekap Cuti Terkini</h5>
                <hr class="mt-0">
                
                @forelse($rekapCuti as $cuti)
                    @if($loop->iteration <= 3)
                        <div class="bg-light rounded p-3 mb-3 border">
                            <div class="mb-2">
                                <strong class="fs-5">{{ $cuti->nama }}</strong>
                            </div>
                            <p class="m-0 text-muted" style="font-size: 0.9rem;">
                                Tanggal: {{ $cuti->tgl_mulai }} s/d {{ $cuti->tgl_selesai }} <br>
                                Status: <span class="badge bg-{{ $cuti->status == 'Pending' ? 'warning text-dark' : 'success' }}">{{ $cuti->status }}</span>
                            </p>
                        </div>
                    @endif
                @empty
                    <div class="alert alert-secondary">Tidak ada data cuti.</div>
                @endforelse
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Grafik Rekap Absensi</h5>
                        <div class="text-muted small">Periode: {{ $selectedPeriod['label'] }}</div>
                    </div>
                    <form method="GET" action="{{ route('akademik.beranda') }}" class="m-0">
                        <select name="periode_absensi" class="form-select form-select-sm" style="min-width: 160px;" onchange="this.form.submit()">
                            <option value="hari" {{ $chartPeriod === 'hari' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu" {{ $chartPeriod === 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="bulan" {{ $chartPeriod === 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                        </select>
                    </form>
                </div>
                <hr class="mt-0 mb-4">
                <div style="position: relative; height: 180px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="absensiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

        // --- LOGIKA CHART PIE ---
        const ctx = document.getElementById('absensiChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut', 
                data: {
                    labels: ['Hadir', 'Tidak Hadir', 'Sakit', 'Izin', 'Cuti'],
                    datasets: [{
                        data: [
                            {{ $rekapAbsensi['Hadir'] }},
                            {{ $rekapAbsensi['Tidak Hadir'] }},
                            {{ $rekapAbsensi['Sakit'] }},
                            {{ $rekapAbsensi['Izin'] }},
                            {{ $rekapAbsensi['Cuti'] }}
                        ],
                        backgroundColor: [
                            '#a8e6cf', // Hijau Pastel (Hadir)
                            '#ffaaa5', // Merah Muda (Tidak Hadir)
                            '#a2d5f2', // Biru Muda (Sakit)
                            '#e0c3fc', // Ungu Muda (Izin)
                            '#ffd3b6'  // Orange/Kuning Pastel (Cuti)
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom', 
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