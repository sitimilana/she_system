<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Absensi - Akademik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            background: #f8f9fa;
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
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .card-custom {
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        
        /* TABLE STYLES */
        .table-custom th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        
        /* PHOTO THUMBNAIL */
        .photo-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: 0.2s;
        }

        .photo-thumb:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* LAT LONG TEXT */
        .coord-text {
            font-size: 0.75rem;
            color: #64748b;
            font-family: monospace;
        }
        
        /* PAGINATION */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            padding: 0.35rem 0.5rem !important;
            font-size: 0.85rem !important;
            line-height: 1.4 !important;
            border: 1px solid #dee2e6 !important;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9 !important;
            border-color: #8f9fc4 !important;
        }

        .pagination .active .page-link {
            background-color: #8f9fc4 !important;
            border-color: #8f9fc4 !important;
        }

        .pagination .disabled .page-link {
            color: #6c757d !important;
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
            
            .filter-btn-group {
                margin-top: 10px;
                width: 100%;
            }

            .filter-btn-group button,
            .filter-btn-group a {
                width: 100%;
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
            <a href="{{ route('akademik.absensi') }}" class="nav-link active">
                <i class="bi bi-journal-check"></i> Riwayat Absensi
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akademik.cuti') }}" class="nav-link">
                <i class="bi bi-calendar-range"></i> Riwayat Cuti
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('akademik.karyawan') }}" class="nav-link">
                <i class="bi bi-people"></i> Manajemen Karyawan
            </a>
        </li>

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
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Riwayat Absensi</h5>

        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="d-none d-md-block mb-4">
        <h3 class="fw-bold m-0" style="color: #1e293b;">Riwayat Absensi</h3>
        <p class="text-muted m-0">Log kehadiran karyawan dari aplikasi mobile.</p>
    </div>

    <div class="card card-custom p-4">

        <form action="{{ route('akademik.absensi') }}" method="GET" class="mb-4 bg-light p-3 rounded-4 border">

            <div class="row g-3 align-items-end">

                <div class="col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-person me-1"></i> Cari Karyawan
                    </label>

                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Ketik nama karyawan..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-month me-1"></i> Bulan & Tahun
                    </label>

                    <input type="month" name="bulan" class="form-control shadow-sm"
                        value="{{ request('bulan') }}">
                </div>

                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-date me-1"></i> Tanggal
                    </label>

                    <input type="date" name="tanggal" class="form-control shadow-sm"
                        value="{{ request('tanggal') }}">
                </div>
                
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-check-circle me-1"></i> Status Kehadiran
                    </label>

                    <select name="status" class="form-select shadow-sm">
                        <option value="">Semua Status</option>

                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>
                            Hadir
                        </option>

                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>
                            Terlambat
                        </option>

                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>
                            Izin
                        </option>

                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>
                            Sakit
                        </option>

                        <option value="alfa" {{ request('status') == 'alfa' ? 'selected' : '' }}>
                            Alfa
                        </option>
                    </select>
                </div>
                
                <div class="col-lg-3">

                    <div class="d-flex gap-2 filter-btn-group h-100 align-items-end">

                        <button type="submit" class="btn btn-primary shadow-sm fw-bold">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>

                        <a href="{{ route('akademik.cetak.absensi', request()->query()) }}"
                        target="_blank"
                        class="btn btn-danger shadow-sm fw-bold">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                        </a>

                        @if(request('search') || request('bulan') || request('status'))
                            <a href="{{ route('akademik.absensi') }}"
                            class="btn btn-danger text-white shadow-sm px-3 text-center"
                            title="Reset Semua Filter">

                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        @endif

                    </div>
                </div>

            </div>
        </form>

        <div class="table-responsive">

            <table class="table table-hover table-custom m-0 text-nowrap">

                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th class="text-center">
                            Waktu <br>
                            <small class="text-muted fw-normal">(Masuk - Pulang)</small>
                        </th>

                        <th>
                            Lokasi Masuk <br>
                            <small class="text-muted fw-normal">(Lat, Long)</small>
                        </th>

                        <th>
                            Lokasi Pulang <br>
                            <small class="text-muted fw-normal">(Lat, Long)</small>
                        </th>

                        <th class="text-center">
                            Bukti Foto <br>
                            <small class="text-muted fw-normal">(Masuk & Pulang)</small>
                        </th>

                        <th class="text-center">Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($dataAbsensi as $index => $absen)

                    <tr>

                        <td class="text-center">
                            {{ $dataAbsensi->firstItem() + $index }}
                        </td>

                        <td class="fw-bold text-dark">
                            {{ $absen->karyawan->nama ?? ($absen->karyawan->user->nama_lengkap ?? 'Unknown') }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}
                        </td>
                        
                        <td class="text-center">
                            @if($absen->jam_masuk)

                                <span class="text-success fw-bold">
                                    {{ $absen->jam_masuk }}
                                </span>

                                -

                                <span class="text-danger fw-bold">
                                    {{ $absen->jam_pulang ?? '--:--:--' }}
                                </span>

                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if($absen->latitude_masuk)

                                <a href="https://maps.google.com/?q={{ $absen->latitude_masuk }},{{ $absen->longitude_masuk }}"
                                   target="_blank"
                                   class="text-decoration-none">

                                    <i class="bi bi-geo-alt-fill text-danger"></i>

                                    <span class="coord-text">
                                        {{ substr($absen->latitude_masuk, 0, 7) }},
                                        {{ substr($absen->longitude_masuk, 0, 8) }}
                                    </span>

                                </a>

                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if($absen->latitude_pulang)

                                <a href="https://maps.google.com/?q={{ $absen->latitude_pulang }},{{ $absen->longitude_pulang }}"
                                   target="_blank"
                                   class="text-decoration-none">

                                    <i class="bi bi-geo-alt-fill text-primary"></i>

                                    <span class="coord-text">
                                        {{ substr($absen->latitude_pulang, 0, 7) }},
                                        {{ substr($absen->longitude_pulang, 0, 8) }}
                                    </span>

                                </a>

                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="text-center">

                            @if($absen->foto_masuk)

                                @php
                                    $fotoMasuk = $absen->foto_masuk;
                                    if (preg_match('/^https?:\/\//', $fotoMasuk)) {
                                        $fotoMasukUrl = $fotoMasuk;
                                    } else {
                                        $cleanPath = ltrim(str_replace('storage/', '', $fotoMasuk), '/');
                                        $fotoMasukUrl = asset('storage/' . $cleanPath);
                                    }
                                @endphp

                                <img src="{{ $fotoMasukUrl }}"
                                     alt="In"
                                     class="photo-thumb me-1"
                                     title="Foto Masuk">

                                @if($absen->foto_pulang)

                                    @php
                                        $fotoPulang = $absen->foto_pulang;
                                        if (preg_match('/^https?:\/\//', $fotoPulang)) {
                                            $fotoPulangUrl = $fotoPulang;
                                        } else {
                                            $cleanPath = ltrim(str_replace('storage/', '', $fotoPulang), '/');
                                            $fotoPulangUrl = asset('storage/' . $cleanPath);
                                        }
                                    @endphp

                                    <img src="{{ $fotoPulangUrl }}"
                                         alt="Out"
                                         class="photo-thumb"
                                         title="Foto Pulang">

                                @endif

                            @else
                                <span class="text-muted small">Tidak ada foto</span>
                            @endif

                        </td>

                        <td class="text-center">

                            @php
                                $badgeClass = 'secondary';

                                if($absen->status == 'hadir') {
                                    $badgeClass = 'success';
                                }
                                elseif($absen->status == 'terlambat') {
                                    $badgeClass = 'warning text-dark';
                                }
                                elseif($absen->status == 'alfa') {
                                    $badgeClass = 'danger';
                                }
                                elseif($absen->status == 'izin') {
                                    $badgeClass = 'info text-dark';
                                }
                            @endphp

                            <span class="badge bg-{{ $badgeClass }} px-3 py-2 text-uppercase"
                                  style="letter-spacing: 0.5px;">

                                {{ $absen->status }}

                            </span>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Belum ada log absensi.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center" style="padding: 0;">
            {{ $dataAbsensi->links('pagination::bootstrap-4') }}
        </div>

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