<!DOCTYPE html>
<html>
<head>
    <title>Edit Slip Gaji - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR STRUKTUR BARU */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; /* Di atas elemen lain */
            transition: transform 0.3s ease-in-out; /* Animasi geser */
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

        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .form-card { background-color: #ffffff; border-radius: 16px; padding: 40px; max-width: 900px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .form-section-title { color: #2c3e50; font-size: 1.15rem; font-weight: 700; border-bottom: 2px solid #f0f2f5; padding-bottom: 12px; margin-bottom: 24px; margin-top: 10px; }
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.9rem; margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e0; padding: 10px 15px; background-color: #f8fafc; transition: all 0.2s; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: #8f9fc4; box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15); }
        .input-group-text { background-color: #edf2f7; border: 1px solid #cbd5e0; color: #4a5568; font-weight: 600; border-radius: 8px 0 0 8px; }
        .btn-modern { border-radius: 8px; padding: 12px 30px; font-weight: 600; letter-spacing: 0.3px; transition: 0.3s; }
        .btn-primary-custom { background-color: #3b82f6; border: none; color: white; }
        .btn-primary-custom:hover { background-color: #2563eb; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);}
        .btn-outline-custom { border: 1px solid #cbd5e0; color: #4a5568; background: white;}
        .btn-outline-custom:hover { background-color: #f1f5f9; color: #1e293b; }

        /* --- TAMBAHAN UNTUK MOBILE RESPONSIVE --- */
        .sidebar-overlay {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); /* Sembunyikan sidebar ke kiri */
            }
            .sidebar.show-mobile {
                transform: translateX(0); /* Tampilkan saat menu di-klik */
            }
            .content {
                margin-left: 0; /* Hapus margin kiri di HP */
                padding: 15px; /* Perkecil padding content di HP */
            }
            .form-card {
                padding: 20px; /* Perkecil padding dalam form agar pas di layar kecil */
            }
            .sidebar-overlay.show {
                display: block; /* Tampilkan overlay gelap */
            }
        }
        /* --------------------------------------- */
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
        <div>
            <h5 class="fw-bold m-0" style="color: #2c3e50;">Edit Slip Gaji</h5>
        </div>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="mb-4 d-none d-md-block">
        <h2 class="fw-bold" style="color: #1e293b;">Edit Slip Gaji</h2>
        <p class="text-muted">Perbarui komponen penerimaan dan potongan gaji karyawan.</p>
    </div>

    <div class="form-card">
        <form action="{{ route('pimpinan.gaji.update', $gaji->id_gaji) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-7 mb-3 mb-md-0">
                    <label class="form-label">Nama Karyawan</label>
                    <select class="form-select" name="id_karyawan" id="id_karyawan" required>
                        <option value="" disabled>-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}" {{ $gaji->id_karyawan == $k->id_karyawan ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->divisi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Periode Penggajian</label>
                    @php $periodeValue = sprintf('%04d-%02d', $gaji->tahun, $gaji->bulan); @endphp
                    <input type="month" class="form-control" name="periode" id="periode" value="{{ $periodeValue }}" required> 
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 pe-md-4 border-end">
                    <h5 class="form-section-title"><i class="bi bi-plus-circle text-success me-2"></i>Komponen Penerimaan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Gaji Pokok (Otomatis dari Master)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" class="form-control calc-penerimaan bg-light text-muted" name="gaji_pokok" id="gaji_pokok" value="{{ $gaji->gaji_pokok ?? 0 }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Uang Makan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="uang_makan" value="{{ $gaji->uang_makan ?? 0 }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="tunjangan_jabatan" id="tunjangan_jabatan" value="{{ $gaji->tunjangan_jabatan ?? 0 }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tunjangan Kinerja</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="insentif_kinerja" value="{{ $gaji->insentif_kinerja ?? 0 }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Program</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="tunjangan_program" value="{{ $gaji->tunjangan_program ?? 0 }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Tunjangan BPJS (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" class="form-control calc-penerimaan bg-light text-muted" name="tunjangan_bpjs" id="tunjangan_bpjs" value="{{ $gaji->tunjangan_bpjs ?? 0 }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bonus</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="bonus" value="{{ $gaji->bonus ?? 0 }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lain-lain</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="lain_lain" value="{{ $gaji->lain_lain ?? 0 }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                    <h5 class="form-section-title"><i class="bi bi-dash-circle text-danger me-2"></i>Komponen Potongan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary">Potongan Absensi (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" class="form-control calc-potongan bg-light text-muted" name="potongan_absen" id="potongan_absen" value="{{ $gaji->potongan_absen ?? 0 }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary">Potongan BPJS Ketenagakerjaan (Konstanta)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rp</span>
                            <input type="number" class="form-control calc-potongan bg-light text-muted" name="potongan_bpjs" id="potongan_bpjs" value="{{ $gaji->potongan_bpjs ?? 25000 }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cash Bon (Pribadi)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="cash_bon" value="{{ $gaji->cash_bon ?? 0 }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cash Bon (Program)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="cash_bon_2" value="{{ $gaji->cash_bon_2 ?? 0 }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Potongan Lain-lain</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="potongan_lain" value="{{ $gaji->potongan_lain ?? 0 }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-6 offset-md-6">
                    <div class="p-3 bg-light rounded border border-secondary text-end">
                        <h6 class="text-danger">Total Potongan: <span id="text_total_potongan">Rp 0</span></h6>
                        <h4 class="text-success fw-bold m-0 mt-2">Gaji Bersih: <span id="text_total_gaji">Rp 0</span></h4>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top flex-wrap">
                <a href="{{ route('pimpinan.gaji') }}" class="btn btn-modern btn-outline-custom">Batal / Kembali</a>
                <button type="submit" class="btn btn-modern btn-primary-custom shadow-sm"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LOGIKA MENU BURGER HP ---
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
        // -----------------------------

        // --- LOGIKA KALKULASI GAJI (Tidak Diubah) ---
        const selectKaryawan = document.getElementById('id_karyawan');
        const selectPeriode = document.getElementById('periode');
        
        const inGajiPokok = document.getElementById('gaji_pokok');
        const inUangMakan = document.getElementById('uang_makan');
        const inKinerja = document.getElementById('insentif_kinerja');
        const inBonus = document.getElementById('bonus');
        const inPotAbsen = document.getElementById('potongan_absen');
        const inBpjs = document.getElementById('potongan_bpjs');

        function fetchFinansial() {
            const idKaryawan = selectKaryawan.value;
            const periode = selectPeriode.value; 
            
            if(idKaryawan && periode) {
                const [tahun, bulan] = periode.split('-');
                
                const url = `/pimpinan/karyawan/${idKaryawan}/finansial?bulan=${bulan}&tahun=${tahun}`;
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        inGajiPokok.value = data.gaji_pokok || 0;
                        inUangMakan.value = data.uang_makan || 0;
                        inKinerja.value = data.insentif_kinerja || 0;
                        inBonus.value = data.bonus || 0;
                        inPotAbsen.value = data.potongan_absen || 0;
                        inBpjs.value = data.potongan_bpjs || 0;
                        
                        calculateTotal();
                    })
                    .catch(error => console.error('Error fetching:', error));
            }
        }

        selectKaryawan.addEventListener('change', fetchFinansial);
        selectPeriode.addEventListener('change', fetchFinansial);

        // Auto Calculator logic
        const inputsPenerimaan = document.querySelectorAll('.calc-penerimaan');
        const inputsPotongan = document.querySelectorAll('.calc-potongan');
        
        function calculateTotal() {
            let totalPenerimaan = 0;
            let totalPotongan = parseFloat(inBpjs.value) || 0;

            inputsPenerimaan.forEach(i => totalPenerimaan += parseFloat(i.value) || 0);
            inputsPotongan.forEach(i => totalPotongan += parseFloat(i.value) || 0);

            let bersih = totalPenerimaan - totalPotongan;
            
            document.getElementById('text_total_potongan').innerText = "Rp " + totalPotongan.toLocaleString('id-ID');
            document.getElementById('text_total_gaji').innerText = "Rp " + bersih.toLocaleString('id-ID');
        }

        inputsPenerimaan.forEach(input => input.addEventListener('input', calculateTotal));
        inputsPotongan.forEach(input => input.addEventListener('input', calculateTotal));

        // Kalkulasi di awal page load
        calculateTotal();
    });
</script>
@include('auth.logout')
</body>
</html>