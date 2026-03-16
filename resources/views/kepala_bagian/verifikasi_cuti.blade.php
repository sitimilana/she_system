<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Cuti - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #e5e5e5; }
        .sidebar { width: 250px; min-height: 100vh; background: #8fa1c7; position: fixed; padding: 20px; }
        .sidebar img { width: 120px; display: block; margin: auto; margin-bottom: 30px; }
        .sidebar .nav-link { color: black; font-size: 18px; margin-bottom: 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.3); border-radius: 10px; }
        .content { margin-left: 260px; padding: 40px; }
        .table-custom th { background-color: #f8f9fa; font-weight: 600; }
        .action-btn { padding: 4px 8px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="{{ asset('images/logoshe.png') }}" alt="Logo">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door-fill"></i> Home</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people-fill"></i> Kelola Karyawan</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.cuti') }}" class="nav-link active"><i class="bi bi-calendar-check-fill"></i> Verifikasi Cuti</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star-fill"></i> Penilaian Kinerja</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a>
        </li>
        <li class="nav-item mt-3">
            <a href="#" class="nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>

<div class="content">
    <h2 class="fw-bold mb-4">Verifikasi Cuti</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0 table-custom">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Karyawan</th>
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
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $cuti->karyawan->nama ?? '-' }}</td>
                            <td>{{ $cuti->karyawan->jabatan ?? '-' }}</td>
                            <td>{{ $cuti->jenis_cuti }}</td>
                            <td>{{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($cuti->alasan, 50) }}</td>
                            <td class="text-center">
                                @if($cuti->berkas_bukti)
                                    <a href="{{ asset('storage/' . $cuti->berkas_bukti) }}"
                                       target="_blank" class="btn btn-info btn-sm text-white action-btn">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('kabag.cuti.approve', $cuti->id_cuti) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success action-btn me-1"
                                            title="Setujui"
                                            onclick="return confirm('Setujui pengajuan cuti ini?')">
                                        <i class="bi bi-check-circle"></i> Setujui
                                    </button>
                                </form>
                                <form action="{{ route('kabag.cuti.reject', $cuti->id_cuti) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger action-btn"
                                            title="Tolak"
                                            onclick="return confirm('Tolak pengajuan cuti ini?')">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada pengajuan cuti yang menunggu verifikasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
