<!DOCTYPE html>
<html>
<head>
    <title>Reward & Recognition - Pimpinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; color: #333; }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px; min-height: 100vh; background-color: #8f9fc4;
            position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 100;
        }
        .sidebar .logo { width: 140px; display: block; margin: 0 auto; margin-top: 20px;}
        .sidebar .logo img { width: 100px; }
        .sidebar .nav-link { color: #fff; font-size: 16px; padding: 12px 25px; margin: 4px 15px; transition: 0.3s;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.2); border-radius: 8px; font-weight: 600;}
        .sidebar .nav-link i { margin-right: 12px; font-size: 1.1rem; }
        
        /* CONTENT */
        .content { margin-left: 250px; padding: 40px; }
        .card-custom { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        
        /* TOP PERFORMER CARD */
        .performer-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white; border-radius: 16px; padding: 20px; position: relative; overflow: hidden;
        }
        .performer-card::after {
            content: "\F5A2"; font-family: "bootstrap-icons"; position: absolute;
            right: -10px; bottom: -20px; font-size: 8rem; color: rgba(255,255,255,0.05);
        }
        
        /* TABLE STYLES */
        .table-custom th { background-color: #f8fafc; color: #4a5568; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .form-check-input { width: 1.2em; height: 1.2em; cursor: pointer; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('storage/storage/images/logoshe.png') }}" alt="Logo">
    </div>
    <ul class="nav flex-column mt-5">
        <li class="nav-item"><a href="{{ route('pimpinan.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.gaji') }}" class="nav-link"><i class="bi bi-cash-stack"></i> Manajemen Gaji</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.cuti') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Manajemen Cuti</a></li>
        <li class="nav-item"><a href="{{ route('pimpinan.reward') }}" class="nav-link active"><i class="bi bi-gift"></i> Reward & Recognition</a></li>
        <li class="nav-item"><a href="{{ route('role.index') }}" class="nav-link"><i class="bi bi-person-gear"></i> Manajemen Role</a></li>
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
            <h2 class="fw-bold m-0" style="color: #1e293b;">Reward & Recognition</h2>
            <p class="text-muted m-0">Evaluasi dan berikan penghargaan untuk karyawan berprestasi.</p>
        </div>
        <button class="btn btn-outline-danger fw-bold shadow-sm"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF</button>
    </div>

    <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-star-fill text-warning me-2"></i>Kandidat Top Performer Bulan Ini</h5>
    <div class="row mb-5">
        @foreach($topKandidat as $kandidat)
        <div class="col-md-12 col-lg-6">
            <div class="performer-card shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center fw-bold fs-4 me-3" style="width: 50px; height: 50px;">
                        {{ substr($kandidat->karyawan->nama ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold">{{ $kandidat->karyawan->nama ?? '-' }}</h5>
                        <small class="text-white-50">{{ $kandidat->karyawan->user->role->nama_role ?? '-' }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-4">
                    <div>
                        <small class="d-block text-white-50">Skor Kinerja</small>
                        <h3 class="m-0 text-warning fw-bold">{{ $kandidat->total_skor }}/100</h3>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group" style="max-width: 350px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama karyawan...">
            </div>
            <button class="btn btn-light border shadow-sm"><i class="bi bi-funnel me-2"></i>Filter Data</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-custom m-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">Peringkat</th>
                        <th width="25%">Nama Karyawan</th>
                        <th width="20%">Jabatan</th>
                        <th width="20%" class="text-center">Periode Penilaian</th>
                        <th width="15%" class="text-center">Skor Kinerja</th>
                        <th width="15%" class="text-center">Status Reward</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarReward as $index => $reward)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $reward->karyawan->nama ?? '-' }}</td>
                        <td>{{ $reward->karyawan->user->role->nama_role ?? '-' }}</td>
                        <td class="text-center">{{ $reward->bulan }} / {{ $reward->tahun }}</td>
                        <td class="text-center fw-bold {{ $reward->total_skor >= 90 ? 'text-success' : 'text-dark' }}">{{ $reward->total_skor }}</td>
                        <td class="text-center">
                            @if($index == 0)
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-trophy-fill me-1"></i> Penerima Reward</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data evaluasi kinerja bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Script sederhana pencarian
</script>

@include('auth.logout')

</body>
</html>