<!DOCTYPE html>
<html>
<head>
    <title>Persetujuan Karyawan - Pimpinan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333; 
        }

        /* SIDEBAR STRUKTUR */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 100;
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

        /* LINK NAVIGASI */
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

        /* HOVER & ACTIVE STATE */
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            background-color: rgba(255,255,255,0.2); 
            border-radius: 8px; 
            font-weight: 600;
            color: #fff;
        }

        .sidebar .nav-link.text-white-50:hover {
            color: #fff !important;
        }

        .content { 
            margin-left: 250px; 
            padding: 40px; 
            min-height: 100vh;
        }
        
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            margin-bottom: 30px;
        }
        
        /* TABLE STYLES YANG DIRAPIKAN */
        .table-custom th { 
            background-color: #f8fafc; 
            color: #4a5568; 
            font-weight: 600; 
            border-bottom: 2px solid #e2e8f0; 
            padding: 15px 12px;
            white-space: nowrap;
        }

        .table-custom td { 
            vertical-align: middle; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 15px 12px;
            color: #475569;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>

<div class="sidebar">
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
            <h2 class="fw-bold m-0" style="color: #1e293b;">Daftar Karyawan Menunggu Persetujuan</h2>
            <p class="text-muted m-0">Menampilkan staf baru yang telah diinput Kepala Bagian.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-clock-history d-inline-block me-2 text-warning"></i>Data Menunggu Persetujuan</h5>
        </div>

        <div class="table-responsive">
            <!-- MENGGUNAKAN TABLE CUSTOM AGAR LEBIH RAPI -->
            <table class="table table-custom align-middle m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="20%">Role / Hak Akses</th>
                        <th width="20%">Divisi</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->nama_lengkap }}</div>
                            <small class="text-muted">{{ $user->username }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $user->role->nama_role ?? '-' }}</span></td>
                        <td><span class="text-capitalize">{{ $user->karyawan->divisi ?? '-' }}</span></td>
                        <td class="text-center">
                            <span class="status-badge"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-sm rounded-3 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $user->id_user }}">
                                <i class="bi bi-search me-1"></i> Review
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                            Tidak ada pengajuan karyawan baru saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan -->
@foreach($users as $user)
<div class="modal fade" id="modalDetail{{ $user->id_user }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Detail Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-5">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Akun</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td width="40%" class="text-muted small">Username</td><td class="fw-medium">: {{ $user->username }}</td></tr>
                            <tr><td class="text-muted small">Role Akses</td><td class="fw-medium">: <span class="badge bg-secondary">{{ $user->role->nama_role ?? '-' }}</span></td></tr>
                            <tr><td class="text-muted small">Tgl Daftar</td><td class="fw-medium">: {{ $user->created_at->format('d M Y') }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-7 border-start">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Biodata Karyawan</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td width="35%" class="text-muted small">Nama Lengkap</td><td class="fw-medium">: {{ $user->karyawan->nama ?? $user->nama_lengkap }}</td></tr>
                            <tr><td class="text-muted small">Divisi/Jabatan</td><td class="fw-medium text-capitalize">: {{ $user->karyawan->divisi ?? '-' }}</td></tr>
                            <tr><td class="text-muted small">Kontak (HP)</td><td class="fw-medium">: {{ $user->karyawan->no_hp ?? '-' }}</td></tr>
                            <tr><td class="text-muted small">Email</td><td class="fw-medium">: {{ $user->karyawan->email ?? '-' }}</td></tr>
                            <tr><td class="text-muted small">Alamat</td><td class="fw-medium">: {{ $user->karyawan->alamat ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="alert alert-primary mt-4 mb-0 d-flex align-items-center border-0 bg-primary bg-opacity-10">
                    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                    <div>
                        <strong class="text-primary">Keputusan Persetujuan</strong><br>
                        <span class="text-dark small">Pastikan data di atas sudah benar. Jika disetujui, karyawan ini akan berstatus aktif dan dapat login.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4 bg-white rounded-bottom-4 d-flex justify-content-between">
                
                <!-- TOMBOL TOLAK DITAMBAHKAN KEMBALI DI SINI -->
                <form action="{{ route('pimpinan.rejectKaryawan', $user->id_user) }}" method="POST" class="m-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4" onclick="return confirm('Tolak dan hapus data karyawan ini?')">
                        <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                    </button>
                </form>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Tutup</button>
                    
                    <form action="{{ route('pimpinan.approveKaryawan', $user->id_user) }}" method="POST" class="m-0">
                        @csrf
                        @method('PUT') <!-- Sesuaikan method dengan di web.php -->
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Setujui & Aktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('auth.logout')

</body>
</html>