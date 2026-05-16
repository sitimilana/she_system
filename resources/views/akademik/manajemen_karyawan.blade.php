<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Karyawan - Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            background: #f4f7f6;
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        
        /* SIDEBAR SAMA DENGAN BERANDA */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #8f9fc4;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1045;
            transition: transform 0.3s ease-in-out;
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

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            border-radius: 8px;
            font-weight: 600;
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar .nav-link.text-white-50:hover {
            color: #fff !important;
        }

        /* CONTENT */
        .content {
            margin-left: 250px;
            padding: 40px;
            transition: margin-left 0.3s ease;
        }

        .card-custom {
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        
        /* TABLE */
        .table-custom th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-custom td {
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        /* PAGINATION */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }

        .pagination .page-link {
            padding: 0.35rem 0.5rem !important;
            font-size: 0.85rem !important;
            line-height: 1.4 !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 6px !important;
            color: #475569;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            border-color: #8f9fc4 !important;
        }

        .pagination .active .page-link {
            background-color: #8f9fc4 !important;
            border-color: #8f9fc4 !important;
            color: white !important;
        }

        /* MOBILE */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show-mobile {
                transform: translateX(0);
            }

            .content {
                margin-left: 0 !important;
                padding: 15px !important;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
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
            <a href="{{ route('akademik.beranda') }}" class="nav-link">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akademik.absensi') }}" class="nav-link">
                <i class="bi bi-journal-check"></i> Riwayat Absensi
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akademik.cuti') }}" class="nav-link">
                <i class="bi bi-calendar-range"></i> Riwayat Cuti
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akademik.karyawan') }}" class="nav-link active">
                <i class="bi bi-people"></i> Manajemen Karyawan
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
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Data Karyawan</h5>

        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="mb-4 d-none d-md-block">
        <h2 class="fw-bold m-0" style="color: #1e293b;">Data Karyawan</h2>
        <p class="text-muted m-0">
            Pemantauan daftar staf dan karyawan (Akses Read-Only).
        </p>
    </div>

    <div class="card card-custom p-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div class="input-group shadow-sm w-100" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>

                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama atau jabatan...">
            </div>
            
            <div class="d-flex gap-2 w-100 w-md-auto justify-content-md-end">
                <button class="btn btn-outline-secondary bg-white shadow-sm flex-grow-1 flex-md-grow-0">
                    <i class="bi bi-funnel"></i> Filter
                </button>

                <button class="btn btn-outline-secondary bg-white shadow-sm flex-grow-1 flex-md-grow-0">
                    <i class="bi bi-printer me-2"></i>Cetak
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-custom m-0 text-nowrap">

                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama</th>
                        <th width="15%">Divisi</th>
                        <th width="15%">Kontak</th>
                        <th width="20%">Alamat</th>
                        <th width="10%" class="text-center">Sisa Cuti</th>
                        <th width="15%" class="text-center">Status Kerja</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($dataKaryawan as $index => $karyawan)
                    <tr>

                        <td class="text-center">
                            {{ $dataKaryawan->firstItem() + $index }}
                        </td>

                        <td class="fw-bold text-dark">
                            {{ $karyawan->karyawan->nama ?? $karyawan->nama_lengkap }}
                        </td>

                        <td class="text-capitalize">
                            {{ $karyawan->karyawan->divisi ?? '-' }}
                        </td>

                        <td>
                            {{ $karyawan->karyawan->no_hp ?? '-' }}
                        </td>

                        <td class="text-truncate" style="max-width: 150px;"
                            title="{{ $karyawan->karyawan->alamat ?? '-' }}">

                            {{ $karyawan->karyawan->alamat ?? '-' }}
                        </td>

                        <td class="text-center">
                            @php
                                $sisaCuti = $karyawan->karyawan->sisa_cuti ?? 0;
                            @endphp

                            <span class="badge {{ $sisaCuti > 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ $sisaCuti }} Hari
                            </span>
                        </td>

                        <td class="text-center">
                            @php
                                $status = $karyawan->karyawan->status_karyawan ?? 'Belum Lengkap';
                            @endphp

                            <span class="badge bg-{{ strtolower($status) == 'aktif' ? 'success' : 'warning text-dark' }} px-3 py-2 rounded-pill">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                    </tr>
                    @empty

                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Tidak ada data karyawan.
                        </td>
                    </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        @if($dataKaryawan->hasPages())
        <div class="d-flex flex-column align-items-center mt-4 gap-2 text-center">

            <div class="text-muted small">
                Menampilkan
                <strong>{{ $dataKaryawan->firstItem() }}</strong>
                s/d
                <strong>{{ $dataKaryawan->lastItem() }}</strong>
                dari
                <strong>{{ $dataKaryawan->total() }}</strong>
                data
            </div>

            <div class="d-flex justify-content-center">
                {{ $dataKaryawan->links('pagination::bootstrap-5') }}
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