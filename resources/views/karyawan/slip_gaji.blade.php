<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji - Karyawan</title>
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

        .content { margin-left: 260px; padding: 40px; background: white; min-height: 100vh; }

        .slip-card {
            border-radius: 12px; border: 1px solid #dee2e6;
            padding: 20px; background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: box-shadow 0.2s;
        }
        .slip-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('karyawan.dashboard') }}" class="nav-link">
                <i class="bi bi-house"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('karyawan.slip-gaji') }}" class="nav-link active">
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Slip Gaji Saya</h2>
            <p class="text-muted mb-0">Riwayat slip gaji yang telah diterbitkan.</p>
        </div>
    </div>

    @if($karyawan)

    <div class="row g-3 mb-4">
        @forelse($dataSlip as $slip)
        <div class="col-md-6 col-lg-4">
            <div class="slip-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0 fs-5">
                            {{ $bulanList[$slip->bulan] ?? $slip->bulan }} {{ $slip->tahun }}
                        </h6>
                        <span class="badge bg-success">Final</span>
                    </div>
                    <p class="text-muted small mb-1">Total Gaji Bersih</p>
                    <h4 class="fw-bold text-success">Rp {{ number_format($slip->total_gaji, 0, ',', '.') }}</h4>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('karyawan.slip-gaji.show', $slip->id_gaji) }}"
                       class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                Belum ada slip gaji yang diterbitkan.
            </div>
        </div>
        @endforelse
    </div>

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
