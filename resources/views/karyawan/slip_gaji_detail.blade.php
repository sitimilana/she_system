<!DOCTYPE html>
<html>
<head>
    <title>Detail Slip Gaji</title>
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

        .slip-header {
            background: #8fa1c7; border-radius: 16px; padding: 25px 30px;
            color: white; margin-bottom: 24px;
        }
        .detail-section {
            border-radius: 12px; border: 1px solid #dee2e6;
            overflow: hidden; margin-bottom: 20px;
        }
        .detail-section .section-title {
            background: #f8f9fa; padding: 12px 20px;
            font-weight: 600; font-size: 0.95rem; color: #555;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row {
            display: flex; justify-content: space-between;
            padding: 10px 20px; border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .label { color: #666; }
        .detail-row .value { font-weight: 500; }
        .total-row {
            background: #f8f9fa; padding: 14px 20px;
            font-weight: 700; font-size: 1rem;
            display: flex; justify-content: space-between;
        }
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
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('karyawan.slip-gaji') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h2 class="fw-bold m-0">Detail Slip Gaji</h2>
            <p class="text-muted mb-0 small">
                Periode: {{ $bulanList[$slip->bulan] ?? $slip->bulan }} {{ $slip->tahun }}
            </p>
        </div>
    </div>

    <!-- HEADER SLIP -->
    <div class="slip-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-1 fw-bold">{{ $karyawan->nama }}</h5>
                <p class="mb-0 opacity-75">{{ $karyawan->jabatan ?? '-' }} &nbsp;|&nbsp; {{ $karyawan->divisi ?? '-' }}</p>
            </div>
            <div class="col-auto text-end">
                <p class="mb-0 opacity-75 small">Periode</p>
                <h5 class="fw-bold mb-0">{{ $bulanList[$slip->bulan] ?? $slip->bulan }} {{ $slip->tahun }}</h5>
                <span class="badge bg-light text-dark mt-1">Final</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- PENERIMAAN -->
        <div class="col-md-6">
            <div class="detail-section">
                <div class="section-title"><i class="bi bi-plus-circle-fill text-success me-2"></i>Penerimaan</div>
                <div id="detail-penerimaan" style="display:none;">
                    <div class="detail-row">
                        <span class="label">Gaji Pokok</span>
                        <span class="value">Rp {{ number_format($slip->gaji_pokok ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Uang Makan</span>
                        <span class="value">Rp {{ number_format($slip->uang_makan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Tunjangan Jabatan (Leader)</span>
                        <span class="value">Rp {{ number_format($slip->tunjangan_jabatan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Insentif Kinerja</span>
                        <span class="value">Rp {{ number_format($slip->insentif_kinerja ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Tunjangan Program</span>
                        <span class="value">Rp {{ number_format($slip->tunjangan_program ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Tunjangan BPJS</span>
                        <span class="value">Rp {{ number_format($slip->tunjangan_bpjs ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Bonus</span>
                        <span class="value">Rp {{ number_format($slip->bonus ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Lain-lain</span>
                        <span class="value">Rp {{ number_format($slip->lain_lain ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @php
                    $totalPenerimaan = $slip->total_penerimaan ?? (
                        ($slip->gaji_pokok ?? 0) + ($slip->uang_makan ?? 0) +
                        ($slip->tunjangan_jabatan ?? 0) + ($slip->insentif_kinerja ?? 0) +
                        ($slip->tunjangan_program ?? 0) + ($slip->tunjangan_bpjs ?? 0) +
                        ($slip->bonus ?? 0) + ($slip->lain_lain ?? 0)
                    );
                @endphp
                <div class="total-row text-success">
                    <span>Total Penerimaan</span>
                    <span>Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
                </div>
                <div class="text-center py-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleDetail('penerimaan', this)">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- POTONGAN -->
        <div class="col-md-6">
            <div class="detail-section">
                <div class="section-title"><i class="bi bi-dash-circle-fill text-danger me-2"></i>Potongan</div>
                <div id="detail-potongan" style="display:none;">
                    <div class="detail-row">
                        <span class="label">Potongan Absen</span>
                        <span class="value text-danger">Rp {{ number_format($slip->potongan_absen ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Cash Bon</span>
                        <span class="value text-danger">Rp {{ number_format($slip->cash_bon ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Potongan BPJS</span>
                        <span class="value text-danger">Rp {{ number_format($slip->potongan_bpjs ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Potongan Lain-lain</span>
                        <span class="value text-danger">Rp {{ number_format($slip->potongan_lain ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @php
                    $totalPotongan = ($slip->potongan_absen ?? 0) + ($slip->cash_bon ?? 0)
                        + ($slip->potongan_bpjs ?? 0) + ($slip->potongan_lain ?? 0);
                @endphp
                <div class="total-row text-danger">
                    <span>Total Potongan</span>
                    <span>Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                </div>
                <div class="text-center py-2">
                    <button class="btn btn-sm btn-outline-danger" onclick="toggleDetail('potongan', this)">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- GAJI BERSIH -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mt-2" style="background:#8fa1c7;color:white;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="mb-0 opacity-75">Gaji Bersih Diterima</p>
                <h2 class="fw-bold mb-0">Rp {{ number_format($slip->total_gaji ?? 0, 0, ',', '.') }}</h2>
            </div>
            <i class="bi bi-cash-coin" style="font-size:3rem;opacity:0.4;"></i>
        </div>
    </div>
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

function toggleDetail(section, btn) {
    const el = document.getElementById('detail-' + section);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        btn.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Sembunyikan';
    } else {
        el.style.display = 'none';
        btn.innerHTML = '<i class="bi bi-eye me-1"></i> Lihat Detail';
    }
}
</script>
</body>
</html>
