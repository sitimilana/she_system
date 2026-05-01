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
            padding: 12px;
        }
        
        .search-bar { max-width: 350px; }
        .action-btn { padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; font-weight: 500; }
        .badge-status { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
        .jenis-cuti-badge { padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; background-color: #e2e8f0; color: #475569; }
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
            <h2 class="fw-bold m-0" style="color: #1e293b;">Manajemen Cuti</h2>
            <p class="text-muted m-0">Verifikasi dan pantau pengajuan cuti seluruh karyawan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <form method="GET" action="{{ route('pimpinan.cuti') }}" id="formFilter" class="d-flex gap-2 mb-4 flex-wrap">
            <div class="input-group search-bar">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="searchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama atau jabatan..." value="{{ request('search') }}">
            </div>
            <select name="jenis_cuti" id="jenisCutiSelect" class="form-select bg-light w-auto">
                <option value="">Semua Jenis Cuti</option>
                <option value="Izin" {{ request('jenis_cuti') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Cuti" {{ request('jenis_cuti') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Sakit" {{ request('jenis_cuti') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
            <button type="submit" class="btn btn-primary px-3 rounded-3 shadow-sm d-none"><i class="bi bi-funnel"></i> Filter</button>
            @if(request('search') || request('jenis_cuti'))
                <a href="{{ route('pimpinan.cuti') }}" class="btn btn-light border px-3 rounded-3">Reset</a>
            @endif
        </form>

        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-hourglass-split text-warning me-2"></i>Belum Tervalidasi</h5>
        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle m-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="20%">Nama & Jabatan</th>
                        <th width="15%" class="text-center">Jenis Cuti</th>
                        <th width="20%">Tgl Pelaksanaan</th>
                        <th width="15%">Alasan</th>
                        <th class="text-center" width="10%">Berkas</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataCuti as $index => $cuti)
                    <tr>
                        <td class="text-center">{{ ($dataCuti->currentPage() - 1) * $dataCuti->perPage() + $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cuti->karyawan->nama ?? '-' }}</div>
                            <div class="small text-muted">{{ $cuti->karyawan->jabatan ?? '-' }}</div>
                        </td>
                        <td class="text-center"><span class="jenis-cuti-badge">{{ $cuti->jenis_cuti }}</span></td>
                        <td>
                            <strong class="text-dark"><i class="bi bi-calendar-event me-1 text-muted"></i> {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</strong><br>
                            <small class="text-muted">s.d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</small>
                        </td>
                        <td><small class="text-muted" title="{{ $cuti->alasan }}">{{ \Illuminate\Support\Str::limit($cuti->alasan, 40) }}</small></td>
                        <td class="text-center">
                            @if($cuti->berkas_bukti)
                                <a href="{{ asset('storage/' . $cuti->berkas_bukti) }}" target="_blank" class="btn btn-light border btn-sm text-primary action-btn" title="Lihat Berkas"><i class="bi bi-file-earmark-text"></i> Berkas</a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <form action="{{ route('pimpinan.cuti.approve', $cuti->id_cuti) }}" method="POST" class="d-inline">
                                    @csrf 
                                    <button type="submit" class="btn btn-success action-btn shadow-sm" title="Setujui" onclick="return confirm('Setujui & potong saldo cuti karyawan ini?')"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <form action="{{ route('pimpinan.cuti.reject', $cuti->id_cuti) }}" method="POST" class="d-inline">
                                    @csrf 
                                    <button type="submit" class="btn btn-danger action-btn shadow-sm" title="Tolak" onclick="return confirm('Tolak pengajuan cuti ini?')"><i class="bi bi-x-lg"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2 text-black-50"></i>
                            Tidak ada pengajuan cuti yang menunggu validasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $dataCuti->links() }}</div>
    </div>

    <div class="card card-custom p-4">
        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history text-info me-2"></i> Tervalidasi</h5>
        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle m-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="25%">Nama & Jabatan</th>
                        <th width="15%" class="text-center">Jenis Cuti</th>
                        <th width="25%">Tgl Pelaksanaan</th>
                        <th class="text-center" width="15%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatCuti as $index => $cuti)
                    <tr>
                        <td class="text-center">{{ ($riwayatCuti->currentPage() - 1) * $riwayatCuti->perPage() + $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cuti->karyawan->nama ?? '-' }}</div>
                            <div class="small text-muted">{{ $cuti->karyawan->jabatan ?? '-' }}</div>
                        </td>
                        <td class="text-center"><span class="jenis-cuti-badge">{{ $cuti->jenis_cuti }}</span></td>
                        <td>
                            <strong class="text-dark"><i class="bi bi-calendar-event me-1 text-muted"></i> {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</strong><br>
                            <small class="text-muted">s.d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</small>
                        </td>
                        <td class="text-center">
                            @if(strtolower($cuti->status) === 'approved' || strtolower($cuti->status) === 'disetujui')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 badge-status"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                            @elseif(strtolower($cuti->status) === 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 badge-status"><i class="bi bi-clock-fill me-1"></i> Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 badge-status"><i class="bi bi-x-circle-fill me-1"></i> {{ ucfirst($cuti->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                            Belum ada riwayat cuti.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $riwayatCuti->links() }}</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formFilter = document.getElementById('formFilter');
        const searchInput = document.getElementById('searchInput');
        const jenisCutiSelect = document.getElementById('jenisCutiSelect');

        let debounceTimer;

        // Auto submit setelah mengetik
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                formFilter.submit();
            }, 800); // Delay 800ms agar dpt mengetik beberapa huruf sebelum auto reload
        });

        // Auto submit setelah milih jenis cuti
        jenisCutiSelect.addEventListener('change', function() {
            formFilter.submit();
        });

        // Kembalikan fokus ke input pencarian tanpa mengganggu kursor
        if (searchInput.value) {
            let val = searchInput.value;
            searchInput.focus();
            searchInput.value = '';
            searchInput.value = val;
        }
    });
</script>

@include('auth.logout')

</body>
</html>
