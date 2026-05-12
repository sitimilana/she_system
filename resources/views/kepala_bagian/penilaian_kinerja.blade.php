<!DOCTYPE html>
<html>
<head>
    <title>Penilaian Kinerja - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
        }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link active"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
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
            <h2 class="fw-bold m-0" style="color: #1e293b;">Penilaian Kinerja</h2>
            <p class="text-muted m-0">Evaluasi kinerja bulanan karyawan di departemen Anda.</p>
        </div>
        <!-- Tombol Pemicu Modal -->
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPenilaian">
            <i class="bi bi-plus-circle me-2"></i>Tambah Penilaian
        </button>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Riwayat (Sekarang Full Width) -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="fw-bold m-0" style="color: #1e293b;"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Penilaian</h5>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchInputPenilaian" class="form-control border-start-0 ps-0" placeholder="Cari karyawan atau bulan...">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-custom m-0" id="tabelPenilaian">
                        <thead>
                            <tr>
                                <th width="20%">Bulan & Tahun</th>
                                <th width="35%">Nama Karyawan</th>
                                <th width="20%" class="text-center">Total Skor Akhir</th>
                                <th width="25%" class="text-center">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPenilaian as $rp)
                            <tr>
                                <td>
                                    @php
                                        setlocale(LC_TIME, 'id_ID');
                                        $monthName = \Carbon\Carbon::create()->month($rp->bulan)->translatedFormat('F');
                                    @endphp
                                    <span class="fw-medium">{{ $monthName }} {{ $rp->tahun }}</span>
                                </td>
                                <td>{{ $rp->karyawan->nama ?? 'Tidak Ditemukan' }}</td>
                                <td class="text-center fw-bold text-primary fs-5">{{ $rp->total_skor }}</td>
                                <td class="text-center">
                                    <!-- Menyesuaikan badge dengan skor 1-100 hasil konversi controller -->
                                    @if($rp->total_skor >= 90)
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Sangat Baik</span>
                                    @elseif($rp->total_skor >= 80)
                                        <span class="badge bg-primary px-3 py-2 rounded-pill">Baik</span>
                                    @elseif($rp->total_skor >= 60)
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Cukup</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">Kurang</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                                    Belum ada riwayat penilaian.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL TAMBAH PENILAIAN DITARUH DI SINI -->
<!-- ============================================== -->
<div class="modal fade" id="modalTambahPenilaian" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-clipboard-check me-2 text-primary"></i>Form Penilaian Kuisioner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('kabag.penilaian.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Karyawan</label>
                            <select name="id_karyawan" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Nama Karyawan --</option>
                                @foreach($karyawan as $k)
                                    <option value="{{ $k->id_karyawan }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Periode Penilaian</label>
                            <input type="month" name="periode" class="form-control form-control-lg" required value="{{ date('Y-m') }}">
                        </div>
                    </div>

                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i> Pilih skala <strong>1 (Sangat Kurang)</strong> hingga <strong>5 (Sangat Baik)</strong>. Skor akan dikalkulasi otomatis menjadi skala 100.
                    </div>
                    
                    <div class="table-responsive border rounded-3 mb-4">
                        <table class="table table-sm text-center align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start py-3 px-3" width="45%">Aspek Penilaian</th>
                                    <th class="py-3">1</th>
                                    <th class="py-3">2</th>
                                    <th class="py-3">3</th>
                                    <th class="py-3">4</th>
                                    <th class="py-3">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start px-3 py-2 fw-medium">Kedisiplinan & Kehadiran (20%)</td>
                                    <td><input class="form-check-input" type="radio" name="disiplin" value="1" required></td>
                                    <td><input class="form-check-input" type="radio" name="disiplin" value="2"></td>
                                    <td><input class="form-check-input" type="radio" name="disiplin" value="3"></td>
                                    <td><input class="form-check-input" type="radio" name="disiplin" value="4"></td>
                                    <td><input class="form-check-input" type="radio" name="disiplin" value="5"></td>
                                </tr>
                                <tr>
                                    <td class="text-start px-3 py-2 fw-medium">Produktivitas Target (30%)</td>
                                    <td><input class="form-check-input" type="radio" name="produktivitas" value="1" required></td>
                                    <td><input class="form-check-input" type="radio" name="produktivitas" value="2"></td>
                                    <td><input class="form-check-input" type="radio" name="produktivitas" value="3"></td>
                                    <td><input class="form-check-input" type="radio" name="produktivitas" value="4"></td>
                                    <td><input class="form-check-input" type="radio" name="produktivitas" value="5"></td>
                                </tr>
                                <tr>
                                    <td class="text-start px-3 py-2 fw-medium">Tanggung Jawab Pekerjaan (20%)</td>
                                    <td><input class="form-check-input" type="radio" name="tanggung_jawab" value="1" required></td>
                                    <td><input class="form-check-input" type="radio" name="tanggung_jawab" value="2"></td>
                                    <td><input class="form-check-input" type="radio" name="tanggung_jawab" value="3"></td>
                                    <td><input class="form-check-input" type="radio" name="tanggung_jawab" value="4"></td>
                                    <td><input class="form-check-input" type="radio" name="tanggung_jawab" value="5"></td>
                                </tr>
                                <tr>
                                    <td class="text-start px-3 py-2 fw-medium">Sikap Kerja & Perilaku (15%)</td>
                                    <td><input class="form-check-input" type="radio" name="sikap_kerja" value="1" required></td>
                                    <td><input class="form-check-input" type="radio" name="sikap_kerja" value="2"></td>
                                    <td><input class="form-check-input" type="radio" name="sikap_kerja" value="3"></td>
                                    <td><input class="form-check-input" type="radio" name="sikap_kerja" value="4"></td>
                                    <td><input class="form-check-input" type="radio" name="sikap_kerja" value="5"></td>
                                </tr>
                                <tr>
                                    <td class="text-start px-3 py-2 fw-medium">Loyalitas & Kerja Sama (15%)</td>
                                    <td><input class="form-check-input" type="radio" name="loyalitas" value="1" required></td>
                                    <td><input class="form-check-input" type="radio" name="loyalitas" value="2"></td>
                                    <td><input class="form-check-input" type="radio" name="loyalitas" value="3"></td>
                                    <td><input class="form-check-input" type="radio" name="loyalitas" value="4"></td>
                                    <td><input class="form-check-input" type="radio" name="loyalitas" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Catatan Evaluasi / Feedback (Opsional)</label>
                        <textarea name="catatan_evaluasi" class="form-control" rows="3" placeholder="Tambahkan catatan khusus untuk karyawan ini..."></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-save me-2"></i>Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('auth.logout')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Jika ada error validasi, otomatis buka modal lagi -->
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('modalTambahPenilaian'));
        myModal.show();
    });
</script>
@endif

<script>
    document.getElementById('searchInputPenilaian').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tabelPenilaian tbody tr');
        
        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) return; // Abaikan baris "Data Kosong"
            
            let text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>