<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Karyawan - Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR AKADEMIK */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 30px; margin-bottom: 20px;}
        .sidebar .nav-link { color: #fff; font-size: 15px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; border-radius: 8px;}
        .sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { background-color: rgba(255,255,255,0.3); font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem;}
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logoshe.png') }}" alt="Logo" style="width: 100%;">
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item"><a href="{{ route('akademik.beranda') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('akademik.absensi') }}" class="nav-link"><i class="bi bi-journal-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="{{ route('akademik.cuti') }}" class="nav-link"><i class="bi bi-calendar-range"></i> Riwayat Cuti</a></li>
        <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-people"></i> Manajemen Karyawan</a></li>
        
        <li class="nav-item mt-5 pt-3 border-top border-light border-opacity-25 mx-3">
            <a href="{{ route('logout') }}" class="nav-link text-white-50 px-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Data Karyawan</h2>
            <p class="text-muted m-0">Pemantauan daftar staf dan karyawan (Akses Read-Only).</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group shadow-sm" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama atau jabatan...">
            </div>
            
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary bg-white shadow-sm"><i class="bi bi-funnel"></i> Filter</button>
                <button class="btn btn-outline-secondary bg-white shadow-sm"><i class="bi bi-printer me-2"></i>Cetak</button>
                </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-custom m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama</th>
                        <th width="15%">Jabatan</th>
                        <th width="15%">Kontak</th>
                        <th width="20%">Alamat</th>
                        <th width="15%" class="text-center">Status Kerja</th>
                        <th width="10%" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataKaryawan as $index => $karyawan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $karyawan->nama }}</td>
                        <td>{{ $karyawan->jabatan }}</td>
                        <td>{{ $karyawan->kontak }}</td>
                        <td class="text-truncate" style="max-width: 150px;" title="{{ $karyawan->alamat }}">
                            {{ $karyawan->alamat }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $karyawan->status_kerja == 'Aktif' ? 'success' : 'warning text-dark' }} px-3 py-2 rounded-pill">
                                {{ $karyawan->status_kerja }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-light border text-primary" title="Lihat Profil Lengkap (Read-Only)">
                                <i class="bi bi-person-vcard"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-sm m-0 shadow-sm">
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>

    </div>
</div>

</body>
</html>