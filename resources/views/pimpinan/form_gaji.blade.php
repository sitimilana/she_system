<!DOCTYPE html>
<html>
<head>
    <title>Buat Slip Gaji - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100; }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.95rem; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e0; padding: 10px 15px; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: #8f9fc4; box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15); }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo"><img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo"></div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.gaji') }}" class="nav-link active"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.cuti') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Manajemen Cuti</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.reward') }}" class="nav-link"><i class="bi bi-gift"></i> Reward & Recognition</a></li>
        <li class="nav-item mt-4"><a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Buat Slip Gaji</h2>
            <p class="text-muted m-0">Input data komponen gaji manual untuk karyawan.</p>
        </div>
        <a href="{{ route('pimpinan.gaji') }}" class="btn btn-secondary shadow-sm fw-bold"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <form action="{{ route('pimpinan.gaji.store') }}" method="POST">
            @csrf
            
            <div class="alert alert-primary bg-primary bg-opacity-10 border-0 mb-4 d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-3 text-primary me-3"></i>
                <div>
                    <strong>Sistem Gaji Terintegrasi</strong><br>
                    <span class="small text-dark">Gaji Pokok & Tunjangan Tetap akan ditarik otomatis dari profil karyawan. Uang Makan, Insentif, dan Potongan Absen akan dihitung otomatis saat Anda klik Simpan.</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                    <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nama }} ({{ $k->divisi }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Periode Gaji (Bulan & Tahun) <span class="text-danger">*</span></label>
                    <input type="month" name="periode" class="form-control" required value="{{ date('Y-m') }}">
                </div>
            </div>

            <div class="row">
                <!-- Kolom PENERIMAAN -->
                <div class="col-md-6 border-end pe-4">
                    <h5 class="text-success fw-bold border-bottom pb-2 mb-3"><i class="bi bi-plus-circle me-2"></i>Komponen Penerimaan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Gaji Pokok (Otomatis dari Master)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control bg-light text-muted" value="0" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Tunjangan Jabatan (Otomatis dari Master)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" name="tunjangan_jabatan" id="tunjangan_jabatan" class="form-control bg-light text-muted" value="0" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Tunjangan BPJS (Otomatis dari Master)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" name="tunjangan_bpjs" id="tunjangan_bpjs" class="form-control bg-light text-muted" value="0" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Leader Kursus (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="tunjangan_leader" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Program / Lainnya (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="tunjangan_program" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bonus Tambahan Khusus (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="bonus" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lain-lain (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="lain_lain" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </div>

                <!-- Kolom POTONGAN -->
                <div class="col-md-6 ps-4">
                    <h5 class="text-danger fw-bold border-bottom pb-2 mb-3"><i class="bi bi-dash-circle me-2"></i>Komponen Potongan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Pertama (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="cash_bon" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Kedua (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="cash_bon_2" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan Lain-lain (Input Manual)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="potongan_lain" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <!-- Status otomatis menjadi 'draft' -->
                <input type="hidden" name="status_slip" value="draft">
                <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-save me-2"></i>Simpan & Hitung Otomatis</button>
            </div>
        </form>
    </div>
</div>

@include('auth.logout')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCRIPT AUTO-FILL JAVASCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectKaryawan = document.getElementById('id_karyawan');
        const inputGajiPokok = document.getElementById('gaji_pokok');
        const inputTunjJabatan = document.getElementById('tunjangan_jabatan');
        const inputTunjBpjs = document.getElementById('tunjangan_bpjs');

        selectKaryawan.addEventListener('change', function() {
            const idKaryawan = this.value;
            
            if(idKaryawan) {
                // Nolkan sementara saat proses menarik data
                inputGajiPokok.value = 0;
                inputTunjJabatan.value = 0;
                inputTunjBpjs.value = 0;
                
                // Ambil data ke server
                const url = `/pimpinan/karyawan/${idKaryawan}/finansial`;
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Isi data otomatis berdasarkan respon server
                        inputGajiPokok.value = data.gaji_pokok || 0;
                        inputTunjJabatan.value = data.tunjangan_jabatan || 0;
                        inputTunjBpjs.value = data.tunjangan_bpjs || 0;
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        inputGajiPokok.value = 0;
                        inputTunjJabatan.value = 0;
                        inputTunjBpjs.value = 0;
                        alert('Gagal mengambil data finansial master. Pastikan koneksi dan route benar.');
                    });
            }
        });
    });
</script>

</body>
</html>