<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333;
        }
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
        
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        .content { 
            margin-left: 250px; 
            padding: 40px; 
            transition: margin-left 0.3s ease;
        }
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); 
            transition: 0.3s;
        }
        .card-custom:hover { 
            box-shadow: 0 8px 25px rgba(0,0,0,0.05); 
            transform: translateY(-2px); 
        }
        .stat-widget { 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
            color: white; overflow: hidden; 
            position: relative;
        }
        .stat-widget::after {
            content: "\F4E1"; 
            font-family: "bootstrap-icons"; 
            position: absolute;
            right: -10px; 
            bottom: -20px; 
            font-size: 8rem; 
            color: rgba(255,255,255,0.05);
        }
        .stat-value { 
            font-size: 3.5rem; 
            font-weight: 800; 
            line-height: 1; 
            margin-bottom: 5px;
        }
        .list-item-custom {
            border: 1px solid #f1f5f9; 
            border-radius: 12px; 
            padding: 15px; 
            margin-bottom: 15px; 
            background: #fafafa; 
            transition: 0.2s;
        }
        .list-item-custom:hover { 
            background: #fff; 
            border-color: #e2e8f0; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
        }
        .modal-content { 
            border-radius: 16px; 
            border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        .modal-header { 
            border-bottom: 1px solid #f1f5f9; 
        }
        .modal-footer { 
            border-top: 1px solid #f1f5f9; 
        }

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
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link active"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50 px-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
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

    <div class="mb-5 d-none d-md-block">
        <h2 class="fw-bold m-0" style="color: #1e293b;">Selamat Datang, Kepala Bagian</h2>
        <p class="text-muted">Berikut adalah ringkasan operasional departemen Anda hari ini.</p>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-custom stat-widget p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-white-50 fw-medium mb-1">Karyawan Aktif</p>
                        <div class="stat-value">{{ $jumlahKaryawan ?? 0 }}</div>
                        <span class="badge bg-success bg-opacity-25 text-light mt-2"><i class="bi bi-person-check-fill"></i> Sedang Beroperasi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-custom stat-widget p-4 h-100" style="background: linear-gradient(135deg, #475569 0%, #334155 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-white-50 fw-medium mb-1">Karyawan Tidak Aktif</p>
                        <div class="stat-value">{{ $karyawanTidakAktif ?? 0 }}</div>
                        <span class="badge bg-secondary bg-opacity-25 text-light mt-2"><i class="bi bi-person-x-fill"></i> Tidak Aktif</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(120deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Progress Evaluasi ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }})</h6>
                        <p class="text-muted m-0 small"><span class="fw-bold text-dark">{{ $evaluasiSelesai ?? 0 }} / {{ $jumlahKaryawan ?? 0 }}</span> Selesai</p>
                    </div>
                    <a href="{{ route('kabag.penilaian') }}" class="btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-medium" style="font-size: 0.8rem;">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @php
                    $persentase = ($jumlahKaryawan > 0) ? round((($evaluasiSelesai ?? 0) / $jumlahKaryawan) * 100) : 0;
                @endphp
                <div class="progress bg-secondary bg-opacity-10 mt-1" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-list-check text-info me-2"></i>Karyawan Yang Belum Dievaluasi ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }})</h5>
                    <a href="{{ route('kabag.penilaian') }}" class="btn btn-sm btn-outline-primary">Lihat Selengkapnya</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama Karyawan</th>
                                <th width="20%">Divisi</th>
                                <th width="25%" class="text-center">Status Evaluasi</th>
                                <th width="20%" class="text-center">Aksi / Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $karyawanBelumDievaluasi = collect($progressPenilaian)->filter(function($item) {
                                    return empty($item->is_dinilai);
                                })->take(3);
                            @endphp

                            @forelse($karyawanBelumDievaluasi as $pegawai)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $pegawai->nama }}</td>
                                <td class="text-capitalize">{{ $pegawai->divisi ?? '-' }}</td>
                                <td class="text-center">
                                    @if($pegawai->is_dinilai)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Telah Dinilai</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Menunggu Evaluasi</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pegawai->is_dinilai)
                                        <span class="fw-bold text-primary">{{ $pegawai->total_skor }}</span> Poin
                                    @else
                                        <a href="{{ route('kabag.penilaian') }}" class="btn btn-sm btn-primary py-1 px-3" style="font-size: 0.8rem;">Evaluasi Sekarang</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                                    Semua Karyawan bulan ini telah dievaluasi!
                                </td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>