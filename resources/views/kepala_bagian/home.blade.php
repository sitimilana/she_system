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
            <a href="#" class="nav-link text-white-50 px-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
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
            <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(120deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Progress Evaluasi Kinerja</h5>
                        <p class="text-muted m-0 small">Progress Evaluasi: <span class="fw-bold text-dark">{{ $evaluasiSelesai ?? 0 }} dari {{ $jumlahKaryawan ?? 0 }}</span> Karyawan Selesai</p>
                    </div>
                    <a href="{{ route('kabag.penilaian') }}" class="btn btn-primary shadow-sm px-4 py-2 rounded-pill fw-medium" style="font-size: 0.9rem;">
                        Lanjutkan Evaluasi <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                @php
                    $persentase = ($jumlahKaryawan > 0) ? round((($evaluasiSelesai ?? 0) / $jumlahKaryawan) * 100) : 0;
                @endphp
                <div class="progress bg-secondary bg-opacity-10 mt-1" style="height: 12px; border-radius: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-8 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-activity text-info me-2"></i>Ringkasan Aktivitas SDM & Evaluasi</h5>
                </div>

                <div>
                    <div class="list-item-custom d-flex align-items-center bg-light border-0 mb-3 shadow-sm">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center fs-5 me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Karyawan Baru Terdaftar</h6>
                            <p class="text-muted small m-0">Terdapat <strong class="text-dark">{{ $karyawanBaru ?? 0 }}</strong> karyawan baru yang bergabung di divisi Anda bulan ini.</p>
                        </div>
                    </div>

                    @forelse($penilaian as $p)
                    <div class="list-item-custom d-flex align-items-center bg-light border-0 mb-3 shadow-sm hover-effect">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center fs-5 me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Penilaian Kinerja Disimpan</h6>
                            <p class="text-muted m-0" style="font-size: 0.85rem;">
                                Anda baru saja mengevaluasi <strong>{{ $p->karyawan->nama ?? 'Nama Tidak Diketahui' }}</strong> pada periode {{ $p->bulan ?? '-' }}-{{ $p->tahun ?? '-' }} dengan hasil skor <strong class="text-primary">{{ $p->total_skor ?? '-' }}</strong>.
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                        Belum ada riwayat aktivitas terbaru.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-ui-checks text-primary me-2"></i>Daftar Evaluasi Tim</h5>
                    <a href="{{ route('kabag.karyawan') }}" class="btn btn-sm btn-light border small py-1 px-2 fw-medium text-muted rounded-pill hover-effect">Lihat Detail <i class="bi bi-chevron-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>

                <div class="d-flex flex-column gap-3 overflow-auto" style="max-height: 380px; padding-right: 5px;">
                    @forelse($karyawan as $k)
                        @php
                            // Cek status sementara untuk demo UI. Anda dapat menghubungkan logika aslinya dari Controller.
                            $sudahSelesai = $k->penilaian->count() > 0 ?? false;
                            $skorKaryawan = $sudahSelesai ? ($k->penilaian->first()->total_skor ?? 0) : null;
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 transition hover-effect" style="border: 1px solid {{ $sudahSelesai ? '#dcfce7' : '#f1f5f9' }}; background-color: {{ $sudahSelesai ? '#f8fafc' : '#ffffff' }}; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-{{ $sudahSelesai ? 'success opacity-75' : 'secondary opacity-25' }} text-white rounded d-flex justify-content-center align-items-center fw-bold" style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ substr($k->nama ?? 'U', 0, 1) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold mb-1" style="font-size: 0.95rem;">{{ $k->nama }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">Divisi Anda</span>
                                </div>
                            </div>
                            <div>
                                @if($sudahSelesai)
                                    <div class="bg-success bg-opacity-25 text-success rounded-pill px-3 py-1 fw-bold border border-success border-opacity-25 shadow-sm d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-check-circle-fill"></i> Skor: {{ $skorKaryawan }}
                                    </div>
                                @else
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-medium border border-danger border-opacity-25 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-x-circle-fill"></i> Belum
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted small py-4 bg-light rounded-3">
                            <i class="bi bi-emoji-frown fs-4 pb-2 d-block text-black-50"></i>
                            Tidak ada karyawan ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

@include('auth.logout')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>