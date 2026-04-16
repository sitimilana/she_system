<!DOCTYPE html>
<html>
<head>
    <title>Kelola Karyawan - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Data Karyawan</h2>
            <p class="text-muted m-0">Daftar staf dan karyawan di departemen Anda.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama atau jabatan...">
            </div>
            
            <div class="d-flex gap-2">
                <button class="btn btn-light border shadow-sm"><i class="bi bi-funnel"></i> Filter</button>
                <button class="btn btn-outline-secondary shadow-sm"><i class="bi bi-printer me-2"></i>Cetak</button>
                
                <button class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Baru</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-custom m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama</th>
                        <th width="15%">Divisi</th>
                        <th width="15%">Kontak</th>
                        <th width="20%">Alamat</th>
                        <th width="15%" class="text-center">Status Kerja</th>
                        <th width="10%" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataKaryawan as $index => $user)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $user->nama_lengkap }}</td>
                        <td class="text-capitalize">{{ $user->karyawan->divisi ?? '-' }}</td>
                        <td>{{ $user->karyawan->no_hp ?? '-' }}</td>
                        <td class="text-truncate" style="max-width: 150px;" title="{{ $user->karyawan->alamat ?? '-' }}">
                            {{ $user->karyawan->alamat ?? '-' }}
                        </td>
                        <td class="text-center">
                            @if($user->karyawan && $user->karyawan->status_karyawan)
                                <span class="badge bg-{{ Illuminate\Support\Str::lower($user->karyawan->status_karyawan) == 'aktif' ? 'success' : 'warning text-dark' }} px-3 py-2 rounded-pill">
                                    {{ $user->karyawan->status_karyawan }}
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                    Belum Dilengkapi
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kabag.karyawan.detail', $user->id_user) }}" class="btn btn-sm btn-light border text-primary" title="Lengkapi Profil">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data karyawan di departemen ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-sm m-0">
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

@include('auth.logout')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>