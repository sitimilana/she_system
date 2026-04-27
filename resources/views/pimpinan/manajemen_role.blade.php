<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Role</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333; 
        }

        /* SIDEBAR STRUKTUR */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 100;
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

        /* LINK NAVIGASI */
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

        /* HOVER & ACTIVE STATE */
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            background-color: rgba(255,255,255,0.2); 
            border-radius: 8px; 
            font-weight: 600;
            color: #fff;
        }

        /* LOGOUT STYLE */
        .sidebar .nav-link.text-white-50:hover {
            color: #fff !important;
        }

        .content { 
            margin-left: 250px; 
            padding: 40px; 
            min-height: 100vh;
        }
        
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 16px; 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            margin-bottom: 30px;
        }
        
        .table-custom th { 
            background-color: #f8fafc; 
            color: #4a5568; 
            font-weight: 600; 
            border-bottom: 2px solid #e2e8f0; 
            padding: 15px 12px;
            white-space: nowrap;
        }

        .table-custom td { 
            vertical-align: middle; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 12px;
        }
        
        .search-bar { max-width: 350px; }
        .btn-action { padding: 8px 12px; border-radius: 8px; font-weight: 500; font-size: 0.85rem;}
        .role-badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="sidebar">
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
            <a href="{{ route('role.index') }}" class="nav-link {{ Request::is('role*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Manajemen Role
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link {{ Request::is('pimpinan/pengaturan-lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Pengaturan Lokasi
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Manajemen Role</h2>
            <p class="text-muted m-0">Kelola akun dan hak akses pengguna sistem.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah Role Terbaru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-people-fill d-inline-block me-2 text-primary"></i>Daftar Pengguna</h5>
            <form action="{{ route('role.index') }}" method="GET" class="input-group search-bar">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="searchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama atau role..." value="{{ request('search') }}">
            </form>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle m-0 table-custom">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="30%">Nama Lengkap</th>
                        <th width="20%" class="text-center">Role Akses</th>
                        <th width="25%">Username</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                        <td><div class="fw-bold text-dark">{{ $user->nama_lengkap ?? '-' }}</div></td>
                        <td class="text-center">
                            @php
                                $roleName = strtolower($user->role->nama_role ?? '');
                                $badgeClass = 'bg-secondary text-white';
                                
                                if($roleName == 'pimpinan') $badgeClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                elseif($roleName == 'kepala bagian') $badgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                elseif($roleName == 'akademik') $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                elseif($roleName == 'karyawan') $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                            @endphp
                            <span class="role-badge {{ $badgeClass }}">{{ $user->role->nama_role ?? '-' }}</span>
                        </td>
                        <td><span class="text-muted"><i class="bi bi-person-badge me-2"></i> {{ $user->username ?? '-' }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-light text-danger border btn-action shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#modalHapus"
                                data-id="{{ $user->id_user }}" title="Hapus Pengguna">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-2 d-block mb-3 text-black-50"></i>
                            Tidak ada pengguna yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="modalTambah">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <form action="{{ route('role.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-light border-bottom-0 rounded-top-4">
          <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-add me-2 text-primary"></i>Tambah Role Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">Pilih Jabatan (Role)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-briefcase"></i></span>
                    <select name="role" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Hak Akses --</option>
                        <option value="Pimpinan">Pimpinan</option>
                        <option value="Kepala Bagian">Kepala Bagian</option>
                        <option value="Akademik">Akademik</option>
                        <option value="Karyawan">Karyawan</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-at"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username unik" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                </div>
            </div>
            
        </div>

        <div class="modal-footer border-top-0 pt-0 pb-4 px-4 bg-white rounded-bottom-4">
          <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-1"></i> Simpan Data</button>
        </div>

      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHapus">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <form id="formHapus" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body p-5 text-center">
            <div class="text-danger mb-4">
                <i class="bi bi-exclamation-circle" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold mb-3">Hapus Pengguna?</h4>
            <p class="text-muted mb-4">Tindakan ini tidak dapat dibatalkan. Data pengguna akan dihapus secara permanen.</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4 py-2 text-dark font-weight-bold shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4 py-2 font-weight-bold shadow-sm">Ya, Hapus!</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function (e) {
        const id = e.relatedTarget.getAttribute('data-id');
        document.getElementById('formHapus').action = '/role/' + id;
    });

    // Real-time search/filter untuk tabel tanpa memuat ulang halaman
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filterValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableBody tr');

        rows.forEach(function(row) {
            // Abaikan baris "Data tidak ditemukan"
            if(row.children.length === 1) return;

            let rowText = row.innerText.toLowerCase();
            if(rowText.includes(filterValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@include('auth.logout')

</body>
</html>