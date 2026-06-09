<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Hari Libur - Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR STRUKTUR SAMA DENGAN BERANDA */
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
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; display: flex; align-items: center; text-decoration: none;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600; color: #fff;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        .sidebar .nav-link.text-white-50:hover { color: #fff !important; }
        
        .content { margin-left: 250px; padding: 40px; transition: margin-left 0.3s ease; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 15px !important; }
            .sidebar-overlay { display: block; }
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
        <li class="nav-item"><a href="{{ route('akademik.beranda') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('akademik.absensi') }}" class="nav-link"><i class="bi bi-journal-check"></i> Riwayat Absensi</a></li>
        <li class="nav-item"><a href="{{ route('akademik.cuti') }}" class="nav-link"><i class="bi bi-calendar-range"></i> Riwayat Cuti</a></li>
        <li class="nav-item"><a href="{{ route('akademik.karyawan') }}" class="nav-link"><i class="bi bi-people"></i> Manajemen Karyawan</a></li>
        <li class="nav-item">
            <a href="{{ route('akademik.hari_libur') }}" 
            class="nav-link {{ Request::routeIs('akademik.hari_libur') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Kelola Hari Libur
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
    <h2 class="fw-bold mb-4" style="color: #1e293b;">Manajemen Hari Libur Akademik</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i> Tambah Libur</h5>
                <form action="{{ route('akademik.store_hari_libur') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Libur Semester" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Data</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3">Daftar Hari Libur</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th><th>Tanggal</th><th>Keterangan</th><th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hariLibur as $index => $libur)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($libur->tanggal)->format('d F Y') }}</td>
                                    <td>{{ $libur->keterangan }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" 
                                            onclick="setEditData('{{$libur->id_libur}}', '{{$libur->tanggal}}', '{{$libur->keterangan}}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('akademik.destroy_hari_libur', $libur->id_libur) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Hari Libur</h5></div>
                <div class="modal-body">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" class="form-control mb-3" required>
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" id="editKeterangan" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('auth.logout')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setEditData(id, tgl, ket) {
        document.getElementById('formEdit').action = "/akademik/hari-libur/" + id;
        document.getElementById('editTanggal').value = tgl;
        document.getElementById('editKeterangan').value = ket;
    }
</script>
</body>
</html>