<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pimpinan</title>
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

        /* CONTENT LAMA (TIDAK DIUBAH) */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #8f9fc4; border-radius: 15px; }
        .card { border-radius: 15px; border: none; }
        .metric-value { font-size: 2.5rem; font-weight: bold; color: #2c3e50; }
        .trend-up { color: #27ae60; font-size: 0.9rem; font-weight: bold;}
        .employee-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #ddd;}
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    
    <ul class="nav flex-column mt-5">
        <li class="nav-item">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link {{ Request::is('pimpinan/dashboard*') ? 'active' : '' }}">
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
        
        <li class="nav-item mt-4">
            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                @csrf
            </form>
            <a href="{{ route('logout') }}" class="nav-link text-white-50" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <h2 class="mb-4 fw-bold">Dashboard Strategis Pimpinan</h2>

    <!-- Transformasi Card Kehadiran (Real-time Insight) -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card p-3 shadow-sm h-100" style="border-radius: 15px;">
                <div class="row align-items-center h-100">
                    <div class="col-md-5 border-end text-center text-md-start px-4">
                        <p class="text-muted mb-1">Total Karyawan Aktif</p>
                        <div class="metric-value">{{ $totalKaryawan ?? 0 }}</div>
                        <p class="mt-2 mb-0" style="font-size: 0.9rem;">
                            <span class="badge bg-primary">{{ $karyawanCutiHariIni ?? 0 }} Cuti Hari Ini</span>
                        </p>
                    </div>
                    <div class="col-md-7">
                        <p class="text-muted text-center mb-1 small fw-bold">Insight Kehadiran Hari Ini</p>
                        <div class="d-flex justify-content-center align-items-center" style="height: 120px;">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pengajuan Cuti Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 shadow h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-calendar2-week"></i> Pengajuan Cuti Terbaru</h5>
                    <a href="{{ route('pimpinan.cuti') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                </div>
                <hr class="mt-0">

                @forelse($cutiTerbaru as $cuti)
                    <div class="bg-white rounded p-3 mb-3 shadow-sm border-start border-4 border-warning">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $cuti->karyawan->nama ?? 'Nama Tidak Ditemukan' }}</strong>
                            <span class="badge bg-{{ $cuti->status == 'Pending' ? 'warning' : 'success' }}">{{ $cuti->status }}</span>
                        </div>
                        <p class="mb-1 mt-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-clock"></i> {{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}
                        </p>
                    </div>
                @empty
                    <div class="alert alert-light text-center">Tidak ada pengajuan cuti baru.</div>
                @endforelse
            </div>
        </div>

        <!-- Top Performer -->
        <div class="col-md-6 mb-4">
            <div class="card card-custom p-4 shadow h-100" style="background-color: #2c3e50; color: white;">
                <h5 class="fw-bold mb-3"><i class="bi bi-trophy text-warning"></i> Top Performer Bulan Ini</h5>
                <hr class="mt-0 border-secondary">
                
                <p class="text-light" style="font-size: 0.9rem;">Karyawan dengan poin penilaian tertinggi yang direkomendasikan untuk Reward.</p>

                @forelse($topKaryawan as $top)
                    <div class="bg-dark rounded p-3 mb-3 shadow-sm d-flex align-items-center border-start border-4 border-success">
                        <div class="employee-avatar me-3 d-flex justify-content-center align-items-center text-dark fw-bold">
                            {{ substr($top->karyawan->nama ?? 'U', 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block">{{ $top->karyawan->nama ?? 'Data Terhapus' }}</strong>
                            <span class="text-muted" style="font-size: 0.8rem;">Departemen: {{ $top->karyawan->departemen ?? '-' }}</span>
                        </div>
                        <div class="text-end">
                            <h4 class="text-warning m-0 fw-bold">{{ $top->total_nilai ?? 0 }}</h4>
                            <small class="text-muted">Poin</small>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-secondary text-center">Belum ada data penilaian bulan ini.</div>
                @endforelse

                @if(!empty($topKaryawan) && count($topKaryawan) > 0)
                    <div class="mt-auto pt-3 text-end">
                        <a href="{{ route('pimpinan.reward') }}" class="btn btn-warning fw-bold shadow-sm">Eksekusi Reward</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Widget Status Penggajian -->
        <div class="col-md-12 mb-4">
            @php
                $statusPenggajian = $statusPenggajian ?? 'Finalisasi & Kirim';
                $bgColor = '#f4f7f6';
                if($statusPenggajian == 'Belum Dihitung') $bgColor = '#fce4e4'; // merah muda pucat
                elseif($statusPenggajian == 'Finalisasi & Kirim' || $statusPenggajian == 'Edit') $bgColor = '#fff3cd'; // kuning pastel
                elseif($statusPenggajian == 'Selesai Dikirim') $bgColor = '#d4edda'; // hijau pastel
            @endphp
            <div class="card p-4 shadow h-100 border-0" style="background-color: {{ $bgColor }}; border-radius: 15px;">
                <h5 class="fw-bold mb-3" style="color: #2c3e50;"><i class="bi bi-wallet2 text-primary"></i> Status Penggajian - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h5>
                <hr class="mt-0 border-secondary">
                
                <div class="d-flex flex-column justify-content-center flex-grow-1">
                    <p class="mb-3" style="color: #444; font-size: 1.05rem;">
                        Status saat ini: <strong class="fs-5">{{ $statusPenggajian }}</strong>
                    </p>
                    <p class="text-muted small mb-4">
                        @if($statusPenggajian == 'Belum Dihitung')
                            Sistem belum melakukan penarikan data kehadiran dan perhitungan gaji untuk periode ini.
                        @elseif($statusPenggajian == 'Finalisasi & Kirim' || $statusPenggajian == 'Edit')
                            Draf penggajian telah dibuat. Silakan lakukan proses {{ strtolower($statusPenggajian) }} slip gaji sebelum didistribusikan.
                        @else
                            Slip gaji bulan ini telah berhasil didistribusikan ke seluruh karyawan.
                        @endif
                    </p>
                    <a href="{{ route('pimpinan.gaji') }}" class="btn btn-primary shadow-sm fw-bold align-self-start"><i class="bi bi-gear"></i> Kelola Payroll</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('kehadiranChart').getContext('2d');
        var kehadiranChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Terlambat', 'Alpha', 'Cuti'],
                datasets: [{
                    // Anda bisa mengganti data dummy di bawah ini dengan variabel dari controller
                    data: [{{ $jmlHadir ?? 85 }}, {{ $jmlTerlambat ?? 10 }}, {{ $jmlAlpha ?? 2 }}, {{ $karyawanCutiHariIni ?? 3 }}], 
                    backgroundColor: [
                        '#a8e6cf', // pastel green (Hadir)
                        '#ffd3b6', // pastel orange/yellow (Terlambat)
                        '#ffaaa5', // pastel red (Alpha)
                        '#a2d5f2'  // pastel blue (Cuti)
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { size: 10, family: 'Inter' }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
</body>
</html>