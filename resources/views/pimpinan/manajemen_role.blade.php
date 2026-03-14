<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Role</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background:#eaeaea; }

        .sidebar{
            width:250px;
            min-height:100vh;
            background:#8f9fc4;
            position:fixed;
        }

        .content{
            margin-left:260px;
            padding:40px;
        }

        .sidebar .nav-link{
            color:black;
            font-size:18px;
        }

        .sidebar .nav-link:hover{
            background:rgba(255,255,255,0.3);
            border-radius:10px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3">
    <img src="{{ asset('images/logo-she.png') }}" width="120" class="d-block mx-auto mb-4">

    <ul class="nav flex-column">
        <li class="nav-item"><a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house"></i> Home</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cash"></i> Manajemen Gaji</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-calendar-check"></i> Manajemen Cuti</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-gift"></i> Reward & Recognition</a></li>
        <li class="nav-item"><a href="{{ route('role.index') }}" class="nav-link fw-bold"><i class="bi bi-person-gear"></i> Manajemen Role</a></li>
    </ul>
</div>

<!-- Content -->
<div class="content">

    <h2 class="fw-bold mb-4">Manajemen Role</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Button Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah Role
    </button>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th></th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    @php $user = $role->users->first(); @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->nama_lengkap ?? '-' }}</td>
                        <td>{{ $role->nama_role }}</td>
                        <td>{{ $user->username ?? '-' }}</td>
                        <td>{{ $user ? '********' : '-' }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalHapus"
                                data-id="{{ $role->role_id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('role.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="Pimpinan">Pimpinan</option>
                    <option value="Kepala Bagian">Kepala Bagian</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Karyawan">Karyawan</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formHapus" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Apakah anda yakin ingin menghapus role ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-danger">Ya</button>
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
</script>
</body>
</html>