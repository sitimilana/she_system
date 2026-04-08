<!DOCTYPE html>
<html>
<head>
    <title>Form Gaji - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        
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
        
        /* FORM MODERN STYLES */
        .form-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .form-section-title {
            color: #2c3e50;
            font-size: 1.15rem;
            font-weight: 700;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 12px;
            margin-bottom: 24px;
            margin-top: 10px;
        }
        .form-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e0;
            padding: 10px 15px;
            background-color: #f8fafc;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: #8f9fc4;
            box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15);
        }
        .input-group-text {
            background-color: #edf2f7;
            border: 1px solid #cbd5e0;
            color: #4a5568;
            font-weight: 600;
            border-radius: 8px 0 0 8px;
        }
        .input-total {
            background-color: #f8fafc !important;
            font-weight: 800;
            font-size: 1.1rem;
            border: 2px dashed #cbd5e0;
        }
        
        /* BUTTONS */
        .btn-modern {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: 0.3s;
        }
        .btn-primary-custom { background-color: #3b82f6; border: none; color: white; }
        .btn-primary-custom:hover { background-color: #2563eb; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);}
        .btn-outline-custom { border: 1px solid #cbd5e0; color: #4a5568; background: white;}
        .btn-outline-custom:hover { background-color: #f1f5f9; color: #1e293b; }
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
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item"><a href="{{ route('kabag.gaji') }}" class="nav-link active"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item mt-4">
            <a href="{{ route('logout') }}" class="nav-link text-white-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #1e293b;">Pembuatan Slip Gaji</h2>
        <p class="text-muted">Masukkan detail komponen penerimaan dan potongan gaji karyawan.</p>
    </div>

    <div class="form-card">
        <form action="{{ route('kabag.gaji.store') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-7 mb-3 mb-md-0">
                    <label class="form-label">Nama Karyawan</label>
                    <select class="form-select" name="id_karyawan" required>
                        <option value="" disabled selected>-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Periode Penggajian</label>
                    <input type="month" class="form-control" name="periode" required> 
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 pe-md-4 border-end">
                    <h5 class="form-section-title"><i class="bi bi-plus-circle text-success me-2"></i>Komponen Penerimaan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="gaji_pokok" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uang Makan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="uang_makan" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Leader</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="tunjangan_jabatan" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Kinerja</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="insentif_kinerja" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan Program</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="tunjangan_program" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan BPJS</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="tunjangan_bpjs" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bonus</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="bonus" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lain-lain</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-penerimaan" name="lain_lain" value="0">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 ps-md-4">
                    <h5 class="form-section-title"><i class="bi bi-dash-circle text-danger me-2"></i>Komponen Potongan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Potongan Absen</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="potongan_absen" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cash Bon</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="cash_bon" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan BPJS</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="potongan_bpjs" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potongan Lain-lain</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control calc-potongan" name="potongan_lain" value="0">
                        </div>
                    </div>

                    <div class="mt-5 p-4 rounded bg-light border">
                        <div class="mb-3">
                            <label class="form-label text-danger">Total Potongan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-danger border-danger">Rp</span>
                                <input type="number" class="form-control input-total text-danger border-danger" id="total_potongan" value="0" readonly>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-success fs-5">Total Gaji Bersih</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white text-success border-success">Rp</span>
                                <input type="number" class="form-control input-total text-success border-success" id="total_gaji" name="total_gaji" value="0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                <a href="{{ route('kabag.gaji') }}" class="btn btn-modern btn-outline-custom">
                    <i class="bi bi-x-lg me-2"></i>Batal / Kembali
                </a>
                <button type="submit" class="btn btn-modern btn-primary-custom shadow-sm">
                    <i class="bi bi-save me-2"></i>Simpan Slip Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const inputsPenerimaan = document.querySelectorAll('.calc-penerimaan');
    const inputsPotongan = document.querySelectorAll('.calc-potongan');
    const totalPotonganEl = document.getElementById('total_potongan');
    const totalGajiEl = document.getElementById('total_gaji');

    function calculateTotal() {
        let totalPenerimaan = 0;
        let totalPotongan = 0;

        inputsPenerimaan.forEach(input => {
            const val = parseFloat(input.value) || 0;
            totalPenerimaan += val;
        });

        inputsPotongan.forEach(input => {
            const val = parseFloat(input.value) || 0;
            totalPotongan += val;
        });

        const gajiBersih = totalPenerimaan - totalPotongan;

        totalPotonganEl.value = totalPotongan;
        totalGajiEl.value = gajiBersih;
    }

    inputsPenerimaan.forEach(input => input.addEventListener('input', calculateTotal));
    inputsPotongan.forEach(input => input.addEventListener('input', calculateTotal));
</script>

</body>
</html>
