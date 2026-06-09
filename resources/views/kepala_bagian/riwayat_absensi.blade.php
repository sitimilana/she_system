<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Absensi - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 1045; transition: transform 0.3s ease-in-out; }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px; }
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: 0.3s; }
        
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: 0.9rem; padding: 12px 10px; white-space: nowrap; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; padding: 12px 10px; }
        
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 15px !important; }
            .sidebar-overlay.show { display: block; }
        }

        /* Tambahan style untuk form filter agar seragam */
        .form-control, .form-select { border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.9rem; padding: 10px 15px;}
        .form-control:focus, .form-select:focus { border-color: #8f9fc4; box-shadow: 0 0 0 0.25rem rgba(143, 159, 196, 0.25); }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <button class="btn text-white position-absolute top-0 end-0 mt-3 me-2 d-md-none fs-4" id="closeSidebarBtn"><i class="bi bi-x-lg"></i></button>
    <div class="logo"><img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo"></div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item"><a href="{{ route('kabag.riwayat_absensi') }}" class="nav-link active"><i class="bi bi-calendar-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="{{ route('kabag.riwayat_cuti') }}" class="nav-link"><i class="bi bi-calendar-event"></i> Riwayat Cuti</a></li>
        <li class="nav-item mt-4"><a href="#" class="nav-link text-white-50 px-3" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Riwayat Absensi</h5>
        <button class="btn btn-light border" id="openSidebarBtn"><i class="bi bi-list fs-4"></i></button>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 d-none d-md-flex">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Riwayat Absensi Karyawan</h2>
            <p class="text-muted m-0">Pantau kehadiran karyawan setiap harinya.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="mb-4 bg-light p-3 rounded-3 border">
            <form action="{{ route('kabag.riwayat_absensi') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Cari Karyawan</label>
                        <input type="text" name="search" class="form-control" placeholder="Ketik nama..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">Bulan</label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @php $currentYear = date('Y'); @endphp
                            @for($y = $currentYear; $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Status Kehadiran</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
                            <option value="alfa" {{ request('status') == 'alfa' ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                        <a href="{{ route('kabag.riwayat_absensi') }}" class="btn btn-light border py-2" title="Reset Data"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-custom m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama Karyawan</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Masuk</th>
                        <th width="15%">Keluar</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Foto Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataAbsensi as $index => $absen)
                    <tr>
                        <td class="text-center">{{ $dataAbsensi->firstItem() + $index }}</td>
                        <td class="fw-bold">{{ $absen->karyawan->nama ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($absen->tanggal)->locale('id')->translatedFormat('d F Y') }}</td>
                        <td>
                            @if($absen->waktu_masuk) <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-box-arrow-in-right"></i> {{ $absen->waktu_masuk }}</span> @else - @endif
                        </td>
                        <td>
                            @if($absen->waktu_keluar) <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-box-arrow-right"></i> {{ $absen->waktu_keluar }}</span> @else - @endif
                        </td>
                        <td class="text-center">
                            @if(strtolower($absen->status) == 'hadir') <span class="badge bg-success px-3">Hadir</span>
                            @elseif(strtolower($absen->status) == 'terlambat') <span class="badge bg-warning text-dark px-3">Terlambat</span>
                            @elseif(strtolower($absen->status) == 'alfa') <span class="badge bg-danger px-3">Alfa</span>
                            @else <span class="badge bg-info text-dark px-3 text-capitalize">{{ $absen->status }}</span> @endif
                        </td>
                        <td class="text-center">
                            @if($absen->foto_masuk || $absen->foto_keluar)
                                <button class="btn btn-sm btn-outline-primary" onclick="alert('Fitur lihat foto')"><i class="bi bi-image"></i> Lihat</button>
                            @else
                                <span class="text-muted small">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>Belum ada data absensi yang sesuai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($dataAbsensi->hasPages())
        <div class="d-flex flex-column align-items-center mt-4 gap-2 text-center">
            <div class="text-muted small">
                Menampilkan <strong>{{ $dataAbsensi->firstItem() }}</strong> s/d <strong>{{ $dataAbsensi->lastItem() }}</strong> dari <strong>{{ $dataAbsensi->total() }}</strong> riwayat absensi
            </div>
            <div class="d-flex justify-content-center">
                {{ $dataAbsensi->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif

    </div>
</div>

@include('auth.logout')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar'), openBtn = document.getElementById('openSidebarBtn'), closeBtn = document.getElementById('closeSidebarBtn'), overlay = document.getElementById('sidebarOverlay');
        function openSidebar() { sidebar.classList.add('show-mobile'); overlay.classList.add('show'); }
        function closeSidebar() { sidebar.classList.remove('show-mobile'); overlay.classList.remove('show'); }
        if(openBtn) openBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>
</body>
</html>