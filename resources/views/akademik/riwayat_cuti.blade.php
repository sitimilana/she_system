<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Cuti - Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR (Konsisten dengan Absensi) */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 30px; margin-bottom: 20px;}
        .sidebar .nav-link { color: #fff; font-size: 15px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; border-radius: 8px;}
        .sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { background-color: rgba(255,255,255,0.3); font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 30px; }
        .card-custom { background-color: #ffffff; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem; vertical-align: middle;}
        .table-custom td { vertical-align: middle; font-size: 0.9rem; }
        
        /* THUMBNAIL & TEXT TRUNCATE */
        .photo-thumb { width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6; cursor: pointer; transition: 0.2s;}
        .photo-thumb:hover { transform: scale(1.1); box-shadow: 0 4px 8px rgba(0,0,0,0.1);}
        .text-truncate-custom { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block;}
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logoshe.png') }}" alt="Logo" style="width: 100%;">
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item"><a href="{{ route('akademik.beranda') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('akademik.absensi') }}" class="nav-link"><i class="bi bi-journal-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-calendar-range"></i> Riwayat Cuti</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-people"></i> Manajemen Karyawan</a></li>
        
        <li class="nav-item mt-5 pt-3 border-top border-light border-opacity-25 mx-3">
            <a href="{{ route('logout') }}" class="nav-link text-white-50 px-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0" style="color: #1e293b;">Riwayat Cuti & Izin</h3>
            <p class="text-muted m-0">Data pengajuan cuti karyawan dan status persetujuan pimpinan.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Cari karyawan...">
            </div>
            <button class="btn btn-outline-secondary bg-white shadow-sm text-nowrap"><i class="bi bi-funnel me-2"></i>Filter</button>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover table-custom m-0 text-nowrap">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Karyawan</th>
                        <th>Tgl Pengajuan</th>
                        <th class="text-center">Periode Cuti <br><small class="text-muted fw-normal">(Mulai - Selesai)</small></th>
                        <th>Alasan</th>
                        <th class="text-center">Berkas/Bukti</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataCuti as $index => $cuti)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $cuti->nama_karyawan }}</td>
                        <td>{{ \Carbon\Carbon::parse($cuti->tanggal_pengajuan)->format('d M Y') }}</td>
                        
                        <td class="text-center">
                            <span class="text-primary fw-bold">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</span> 
                            <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                            <span class="text-primary fw-bold">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</span>
                        </td>

                        <td>
                            <span class="text-truncate-custom" title="{{ $cuti->alasan }}">
                                {{ $cuti->alasan }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($cuti->berkas_bukti)
                                <a href="{{ $cuti->berkas_bukti }}" target="_blank" class="text-decoration-none">
                                    <img src="{{ $cuti->berkas_bukti }}" alt="Bukti" class="photo-thumb" title="Klik untuk lihat berkas">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada Berkas</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @php
                                $badgeClass = 'secondary';
                                $icon = '';
                                if($cuti->status == 'disetujui') {
                                    $badgeClass = 'success';
                                    $icon = 'bi-check-circle-fill';
                                } elseif($cuti->status == 'menunggu') {
                                    $badgeClass = 'warning text-dark';
                                    $icon = 'bi-clock-fill';
                                } elseif($cuti->status == 'ditolak') {
                                    $badgeClass = 'danger';
                                    $icon = 'bi-x-circle-fill';
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} px-3 py-2 text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="bi {{ $icon }} me-1"></i> {{ $cuti->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pengajuan cuti.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>