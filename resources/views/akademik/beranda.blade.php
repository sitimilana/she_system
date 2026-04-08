<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .metric-value { font-size: 2.5rem; font-weight: 800; color: #1e293b; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('akademik.beranda') }}" class="nav-link active"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{route('akademik.absensi') }}" class="nav-link"><i class="bi bi-journal-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="{{ route('akademik.cuti') }}" class="nav-link"><i class="bi bi-calendar-range"></i> Riwayat Cuti</a></li>
        <li class="nav-item"><a href="{{ route('akademik.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Manajemen Karyawan</a></li>
        <li class="nav-item mt-4">
            <a href="{{ route('logout') }}" class="nav-link text-white-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <h2 class="fw-bold mb-4" style="color: #1e293b;">Dashboard Akademik</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="metric-value">{{ $totalKaryawan }}</div>
                <p class="text-muted m-0 fw-medium">Jumlah Seluruh Karyawan</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center" style="border-bottom: 4px solid #3b82f6;">
                <div class="metric-value text-primary">{{ $hadirHariIni }}</div>
                <p class="text-muted m-0 fw-medium">Karyawan Hadir Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3">Rekap Cuti Terkini</h5>
                <hr class="mt-0">
                
                @forelse($rekapCuti as $cuti)
                    <div class="bg-light rounded p-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="fs-5">{{ $cuti->nama }}</strong>
                            <button class="btn btn-sm btn-danger fw-bold">Detail</button>
                        </div>
                        <p class="m-0 text-muted" style="font-size: 0.9rem;">
                            Tanggal: {{ $cuti->tgl_mulai }} s/d {{ $cuti->tgl_selesai }} <br>
                            Status: <span class="badge bg-{{ $cuti->status == 'Pending' ? 'warning' : 'success' }}">{{ $cuti->status }}</span>
                        </p>
                    </div>
                @empty
                    <div class="alert alert-secondary">Tidak ada data cuti.</div>
                @endforelse
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3">Grafik Rekap Absensi Hari Ini</h5>
                <hr class="mt-0 mb-4">
                <div style="max-height: 300px; display: flex; justify-content: center;">
                    <canvas id="absensiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('absensiChart');
    new Chart(ctx, {
        type: 'pie',
        data: {
            // Label disesuaikan persis dengan gambar mockup Anda
            labels: ['Hadir', 'Tidak Hadir', 'Sakit', 'Izin', 'Cuti'],
            datasets: [{
                data: [
                    {{ $rekapAbsensi['Hadir'] }},
                    {{ $rekapAbsensi['Tidak Hadir'] }},
                    {{ $rekapAbsensi['Sakit'] }},
                    {{ $rekapAbsensi['Izin'] }},
                    {{ $rekapAbsensi['Cuti'] }}
                ],
                backgroundColor: [
                    '#10b981', // Hijau (Hadir)
                    '#ef4444', // Merah (Tidak Hadir)
                    '#3b82f6', // Biru (Sakit)
                    '#d946ef', // Ungu (Izin)
                    '#eab308'  // Kuning (Cuti)
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>