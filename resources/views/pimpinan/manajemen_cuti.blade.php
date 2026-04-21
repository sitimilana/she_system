<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Cuti - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333; 
        }

        /* SIDEBAR STRUKTUR BARU */
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

        /* CONTENT (Lama tetap dipertahankan) */
        .content { margin-left: 250px; padding: 30px; background: white; min-height: 100vh;}
        
        /* TABLE & KHUSUS CUTI STYLES (Tidak Diubah) */

        body { background: #eaeaea; }
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: black; font-size: 18px; padding: 12px 25px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.3); border-radius: 10px; }
        .sidebar .nav-link i { margin-right: 10px; }
        .content { margin-left: 260px; padding: 30px; background: white; min-height: 100vh;}

        .table-custom th { background-color: #f8f9fa; font-weight: 600; }
        .search-bar { max-width: 400px; }
        .action-btn { padding: 4px 8px; font-size: 0.85rem; }
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
            <a href="{{ route('role.index') }}" class="nav-link {{ Request::is('role*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Manajemen Role
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
        <h2 class="fw-bold m-0">Manajemen Cuti</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('pimpinan.cuti') }}" class="d-flex gap-2 mb-3 flex-wrap">
        <div class="input-group search-bar shadow-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau jabatan..." value="{{ request('search') }}">
        </div>
        <select name="jenis_cuti" class="form-select w-auto shadow-sm">
            <option value="">Semua Jenis Cuti</option>
            <option value="Cuti Tahunan" {{ request('jenis_cuti') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
            <option value="Cuti Sakit" {{ request('jenis_cuti') == 'Cuti Sakit' ? 'selected' : '' }}>Cuti Sakit</option>
            <option value="Cuti Melahirkan" {{ request('jenis_cuti') == 'Cuti Melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
            <option value="Izin" {{ request('jenis_cuti') == 'Izin' ? 'selected' : '' }}>Izin</option>
        </select>
        <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-funnel-fill"></i> Filter</button>
        @if(request('search') || request('jenis_cuti'))
            <a href="{{ route('pimpinan.cuti') }}" class="btn btn-secondary shadow-sm">Reset</a>
        @endif
    </form>

    <h5 class="fw-semibold mb-2">Menunggu Validasi Pimpinan</h5>
    <div class="table-responsive shadow-sm rounded border mb-3">
        <table class="table table-hover align-middle m-0 table-custom">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Jenis Cuti</th>
                    <th>Tgl Mulai - Selesai</th>
                    <th>Alasan</th>
                    <th>Berkas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataCuti as $index => $cuti)
                <tr>
                    <td class="text-center">{{ ($dataCuti->currentPage() - 1) * $dataCuti->perPage() + $index + 1 }}</td>
                    <td class="fw-bold">{{ $cuti->karyawan->nama ?? '-' }}</td>
                    <td>{{ $cuti->karyawan->jabatan ?? '-' }}</td>
                    <td>{{ $cuti->jenis_cuti }}</td>
                    <td>{{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($cuti->alasan, 50) }}</td>
                    <td class="text-center">
                        @if($cuti->berkas_bukti)
                            <a href="{{ asset('storage/' . $cuti->berkas_bukti) }}" target="_blank" class="btn btn-info btn-sm text-white action-btn"><i class="bi bi-file-earmark-text"></i></a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('pimpinan.cuti.approve', $cuti->id_cuti) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success action-btn me-1" onclick="return confirm('Setujui & potong saldo cuti karyawan ini?')"><i class="bi bi-check-circle"></i> Setujui</button>
                        </form>
                        <form action="{{ route('pimpinan.cuti.reject', $cuti->id_cuti) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-danger action-btn" onclick="return confirm('Tolak pengajuan cuti ini?')"><i class="bi bi-x-circle"></i> Tolak</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada pengajuan cuti yang menunggu validasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mb-4">{{ $dataCuti->links() }}</div>

    <h5 class="fw-semibold mb-2 mt-2">Riwayat Cuti</h5>
    <div class="table-responsive shadow-sm rounded border">
        <table class="table table-hover align-middle m-0 table-custom">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Jenis Cuti</th>
                    <th>Tgl Mulai - Selesai</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatCuti as $index => $cuti)
                <tr>
                    <td class="text-center">{{ ($riwayatCuti->currentPage() - 1) * $riwayatCuti->perPage() + $index + 1 }}</td>
                    <td class="fw-bold">{{ $cuti->karyawan->nama ?? '-' }}</td>
                    <td>{{ $cuti->karyawan->jabatan ?? '-' }}</td>
                    <td>{{ $cuti->jenis_cuti }}</td>
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                    <td class="text-center">
                        @if($cuti->status === 'approved' || $cuti->status === 'Disetujui')
                            <span class="badge bg-success">Disetujui</span>
                        @elseif($cuti->status === 'Pending' || $cuti->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-danger">{{ $cuti->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat cuti.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-2">{{ $riwayatCuti->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('auth.logout')

</body>
</html>
