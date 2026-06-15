<!DOCTYPE html>
<html>
<head>
    <title>Kelola Karyawan - Kepala Bagian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 1050;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s; text-decoration: none;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600; color: #fff;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: 0.9rem; padding: 12px 10px; white-space: nowrap; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; padding: 12px 10px; }
        
        .alamat-text { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .action-buttons { display: flex; gap: 6px; justify-content: center; flex-wrap: nowrap; }

        /* FORM AESTHETIC STYLES */
        .form-control, .form-select { padding: 0.6rem 1rem; border-radius: 0.5rem; border-color: #e2e8f0; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #8f9fc4; box-shadow: 0 0 0 3px rgba(143, 159, 196, 0.15); }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #475569; margin-bottom: 0.4rem; }
        .modal-section { background-color: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; height: 100%; }
        
        /* RESPONSIVE MODAL FIX */
        @media (max-width: 768px) {
            .modal-dialog { margin: 10px; }
            .modal-content { max-height: 90vh; }
            .modal-footer { padding: 15px !important; }
            .modal-footer .btn { width: 100%; margin: 0 !important; }
            .modal-footer { flex-direction: column-reverse; gap: 10px; }
        }

        .modal-section-title { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 1.2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; }

        .pagination { margin-bottom: 0; gap: 4px; }
        .pagination .page-link { padding: 0.35rem 0.5rem !important; font-size: 0.85rem !important; line-height: 1.4 !important; border: 1px solid #dee2e6 !important; border-radius: 6px !important; color: #475569; }
        .pagination .page-link:hover { background-color: #f1f5f9 !important; border-color: #8f9fc4 !important; }
        .pagination .active .page-link { background-color: #8f9fc4 !important; border-color: #8f9fc4 !important; color: white !important;}

        @media print {
            .sidebar, #searchInput, .input-group-text, .btn, .pagination, .alert { display: none !important; }
            .content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; }
            .card-custom { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }

        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1040; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 15px !important; }
            .sidebar-overlay.show { display: block; }
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
            <a href="{{ route('kabag.dashboard') }}" class="nav-link {{ request()->routeIs('kabag.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.karyawan') }}" class="nav-link {{ request()->routeIs('kabag.karyawan') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kelola Karyawan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.penilaian') }}" class="nav-link {{ request()->routeIs('kabag.penilaian') ? 'active' : '' }}">
                <i class="bi bi-star"></i> Penilaian Kinerja
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.riwayat_absensi') }}" class="nav-link {{ request()->routeIs('kabag.riwayat_absensi') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Riwayat Absensi
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kabag.riwayat_cuti') }}" class="nav-link {{ request()->routeIs('kabag.riwayat_cuti') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> Riwayat Cuti
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50 px-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Data Karyawan</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 d-none d-md-flex">
        <div>
            <h2 class="fw-bold m-0" style="color: #1e293b;">Data Karyawan</h2>
            <p class="text-muted m-0">Daftar staf dan karyawan di departemen Anda.</p>
        </div>
    </div>

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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <form method="GET" action="{{ route('kabag.karyawan') }}" class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, divisi, atau status..." value="{{ request('search') }}">
            </div>
        
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-light border shadow-sm"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('kabag.karyawan.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-secondary shadow-sm">
                    <i class="bi bi-printer me-2"></i>Cetak
                </a>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBaru"><i class="bi bi-plus-lg me-1"></i> Tambah Baru</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-custom m-0" id="tabelKaryawan">
                <thead>
                    <tr>
                        <th width="3%" class="text-center">No</th>
                        <th width="15%">Nama Lengkap</th>
                        <th width="10%">Divisi</th>
                        <th width="10%">Tgl Masuk</th>
                        <th width="10%">Kontak</th>
                        <th width="20%">Alamat</th>
                        <th width="10%" class="text-center">Cuti</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataKaryawan as $index => $user)
                    <tr>
                        <td class="text-center">{{ $dataKaryawan->firstItem() + $index }}</td>
                        <td class="fw-bold" style="white-space: nowrap;">{{ $user->nama_lengkap }}</td>
                        <td class="text-capitalize">{{ $user->karyawan->divisi ?? '-' }}</td>
                        <td>
                            @if($user->karyawan && $user->karyawan->tanggal_masuk)
                                <small class="text-muted">{{ date('d/m/y', strtotime($user->karyawan->tanggal_masuk)) }}</small>
                            @else
                                <small class="text-danger fw-bold">Belum diisi</small>
                            @endif
                        </td>
                        <td>{{ $user->karyawan->no_hp ?? '-' }}</td>
                        <td>
                            <div class="alamat-text" title="{{ $user->karyawan->alamat ?? '-' }}">
                                {{ $user->karyawan->alamat ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @php $sisaCuti = $user->karyawan->sisa_cuti ?? 0; @endphp
                            <span class="badge {{ $sisaCuti > 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ $sisaCuti }} Hari
                            </span>
                        </td>
                        <td class="text-center">
                            @if($user->karyawan && $user->karyawan->status_karyawan)
                                <span class="badge bg-{{ strtolower($user->karyawan->status_karyawan) == 'aktif' ? 'success' : (strtolower($user->karyawan->status_karyawan) == 'pending' ? 'warning text-dark' : 'danger') }} px-3 py-2 rounded-pill text-capitalize">
                                    {{ $user->karyawan->status_karyawan }}
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Belum Dilengkapi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning text-white shadow-sm px-2" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id_user }}" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('kabag.karyawan.reset_password', $user->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mereset password akun ini ke bawaan (shekediri123)?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info text-white shadow-sm px-2" title="Reset Password">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                </form>

                                @php $statusKar = strtolower($user->karyawan->status_karyawan ?? ''); @endphp
                                @if(in_array($statusKar, ['tidak aktif', 'keluar', 'resign']))
                                    <form action="{{ route('kabag.karyawan.destroy', $user->id_user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini secara permanen?')" title="Hapus Permanen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $user->id_user }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="modal-header bg-light border-bottom px-4 py-3">
                                    <h5 class="modal-title text-warning fw-bold"><i class="bi bi-pencil-square me-2"></i>Ubah Data Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('kabag.karyawan.update', $user->id_user) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Divisi</label>
                                                <select name="divisi" class="form-select" required>
                                                    <option value="keuangan" {{ ($user->karyawan->divisi ?? '') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                                    <option value="admin umum" {{ ($user->karyawan->divisi ?? '') == 'admin umum' ? 'selected' : '' }}>Admin Umum</option>
                                                    <option value="akademik" {{ ($user->karyawan->divisi ?? '') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                                    <option value="marketing" {{ ($user->karyawan->divisi ?? '') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                                    <option value="office boy" {{ ($user->karyawan->divisi ?? '') == 'office boy' ? 'selected' : '' }}>Office Boy</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Tanggal Masuk</label>
                                                <input type="date" name="tanggal_masuk" class="form-control" value="{{ $user->karyawan->tanggal_masuk ?? '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nomor HP</label>
                                                <input type="text" name="no_hp" class="form-control" value="{{ $user->karyawan->no_hp ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Sisa Cuti</label>
                                                <input type="number" name="sisa_cuti" class="form-control" value="{{ $user->karyawan->sisa_cuti ?? 12 }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->karyawan->email ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status Karyawan</label>
                                                <select name="status_karyawan" class="form-select" required>
                                                    <option value="aktif" {{ ($user->karyawan->status_karyawan ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="pending" {{ ($user->karyawan->status_karyawan ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="tidak aktif" {{ in_array(strtolower($user->karyawan->status_karyawan ?? ''), ['tidak aktif', 'keluar', 'resign']) ? 'selected' : '' }}>Tidak Aktif / Keluar</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Alamat</label>
                                                <textarea name="alamat" class="form-control" rows="2">{{ $user->karyawan->alamat ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer p-3 bg-light border-top d-flex flex-row justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-white px-4 rounded-pill shadow-sm">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-black-50"></i>
                            Tidak ada data karyawan di departemen ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dataKaryawan->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $dataKaryawan->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalTambahBaru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h5 class="modal-title text-primary fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Daftarkan Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kabag.karyawan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                         </div>
                         <div class="col-md-6">
                            <label class="form-label">Role Akses</label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $r)
                                    @if(strtolower($r->nama_role) != 'pimpinan')
                                        <option value="{{ $r->role_id }}">{{ $r->nama_role }}</option>
                                    @endif
                                @endforeach
                            </select>
                         </div>
                         <div class="col-md-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                         </div>
                         <div class="col-md-6">
                            <label class="form-label">Divisi</label>
                            <select name="divisi" class="form-select" required>
                                <option value="keuangan">Keuangan</option>
                                <option value="admin umum">Admin Umum</option>
                                <option value="akademik">Akademik</option>
                                <option value="marketing">Marketing</option>
                                <option value="office boy">Office Boy</option>
                            </select>
                         </div>
                         <div class="col-md-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" required>
                         </div>
                         <div class="col-md-6">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control">
                         </div>
                         <div class="col-md-6">
                            <label class="form-label">Sisa Cuti</label>
                            <input type="number" name="sisa_cuti" class="form-control" value="12">
                         </div>
                         <div class="col-md-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                         </div>
                         <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                         </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light border-top d-flex flex-row justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">Daftarkan Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
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

        // PERBAIKAN EXTRA: Logika Otomatis Submit Pencarian Saat Mengetik (Debounce)
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let timer;
            searchInput.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    searchInput.closest('form').submit();
                }, 700);
            });

            // Menjaga kursor tetap fokus di akhir teks setelah reload halaman
            if(searchInput.value) {
                searchInput.focus();
                let val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }
    });
</script>

@include('auth.logout')

</body>
</html> 