<!DOCTYPE html>
<html>
<head>
    <title>Edit Slip Gaji - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .content { margin-left: 250px; padding: 40px; }
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
    <div class="mb-4">
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
                        <label class="form-label text-secondary">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" class="form-control calc-penerimaan bg-light text-muted" name="gaji_pokok" id="gaji_pokok" value="{{ $gaji->gaji_pokok ?? 0 }}" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uang Makan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="uang_makan" id="uang_makan" value="{{ $gaji->uang_makan ?? 0 }}">
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
                            <input type="number" class="form-control calc-penerimaan" name="insentif_kinerja" id="insentif_kinerja" value="{{ $gaji->insentif_kinerja ?? 0 }}">
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
                        <label class="form-label">Bonus Rank 1 / Khusus</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="bonus" id="bonus" value="{{ $gaji->bonus ?? 0 }}">
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

                <div class="col-md-6 ps-md-4">
                    <h5 class="form-section-title"><i class="bi bi-dash-circle text-danger me-2"></i>Komponen Potongan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Potongan Absen (Izin/Alpha/Cuti)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="potongan_absen" id="potongan_absen" value="{{ $gaji->potongan_absen ?? 0 }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Pertama</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="cash_bon" value="{{ $gaji->cash_bon ?? 0 }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon Kedua</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="cash_bon_2" value="{{ $gaji->cash_bon_2 ?? 0 }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Potongan BPJS (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" class="form-control bg-light" name="potongan_bpjs" id="potongan_bpjs" value="{{ $gaji->potongan_bpjs ?? 25000 }}" readonly>
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

            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                <a href="{{ route('pimpinan.gaji') }}" class="btn btn-modern btn-outline-custom">Batal / Kembali</a>
                <button type="submit" class="btn btn-modern btn-primary-custom shadow-sm"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Kalkulasi di awal page load (karena ini halaman edit data yang sdh ada nilainya)
        calculateTotal();
    });
</script>
@include('auth.logout')
</body>
</html>