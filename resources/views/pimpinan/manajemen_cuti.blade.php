<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Cuti - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #eaeaea; }
        
        /* SIDEBAR (Sama dengan Dashboard) */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: black; font-size: 18px; padding: 12px 25px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.3); border-radius: 10px; }
        .sidebar .nav-link i { margin-right: 10px; }
        
        /* CONTENT */
        .content { margin-left: 260px; padding: 30px; background: white; min-height: 100vh;}
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8f9fa; font-weight: 600; }
        .search-bar { max-width: 400px; }
        .action-btn { padding: 4px 8px; font-size: 0.85rem; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house-door-fill"></i> Home</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.cuti') }}" class="nav-link active"><i class="bi bi-calendar-check-fill"></i> Manajemen Cuti</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.reward') }}" class="nav-link"><i class="bi bi-gift-fill"></i> Reward & Recognition</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('role.index') }}" class="nav-link"><i class="bi bi-person-gear"></i> Manajemen Role</a>
        </li>
        <li class="nav-item mt-3">
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Manajemen Cuti</h2>
        <button class="btn btn-danger"><i class="bi bi-file-earmark-pdf-fill"></i> Export PDF</button>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div class="input-group search-bar shadow-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search nama atau jabatan...">
        </div>
        <button class="btn btn-light shadow-sm border"><i class="bi bi-funnel-fill"></i> Filter</button>
    </div>

    <div class="table-responsive shadow-sm rounded border">
        <table class="table table-hover align-middle m-0 table-custom">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Tgl Mulai - Selesai</th>
                    <th>Jenis Cuti</th>
                    <th class="text-center">Status</th> <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataCuti as $index => $cuti)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $cuti['nama'] }}</td>
                    <td>{{ $cuti['jabatan'] }}</td>
                    <td>{{ $cuti['tgl_mulai'] }} s/d {{ $cuti['tgl_selesai'] }}</td>
                    <td>{{ $cuti['jenis'] }}</td>
                    <td class="text-center">
                        @if($cuti['status'] == 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($cuti['status'] == 'Disetujui')
                            <span class="badge bg-success">Disetujui</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-info text-white action-btn me-1" title="Lihat Detail & Bukti">
                            <i class="bi bi-eye"></i>
                        </button>
                        
                        @if($cuti['status'] == 'Pending')
                            <form action="#" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success action-btn me-1" title="Setujui"><i class="bi bi-check-circle"></i></button>
                            </form>
                            <form action="#" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger action-btn" title="Tolak"><i class="bi bi-x-circle"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengajuan cuti.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>