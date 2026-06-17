<!DOCTYPE html>
<html>
<head>
    <title>Buat Slip Gaji - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; 
            transition: transform 0.3s ease-in-out; 
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

        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            background-color: rgba(255,255,255,0.2); 
            border-radius: 8px; 
            font-weight: 600;
            color: #fff;
        }

        .sidebar .nav-link.text-white-50:hover {
            color: #fff !important;
        }
        
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.95rem; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e0; padding: 10px 15px; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: #8f9fc4; box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15); }
        
        .sidebar-overlay {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show-mobile {
                transform: translateX(0); 
            }
            .content {
                margin-left: 0; 
                padding: 15px; 
            }
            .card-custom {
                padding: 20px !important; 
            }
            .sidebar-overlay.show {
                display: block; 
            }
        }
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <button class="btn text-white position-absolute top-0 end-0 mt-3 me-2 d-md-none fs-4" id="closeSidebarBtn">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    
    <ul class="nav flex-column mt-5">
        <li class="nav-item">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link {{ Request::is('pimpinan') || Request::is('pimpinan/dashboard*') ? 'active' : '' }}">
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
            <a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link {{ Request::is('pimpinan/karyawan-pending*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link {{ Request::is('pimpinan/pengaturan-lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Pengaturan Lokasi
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pimpinan.hari_libur') }}" class="nav-link {{ Request::is('pimpinan/hari-libur*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Hari Libur
            </a>
        </li>

        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light border" id="openSidebarBtn">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="fw-bold m-0" style="color: #2c3e50;">Buat Slip Gaji</h5>
        </div>
    </div>

    <div class="mb-4 d-none d-md-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Buat Slip Gaji</h2>
            <p class="text-muted m-0">Input data komponen gaji manual untuk karyawan.</p>
        </div>
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
        <div id="alertBelumDinilai" class="alert alert-warning border-warning border-opacity-50 shadow-sm rounded-4 d-none d-flex align-items-center" role="alert" style="background-color: #fffbeb;">
            <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>
            <div>
                <strong>PENGGAJIAN BERISIKO: Karyawan Belum Dinilai!</strong><br>
                <span class="small text-dark">Karyawan ini belum mendapatkan Penilaian Kinerja dari Kepala Bagian untuk periode bulan lalu. Komponen <strong>Tunjangan Kinerja</strong> dan <strong>Bonus Prestasi</strong> otomatis dikunci ke angka <strong>Rp 0</strong>.</span>
            </div>
        </div>
        <form action="{{ route('pimpinan.gaji.store') }}" method="POST" id="formGaji">
            @csrf
            
            <div class="alert alert-primary bg-primary bg-opacity-10 border-0 mb-4 d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-3 text-primary me-3"></i>
                <div>
                    <strong>Sistem Gaji Terintegrasi</strong><br>
                    <span class="small text-dark">Data Uang Makan, Bonus Prestasi, dan Potongan akan ditarik otomatis dari server berdasarkan Absensi dan Nilai. Anda masih bisa mengedit manual jika dibutuhkan.</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
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
                    <input type="month" id="periode" name="periode" class="form-control" required value="{{ date('Y-m') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border-end pe-md-4">
                    <h5 class="text-success fw-bold border-bottom pb-2 mb-3"><i class="bi bi-plus-circle me-2"></i>Komponen Penerimaan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control calc-penerimaan bg-light text-muted" value="0" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uang Makan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="uang_makan" id="uang_makan"
                            class="form-control calc-penerimaan bg-light text-muted"
                            readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Jabatan(Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="tunjangan_jabatan" id="tunjangan_jabatan" class="form-control calc-penerimaan" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Kinerja</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="insentif_kinerja"
                            id="insentif_kinerja"
                            class="form-control calc-penerimaan bg-light text-muted"
                            readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Program(Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="tunjangan_program" id="tunjangan_program" class="form-control calc-penerimaan" min="0" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bonus</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="bonus"
                            id="bonus"
                            class="form-control calc-penerimaan bg-light text-muted"
                            readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lain-lain (Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="lain_lain" id="lain_lain" class="form-control calc-penerimaan bg-light text-muted" readonly>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                    <h5 class="text-danger fw-bold border-bottom pb-2 mb-3"><i class="bi bi-dash-circle me-2"></i>Komponen Potongan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Potongan Absensi (Dari Izin/Alpha)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="potongan_absen"
                            id="potongan_absen"
                            class="form-control calc-potongan bg-light text-muted"
                            readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Pertama (Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="cash_bon" id="cash_bon" class="form-control calc-potongan bg-light text-muted" readonly >
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Kedua (Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="cash_bon_2" id="cash_bon_2" class="form-control calc-potongan bg-light text-muted" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Potongan BPJS Disnaker (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="potongan_bpjs" id="potongan_bpjs" class="form-control bg-light text-muted calc-potongan" value="25000" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan Lain-lain (Input)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="potongan_lain" id="potongan_lain" class="form-control calc-potongan" min="0" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-6 offset-md-6">
                    <div class="p-3 bg-light rounded border border-secondary text-end">
                        <h6 class="text-danger">Total Potongan: <span id="text_total_potongan">Rp 25.000</span></h6>
                        <h4 class="text-success fw-bold m-0 mt-2">Gaji Bersih: <span id="text_total_gaji">Rp -25.000</span></h4>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <input type="hidden" name="status_slip" value="draft">
                <button type="submit" id="btnSimpanSlip" class="btn btn-primary px-5 fw-bold w-100 w-md-auto"><i class="bi bi-save me-2"></i>Simpan Slip Gaji</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('show-mobile');
            overlay.classList.add('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('show');
        }

        if(openBtn) openBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
        
        const selectKaryawan = document.getElementById('id_karyawan');
        const selectPeriode = document.getElementById('periode');
        
        const inGajiPokok = document.getElementById('gaji_pokok');
        const inUangMakan = document.getElementById('uang_makan');
        const inKinerja = document.getElementById('insentif_kinerja');
        const inBonus = document.getElementById('bonus');
        const inPotAbsen = document.getElementById('potongan_absen');
        const inBpjs = document.getElementById('potongan_bpjs');
        const inJabatan = document.getElementById('tunjangan_jabatan');
        
        const alertBelumDinilai = document.getElementById('alertBelumDinilai');
        let statusBelumDinilai = false; // Variabel global untuk menyimpan status evaluasi

        function fetchFinansial() {
            const idKaryawan = selectKaryawan.value;
            const periode = selectPeriode.value; // YYYY-MM
            
            if(idKaryawan && periode) {
                const [tahun, bulan] = periode.split('-');
            
                const url = `/pimpinan/karyawan/${idKaryawan}/finansial?bulan=${bulan}&tahun=${tahun}`;
                fetch(url)
                    .then(res => {
                        if (!res.ok) throw new Error('Network error');
                        return res.json();
                    })
                    .then(data => {
                        const btnSubmit = document.getElementById('btnSimpanSlip');
                        
                        if (data.sudah_dibuat) {
                            alert('⚠️ PERINGATAN: Karyawan ini sudah memiliki slip gaji untuk periode tersebut. Anda tidak dapat membuat slip ganda.');
                            btnSubmit.disabled = true;
                            btnSubmit.innerHTML = '<i class="bi bi-x-circle me-2"></i>Slip Sudah Ada';
                            btnSubmit.classList.remove('btn-primary');
                            btnSubmit.classList.add('btn-secondary');
                            alertBelumDinilai.classList.add('d-none'); // Sembunyikan alert jika slip sudah ada
                            statusBelumDinilai = false;
                        } else {
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Slip Gaji';
                            btnSubmit.classList.remove('btn-secondary');
                            btnSubmit.classList.add('btn-primary');
                            
                            // Logika pemunculan Alert Kuning
                            statusBelumDinilai = data.belum_dinilai;
                            if (data.belum_dinilai) {
                                alertBelumDinilai.classList.remove('d-none');
                            } else {
                                alertBelumDinilai.classList.add('d-none');
                            }
                        }
                        
                        inGajiPokok.value = data.gaji_pokok || 0;
                        inUangMakan.value = data.uang_makan || 0;
                        inKinerja.value = data.insentif_kinerja || 0;
                        inBonus.value = data.bonus || 0;
                        inPotAbsen.value = data.potongan_absen || 0;
                        inBpjs.value = data.potongan_bpjs || 0;
                        
                        calculateTotal();
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        alert('Gagal sinkronisasi data master. Pastikan koneksi dan rute server benar.');
                    });
            }
        }

        selectKaryawan.addEventListener('change', fetchFinansial);
        selectPeriode.addEventListener('change', fetchFinansial);

        const inputsPenerimaan = document.querySelectorAll('.calc-penerimaan');
        const inputsPotongan = document.querySelectorAll('.calc-potongan');
        
        function calculateTotal() {
            let totalPenerimaan = 0;
            let totalPotongan = 0; 

            inputsPenerimaan.forEach(i => totalPenerimaan += parseFloat(i.value) || 0);
            inputsPotongan.forEach(i => totalPotongan += parseFloat(i.value) || 0);

            let bersih = totalPenerimaan - totalPotongan;
            
            document.getElementById('text_total_potongan').innerText = "Rp " + totalPotongan.toLocaleString('id-ID');
            document.getElementById('text_total_gaji').innerText = "Rp " + bersih.toLocaleString('id-ID');
        }

        inputsPenerimaan.forEach(input => input.addEventListener('input', calculateTotal));
        inputsPotongan.forEach(input => input.addEventListener('input', calculateTotal));
        
        calculateTotal();

        // TAMBAHAN: Mencegat submit form untuk memberikan konfirmasi ganda
        const formGaji = document.getElementById('formGaji');
        formGaji.addEventListener('submit', function(event) {
            if (statusBelumDinilai) {
                // Hentikan form sementara
                event.preventDefault(); 
                
                // Munculkan konfirmasi
                const isConfirmed = confirm("PENTING: Anda akan menyimpan slip gaji untuk karyawan yang belum dinilai kinerjanya. Komponen kinerja diatur Rp 0.\n\nApakah Anda yakin ingin memproses slip ini tanpa nilai kinerja?");
                
                // Jika setuju, lanjutkan submit
                if (isConfirmed) {
                    formGaji.submit();
                }
            }
        });
    });
</script>

@include('auth.logout')
</body>
</html>