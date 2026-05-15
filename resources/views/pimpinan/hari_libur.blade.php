<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Hari Libur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* Sidebar styling disamakan dengan dashboard */
        .sidebar { width: 250px; min-height: 100vh; background-color: #8f9fc4; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100; }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px; }
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; display: flex; align-items: center; text-decoration: none; }
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600; color: #fff; }
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item">
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.hari_libur') }}" class="nav-link active">
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
    <h2 class="mb-4 fw-bold">Manajemen Hari Libur Nasional & Internal</h2>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary"></i> Tambah Hari Libur</h5>
                <hr>
                <form action="{{ route('pimpinan.hari_libur.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Libur</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Nama Libur</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Idul Fitri" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Hari Libur</button>
                </form>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> Daftar Hari Libur Terdaftar</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hariLibur as $index => $libur)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}</strong></td>
                                    <td>{{ $libur->keterangan }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('pimpinan.hari_libur.destroy', $libur->id_libur) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada hari libur yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('auth.logout')
</body>
</html>