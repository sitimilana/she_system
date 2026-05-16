<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Hari Libur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
        
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

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
                transform: translateX(0); /* Tampilkan saat menu di-klik */
            }
            .content {
                margin-left: 0; /* Hapus margin kiri di HP */
                padding: 15px; /* Perkecil padding di HP */
            }
            .sidebar-overlay.show {
                display: block; /* Tampilkan overlay gelap */
            }
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
    
    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Hari Libur</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <h2 class="mb-4 fw-bold d-none d-md-block">Manajemen Hari Libur Nasional & Internal</h2>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terdapat Kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i> Tambah Hari Libur</h5>
                <hr>
                <form action="{{ route('pimpinan.hari_libur.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Libur</label>
                        <input type="date" name="tanggal" class="form-control bg-light" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Keterangan / Nama Libur</label>
                        <input type="text" name="keterangan" class="form-control bg-light" placeholder="Contoh: Idul Fitri" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm"><i class="bi bi-save me-1"></i> Simpan Hari Libur</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary me-2"></i> Daftar Hari Libur Terdaftar</h5>
                <hr>
                <div class="table-responsive border rounded shadow-sm">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="10%">No</th>
                                <th width="30%">Tanggal</th>
                                <th width="45%">Keterangan</th>
                                <th class="text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hariLibur as $index => $libur)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}</strong></td>
                                    <td>{{ $libur->keterangan }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('pimpinan.hari_libur.destroy', $libur->id_libur) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada hari libur yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
    });
</script>

@include('auth.logout')
</body>
</html>