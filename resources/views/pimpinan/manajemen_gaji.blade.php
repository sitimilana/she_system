<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Gaji - Pimpinan</title>
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

        /* CONTENT (Lama tetap dipertahankan, hanya menyesuaikan margin kiri sidebar baru) */
        .content { margin-left: 250px; padding: 30px; background: white; min-height: 100vh;}
        .table-custom th { background-color: #f8f9fa; font-weight: 600; }
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
            <h2 class="fw-bold m-0">Manajemen Gaji</h2>
            <p class="text-muted">Rekapitulasi penggajian karyawan.</p>
        </div>
        <a href="{{ route('pimpinan.gaji.create') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-2"></i> Buat Slip Gaji
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 p-3 bg-light">
        <form method="GET" action="{{ route('pimpinan.gaji') }}" class="row g-3 align-items-center">
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama karyawan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <label class="col-form-label fw-bold">Periode Penggajian:</label>
            </div>
            <div class="col-auto">
                <select class="form-select" name="bulan">
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select class="form-select" name="tahun">
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-secondary shadow-sm"><i class="bi bi-filter"></i> Tampilkan</button>
                @if(request('search'))
                    <a href="{{ route('pimpinan.gaji') }}?bulan={{ $bulan }}&tahun={{ $tahun }}" class="btn btn-outline-secondary">Reset Pencarian</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive shadow-sm rounded border">
        <table class="table table-hover align-middle m-0 table-custom">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama Karyawan</th>
                    <th>Periode</th>
                    <th>Total Penerimaan</th>
                    <th>Total Potongan</th>
                    <th>Gaji Bersih</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataGaji as $index => $gaji)
                @php
                    $totalPotongan = ($gaji->potongan_absen ?? 0) + ($gaji->cash_bon ?? 0)
                        + ($gaji->potongan_bpjs ?? 0) + ($gaji->potongan_lain ?? 0);
                    $totalPenerimaan = $gaji->total_penerimaan ?? (($gaji->total_gaji ?? 0) + $totalPotongan);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $gaji->karyawan->nama ?? '-' }}</td>
                    <td>{{ ($bulanList[$gaji->bulan] ?? $gaji->bulan) . ' ' . $gaji->tahun }}</td>
                    <td class="text-success">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                    <td class="text-danger">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('pimpinan.gaji.edit', $gaji->id_gaji) }}" class="btn btn-warning btn-sm text-dark me-1" title="Edit Data">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        @if($gaji->status_slip == 'draft')
                        <form action="{{ route('pimpinan.gaji.finalize', $gaji->id_gaji) }}" method="POST" class="d-inline" title="Finalisasi & Kirim">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Finalisasi slip gaji ini dan kirim ke karyawan?')">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('pimpinan.gaji.destroy', $gaji->id_gaji) }}" method="POST" class="d-inline" title="Hapus">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus slip gaji ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data gaji untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('auth.logout')

</body>
</html>