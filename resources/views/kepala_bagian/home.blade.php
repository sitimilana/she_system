<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333;
        }

        /* SIDEBAR (Konsisten dengan halaman lain) */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 15px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; border-radius: 8px;}
        .sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { background-color: rgba(255,255,255,0.3); font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }

        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        
        /* CARDS MODERN */
        .card-custom { 
            background-color: #ffffff; border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.02); 
            transition: 0.3s;
        }
        .card-custom:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-2px); }
        
        /* WIDGET STATISTIK */
        .stat-widget { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; overflow: hidden; position: relative;}
        .stat-widget::after {
            content: "\F4E1"; font-family: "bootstrap-icons"; position: absolute;
            right: -10px; bottom: -20px; font-size: 8rem; color: rgba(255,255,255,0.05);
        }
        .stat-value { font-size: 3.5rem; font-weight: 800; line-height: 1; margin-bottom: 5px;}
        
        /* LIST STYLES */
        .list-item-custom {
            border: 1px solid #f1f5f9; border-radius: 12px; padding: 15px; 
            margin-bottom: 15px; background: #fafafa; transition: 0.2s;
        }
        .list-item-custom:hover { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        
        /* MODAL CUSTOM */
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-header { border-bottom: 1px solid #f1f5f9; }
        .modal-footer { border-top: 1px solid #f1f5f9; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item">
            <a href="{{ route('kabag.dashboard') }}" class="nav-link active"><i class="bi bi-house-door"></i> Home</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a>
        </li>
        <li class="nav-item mt-5 pt-3 border-top border-light border-opacity-25 mx-3">
            <a href="#" class="nav-link text-white-50 px-3" onclick="confirmLogout(event)">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    
    <div class="mb-5">
        <h2 class="fw-bold m-0" style="color: #1e293b;">Selamat Datang, Kepala Bagian</h2>
        <p class="text-muted">Berikut adalah ringkasan operasional departemen Anda hari ini.</p>
    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card card-custom stat-widget p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-white-50 fw-medium mb-1">Total Karyawan Divisi</p>
                        <div class="stat-value">{{ $jumlahKaryawan ?? 0 }}</div>
                        <span class="badge bg-success bg-opacity-25 text-light mt-2"><i class="bi bi-arrow-up-short"></i> Aktif Beroperasi</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card card-custom p-4 h-100 d-flex justify-content-center" style="background: linear-gradient(120deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="d-flex align-items-center">
                    <div class="bg-white p-3 rounded-circle shadow-sm me-4 text-primary fs-3">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Waktunya Evaluasi Bulanan</h5>
                        <p class="text-muted m-0 small">Pastikan Anda telah mengisi penilaian kinerja untuk seluruh staf sebelum akhir bulan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-8 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-star-half text-warning me-2"></i>Penilaian Kinerja Terkini</h5>
                    <select class="form-select form-select-sm w-auto shadow-sm cursor-pointer">
                        <option>Bulan Ini</option>
                        <option>Bulan Lalu</option>
                    </select>
                </div>

                <div>
                    @forelse($penilaian as $p)
                    <div class="list-item-custom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 45px; height: 45px;">
                                {{ substr($p->nama ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold m-0">{{ $p->nama ?? 'Nama Tidak Diketahui' }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i> 
                                    {{ $p->tanggal_mulai ?? '-' }} s/d {{ $p->tanggal_selesai ?? '-' }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-{{ ($p->status ?? '') == 'Selesai' ? 'success' : 'warning text-dark' }} rounded-pill px-3 py-2">
                                {{ $p->status ?? 'Menunggu' }}
                            </span>
                            <button class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10" title="Lihat Detail"><i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                        Belum ada data penilaian terkini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-people text-primary me-2"></i>Tim Anda</h5>
                    <a href="{{ route('kabag.karyawan') }}" class="text-decoration-none small fw-semibold">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle">
                        <tbody>
                            @forelse($karyawan as $k)
                            <tr>
                                <td width="10%" class="text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="fw-medium text-dark">{{ $k->nama }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted small py-3">Tidak ada data karyawan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <div class="text-danger mb-3">
            <i class="bi bi-box-arrow-right" style="font-size: 3rem;"></i>
        </div>
        <h5 class="fw-bold mb-3">Konfirmasi Logout</h5>
        <p class="text-muted mb-4 small">Apakah Anda yakin ingin keluar dari sesi aplikasi saat ini?</p>
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light fw-bold w-100" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger fw-bold w-100 shadow-sm" onclick="document.getElementById('logout-form').submit()">Ya, Keluar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmLogout(e) {
        e.preventDefault();
        var modal = new bootstrap.Modal(document.getElementById('logoutModal'));
        modal.show();
    }
</script>

</body>
</html>