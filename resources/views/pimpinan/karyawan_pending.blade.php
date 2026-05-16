<!DOCTYPE html>
<html>
<head>
    <title>Persetujuan Karyawan - Pimpinan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            background: #f4f7f6;
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #8f9fc4;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1045; /* Diperbesar untuk HP */
            transition: transform 0.3s ease-in-out; /* Animasi geser */
        }

        .sidebar .logo {
            width: 140px;
            display: block;
            margin: 0 auto;
            margin-top: 20px;
            text-align: center;
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

        /* ================= CONTENT ================= */
        .content {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* ================= CARD ================= */
        .card-custom {
            background-color: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* ================= TABLE ================= */
        .table-custom {
            margin-bottom: 0;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px 12px;
            white-space: nowrap;
            font-size: 0.92rem;
        }

        .table-custom td {
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 12px;
            color: #475569;
            font-size: 0.92rem;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
            transition: 0.2s;
        }

        /* ================= STATUS ================= */
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.82rem;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-active {
            background-color: rgba(25,135,84,0.1);
            color: #198754;
            border: 1px solid rgba(25,135,84,0.2);
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        /* ================= BUTTON ================= */
        .btn-review {
            background: #3b82f6;
            border: none;
            color: white;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-review:hover {
            background: #2563eb;
            transform: translateY(-1px);
            color: white;
        }

        /* ================= PAGINATION ================= */
        .pagination { gap: 6px; }
        .pagination .page-link {
            border: none; color: #475569; border-radius: 10px !important;
            padding: 8px 14px; font-weight: 500; box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }
        .pagination .page-link:hover { background-color: #dbeafe; color: #1d4ed8; }
        .pagination .page-item.active .page-link { background-color: #8f9fc4; color: #fff; }

        /* ================= MODAL ================= */
        .modal-content { border-radius: 20px; overflow: hidden; }
        .modal-header { background: #f8fafc; }
        .modal-title { font-weight: 700; color: #1e293b; }
        .info-title { font-weight: 700; font-size: 1rem; color: #1e293b; }
        .table-detail td { padding: 7px 0; vertical-align: top; border: none; }
        .table-detail .label { width: 40%; color: #64748b; font-size: 0.88rem; }
        .table-detail .value { font-weight: 500; color: #1e293b; }

        /* ================= EMPTY STATE ================= */
        .empty-state { padding: 50px 20px; color: #94a3b8; }
        .empty-state i { font-size: 2.5rem; margin-bottom: 10px; }

        /* --- TAMBAHAN UNTUK MOBILE RESPONSIVE --- */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 1040;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 20px 15px !important; }
            .sidebar-overlay.show { display: block; }
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
            <a href="{{ route('pimpinan.dashboard') }}"
               class="nav-link {{ Request::is('pimpinan') || Request::is('pimpinan/dashboard*') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.gaji') }}"
               class="nav-link {{ Request::is('pimpinan/gaji*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Manajemen Gaji
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.cuti') }}"
               class="nav-link {{ Request::is('pimpinan/cuti*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> Manajemen Cuti
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.reward') }}"
               class="nav-link {{ Request::is('pimpinan/reward*') ? 'active' : '' }}">
                <i class="bi bi-gift"></i> Reward & Recognition
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.karyawan_pending') }}"
               class="nav-link {{ Request::is('pimpinan/karyawan-pending*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.pengaturan-lokasi') }}"
               class="nav-link {{ Request::is('pimpinan/pengaturan-lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Pengaturan Lokasi
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pimpinan.hari_libur') }}"
               class="nav-link {{ Request::is('pimpinan/hari-libur*') ? 'active' : '' }}">
                <i class="bi bi-calendar-x"></i> Hari Libur
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="#" class="nav-link text-white-50"
               data-bs-toggle="modal"
               data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Persetujuan Karyawan</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="mb-4 d-none d-md-block">
        <h2 class="page-title">Persetujuan Karyawan Baru</h2>
        <p class="page-subtitle">
            Meninjau staf baru yang telah diinput Kepala Bagian.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="section-title m-0">
                <i class="bi bi-clock-history me-2 text-warning"></i>
                Data Menunggu Persetujuan
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="20%">Role / Hak Akses</th>
                        <th width="20%">Divisi</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->nama_lengkap }}</div>
                            <small class="text-muted">{{ $user->username }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $user->role->nama_role ?? '-' }}</span></td>
                        <td><span class="text-capitalize">{{ $user->karyawan->divisi ?? '-' }}</span></td>
                        <td class="text-center">
                            <span class="status-badge"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-review btn-sm rounded-3 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $user->id_user }}">
                                <i class="bi bi-search me-1"></i> Review
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center empty-state">
                            <i class="bi bi-inbox d-block"></i>
                            Tidak ada pengajuan karyawan baru saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(['approved_page' => request('approved_page')])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="section-title m-0">
                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                Riwayat Disetujui
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="20%">Role / Hak Akses</th>
                        <th width="20%">Divisi</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Tgl Disetujui</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedUsers as $index => $approved)
                    <tr>
                        <td class="text-center">{{ $approvedUsers->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $approved->nama_lengkap }}</div>
                            <small class="text-muted">{{ $approved->username }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $approved->role->nama_role ?? '-' }}</span></td>
                        <td><span class="text-capitalize">{{ $approved->karyawan->divisi ?? '-' }}</span></td>
                        <td class="text-center">
                            <span class="status-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        </td>
                        <td class="text-center text-muted">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $approved->updated_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center empty-state">
                            <i class="bi bi-person-check d-block"></i>
                            Belum ada riwayat karyawan yang disetujui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $approvedUsers->appends(['pending_page' => request('pending_page')])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<div class="modal fade" id="modalDetail{{ $user->id_user }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i> Detail Karyawan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-5">
                        <h6 class="info-title mb-3 border-bottom pb-2">Informasi Akun</h6>
                        <table class="table-detail w-100">
                            <tr><td class="label">Username</td><td class="value">: {{ $user->username }}</td></tr>
                            <tr><td class="label">Role Akses</td><td class="value">: <span class="badge bg-secondary">{{ $user->role->nama_role ?? '-' }}</span></td></tr>
                            <tr><td class="label">Tanggal Daftar</td><td class="value">: {{ $user->created_at->format('d M Y') }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-7 border-start">
                        <h6 class="info-title mb-3 border-bottom pb-2">Biodata Karyawan</h6>
                        <table class="table-detail w-100">
                            <tr><td class="label">Nama Lengkap</td><td class="value">: {{ $user->karyawan->nama ?? $user->nama_lengkap }}</td></tr>
                            <tr><td class="label">Divisi/Jabatan</td><td class="value">: {{ $user->karyawan->divisi ?? '-' }}</td></tr>
                            <tr><td class="label">Kontak</td><td class="value">: {{ $user->karyawan->no_hp ?? '-' }}</td></tr>
                            <tr><td class="label">Email</td><td class="value">: {{ $user->karyawan->email ?? '-' }}</td></tr>
                            <tr><td class="label">Alamat</td><td class="value">: {{ $user->karyawan->alamat ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-primary mt-4 border-0 bg-primary bg-opacity-10">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                        <div>
                            <strong class="text-primary">Keputusan Persetujuan</strong><br>
                            <small class="text-dark">Pastikan data di atas sudah benar. Jika disetujui, karyawan akan aktif dan dapat login.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0 pb-4 px-4 bg-white d-flex flex-wrap justify-content-between gap-2">
                <form action="{{ route('pimpinan.rejectKaryawan', $user->id_user) }}" method="POST" class="m-0 flex-grow-1 flex-md-grow-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100 px-4" onclick="return confirm('Tolak dan hapus data karyawan ini?')">
                        <i class="bi bi-x-lg me-1"></i> Tolak
                    </button>
                </form>
                <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0">
                    <button type="button" class="btn btn-light border px-4 flex-grow-1" data-bs-dismiss="modal">Tutup</button>
                    <form action="{{ route('pimpinan.approveKaryawan', $user->id_user) }}" method="POST" class="m-0 flex-grow-1">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success w-100 px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Setujui
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const overlay = document.getElementById('sidebarOverlay');

        if(openBtn) openBtn.addEventListener('click', () => { sidebar.classList.add('show-mobile'); overlay.classList.add('show'); });
        
        const closeSidebar = () => { sidebar.classList.remove('show-mobile'); overlay.classList.remove('show'); };
        
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);
    });
</script>

@include('auth.logout')
</body>
</html>