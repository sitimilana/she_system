<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #e5e5e5; }

        .sidebar {
            width: 250px; min-height: 100vh; background: #8fa1c7;
            position: fixed; padding: 20px;
        }
        .sidebar img { width: 120px; display: block; margin: auto; margin-bottom: 30px; }
        .sidebar .nav-link { color: black; font-size: 18px; margin-bottom: 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.3); border-radius: 10px;
        }

        .content { margin-left: 260px; padding: 40px; }

        .card-info {
            background: #8fa1c7; border-radius: 20px; padding: 30px;
            box-shadow: 0px 5px 10px rgba(0,0,0,0.2); color: white;
        }
        .card-slip {
            background: white; border-radius: 15px; padding: 20px;
            box-shadow: 0px 5px 10px rgba(0,0,0,0.1); margin-bottom: 15px;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="{{ asset('images/logoshe.png') }}" alt="Logo">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('karyawan.dashboard') }}" class="nav-link active">
                <i class="bi bi-house"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('karyawan.slip-gaji') }}" class="nav-link">
                <i class="bi bi-file-earmark-text"></i> Slip Gaji
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link" onclick="confirmLogout(event)">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
</div>

<!-- CONTENT -->
<div class="content">
    <h1 class="mb-4"><b>Dashboard Karyawan</b></h1>

    @if($karyawan)
    <!-- INFO KARYAWAN -->
    <div class="card-info mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($karyawan->foto)
                    <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto"
                         class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid white;">
                @else
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center"
                         style="width:80px;height:80px;">
                        <i class="bi bi-person-fill text-secondary" style="font-size:2.5rem;"></i>
                    </div>
                @endif
            </div>
            <div class="col">
                <h4 class="mb-1 fw-bold">{{ $karyawan->nama }}</h4>
                <p class="mb-0">{{ $karyawan->jabatan ?? '-' }} &nbsp;|&nbsp; {{ $karyawan->divisi ?? '-' }}</p>
                <p class="mb-0 small opacity-75">{{ $karyawan->email ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- SLIP GAJI TERBARU -->
    <h5 class="fw-bold mb-3">Slip Gaji Terbaru</h5>
    @if($latestSlip)
    <div class="card-slip d-flex justify-content-between align-items-center">
        <div>
            <p class="mb-1 fw-bold fs-5">
                {{ $bulanList[$latestSlip->bulan] ?? $latestSlip->bulan }} {{ $latestSlip->tahun }}
            </p>
            <p class="mb-0 text-muted">Total Gaji Bersih</p>
            <h4 class="fw-bold text-success">Rp {{ number_format($latestSlip->total_gaji, 0, ',', '.') }}</h4>
            <span class="badge bg-success">Final</span>
        </div>
        <a href="{{ route('karyawan.slip-gaji.show', $latestSlip->id_gaji) }}" class="btn btn-primary">
            <i class="bi bi-eye me-1"></i> Detail
        </a>
    </div>
    @else
    <div class="card-slip text-center text-muted py-4">
        <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
        Belum ada slip gaji yang diterbitkan.
    </div>
    @endif

    @else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Data karyawan tidak ditemukan. Hubungi administrator.
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Konfirmasi Logout</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin logout?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger"
                        onclick="document.getElementById('logout-form').submit()">Ya, Logout</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout(e) {
    e.preventDefault();
    new bootstrap.Modal(document.getElementById('logoutModal')).show();
}
</script>
</body>
</html>
