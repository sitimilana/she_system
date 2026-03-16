<!DOCTYPE html>
<html>
<head>
    <title>Detail Karyawan - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        .content { margin-left: 250px; padding: 40px; }
        .form-card {
            background-color: #ffffff; border-radius: 16px; padding: 40px; 
            max-width: 800px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05);
        }
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.9rem; margin-bottom: 6px;}
        .form-control, .form-select { border-radius: 8px; border: 1px solid #cbd5e0; padding: 10px 15px; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { background-color: #fff; border-color: #8f9fc4; box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15); }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('kabag.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('kabag.karyawan') }}" class="nav-link active"><i class="bi bi-people"></i> Kelola Karyawan</a></li>
        <li class="nav-item"><a href="{{ route('kabag.penilaian') }}" class="nav-link"><i class="bi bi-star"></i> Penilaian Kinerja</a></li>
        <li class="nav-item"><a href="{{ route('kabag.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item mt-4">
            <a href="{{ route('logout') }}" class="nav-link text-white-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</div>

<div class="content">
    <div class="mb-4 text-center">
        <a href="{{ route('kabag.karyawan') }}" class="btn btn-light border shadow-sm mb-3 float-start">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="fw-bold" style="color: #1e293b;">Kelengkapan Data Karyawan</h2>
        <p class="text-muted">Lengkapi biodata untuk akun: <span class="fw-bold text-primary">{{ $user->username }}</span></p>
    </div>

    <div class="form-card mt-5">
        <form action="{{ route('kabag.karyawan.store', $user->id_user) }}" method="POST">
            @csrf
            
            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-person-badge text-primary me-2"></i>Informasi Pekerjaan</h5>
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" value="{{ old('nama', $user->karyawan->nama ?? $user->nama_lengkap) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Divisi</label>
                    <input type="text" class="form-control" disabled readonly value="{{ $user->role->nama_role ?? '-' }}" style="background-color: #e9ecef; cursor: not-allowed;">
                    <small class="text-muted" style="font-size: 0.8rem;">(Divisi mengambil dari hak akses Role, tidak bisa diubah dari sini)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Karyawan</label>
                    <select class="form-select" name="status_karyawan" required>
                        <option value="Aktif" {{ (old('status_karyawan', $user->karyawan->status_karyawan ?? '') == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ (old('status_karyawan', $user->karyawan->status_karyawan ?? '') == 'Nonaktif') ? 'selected' : '' }}>Nonaktif</option>
                        <option value="Cuti" {{ (old('status_karyawan', $user->karyawan->status_karyawan ?? '') == 'Cuti') ? 'selected' : '' }}>Cuti</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold mb-4 mt-5 border-bottom pb-2"><i class="bi bi-telephone text-primary me-2"></i>Informasi Kontak</h5>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $user->karyawan->no_hp ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->karyawan->email ?? '') }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="3">{{ old('alamat', $user->karyawan->alamat ?? '') }}</textarea>
                </div>
            </div>

            <div class="d-grid mt-5">
                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Simpan Biodata
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>