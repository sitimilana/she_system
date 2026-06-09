<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Hari Libur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR STRUKTUR */
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 1045; transition: transform 0.3s ease-in-out; }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px; }
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; display: flex; align-items: center; text-decoration: none; }
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600; color: #fff; }
        
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <button class="btn text-white position-absolute top-0 end-0 mt-3 me-2 d-md-none fs-4" id="closeSidebarBtn">
        <i class="bi bi-x-lg"></i>
    </button>
    <div class="logo"><img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo"></div>
    
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.cuti') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Manajemen Cuti</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.reward') }}" class="nav-link"><i class="bi bi-gift"></i> Reward & Recognition</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link"><i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link"><i class="bi bi-geo-alt"></i> Pengaturan Lokasi</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.hari_libur') }}" class="nav-link active"><i class="bi bi-calendar-x"></i> Hari Libur</a></li>
        <li class="nav-item mt-4"><a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Hari Libur</h5>
        <button class="btn btn-light border" id="openSidebarBtn"><i class="bi bi-list fs-4"></i></button>
    </div>

    <h2 class="mb-4 fw-bold d-none d-md-block">Daftar Hari Libur Nasional & Internal</h2>

    <div class="card card-custom p-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary me-2"></i> Data Hari Libur</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="10%">No</th>
                        <th width="40%">Tanggal</th>
                        <th width="50%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hariLibur as $index => $libur)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}</strong></td>
                            <td>{{ $libur->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data hari libur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Logika Sidebar tetap sama
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const overlay = document.getElementById('sidebarOverlay');
        function openSidebar() { sidebar.classList.add('show-mobile'); overlay.classList.add('show'); }
        function closeSidebar() { sidebar.classList.remove('show-mobile'); overlay.classList.remove('show'); }
        if(openBtn) openBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>

@include('auth.logout')
</body>
</html>