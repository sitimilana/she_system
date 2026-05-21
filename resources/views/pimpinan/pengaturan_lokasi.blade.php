<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lokasi Kantor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            background: #f4f7f6; 
            font-family: 'Inter', sans-serif; 
            color: #333; 
        }

        /* SIDEBAR STRUKTUR BARU */
        .sidebar {
            width: 250px; 
            min-height: 100vh; 
            background-color: #8f9fc4;
            position: fixed; 
            left: 0; 
            top: 0; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.05); 
            z-index: 1045; /* DITAMBAHKAN: Diperbesar agar selalu di atas elemen lain saat mobile */
            transition: transform 0.3s ease-in-out; /* DITAMBAHKAN: Efek animasi geser */
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

        /* CONTENT LAMA (Ditambah transisi) */
        .content { 
            margin-left: 250px; 
            padding: 40px 50px; 
            transition: margin-left 0.3s ease; 
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: -0.5px;
        }

        .page-header p {
            color: #6c757d;
            font-size: 15px;
        }

        .custom-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            background: #fff;
            max-width: 800px;
        }

        .custom-card .card-body {
            padding: 35px;
        }

        /* Styling Form & Input */
        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            background-color: #fcfcfc;
            transition: all 0.2s;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #8f9fc4;
            box-shadow: 0 0 0 4px rgba(143, 159, 196, 0.15);
        }

        /* Area Map & Tombol Melayang */
        .map-wrapper {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            margin-bottom: 25px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.03);
        }

        #map {
            height: 350px;
            width: 100%;
            z-index: 1;
        }

        .btn-floating-location {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 400; /* Berada di atas layer peta Leaflet */
            background: white;
            color: #4a6fa5;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn-floating-location:hover {
            background: #4a6fa5;
            color: white;
            transform: translateY(-2px);
        }

        /* Area Radius Spesial */
        .radius-box {
            background: #f8f9fa;
            border: 1px dashed #adb5bd;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 30px;
        }

        /* Tombol Simpan Utama */
        .btn-save {
            background-color: #4a6fa5;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background-color: #385682;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(74, 111, 165, 0.3);
        }

        /* --- TAMBAHAN UNTUK MOBILE RESPONSIVE --- */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-mobile { transform: translateX(0); }
            .content { margin-left: 0 !important; padding: 15px !important; }
            .sidebar-overlay.show { display: block; }
            
            .custom-card .card-body { padding: 20px; } /* Perkecil padding dalam form di HP */
            .btn-floating-location { padding: 6px 12px; font-size: 11px; } /* Sesuaikan ukuran tombol map di HP */
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
            <a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link {{ Request::is('pimpinan/karyawan-pending*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pimpinan.pengaturan-lokasi') }}" class="nav-link {{ Request::is('pimpinan/pengaturan-lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Pengaturan Lokasi
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pimpinan.hari_libur') }}" class="nav-link {{ Request::is('pimpinan/hari-libur*') ? 'active' : '' }}">
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

    <div class="d-flex justify-content-between align-items-center mb-4 d-md-none bg-white p-3 rounded-3 shadow-sm border">
        <h5 class="fw-bold m-0" style="color: #2c3e50;">Pengaturan Lokasi</h5>
        <button class="btn btn-light border" id="openSidebarBtn">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>

    <div class="page-header d-none d-md-block">
        <h2>Pengaturan Lokasi Kantor</h2>
        <p>Tentukan titik koordinat pusat dan batas area presensi karyawan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="max-width: 800px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" style="max-width: 800px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card custom-card">
        <div class="card-body">
            <form method="POST" action="{{ route('pimpinan.pengaturan-lokasi.update') }}">
                @csrf
                @method('PUT')

                <div class="map-wrapper">
                    <button type="button" id="btn-current-loc" class="btn-floating-location">
                        <i class="bi bi-crosshair me-1"></i> Gunakan Lokasi Saya
                    </button>
                    <div id="map"></div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label"><i class="bi bi-geo text-primary me-1"></i> Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" id="input-lat" class="form-control" value="{{ old('latitude', $pengaturan->latitude ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-geo text-primary me-1"></i> Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" id="input-lng" class="form-control" value="{{ old('longitude', $pengaturan->longitude ?? '') }}" required>
                    </div>
                </div>

                <div class="radius-box">
                    <label class="form-label text-danger fs-6">
                        <i class="bi bi-bullseye me-1"></i> Jangkauan Radius Presensi
                    </label>

                    <div class="input-group mb-3">
                        <input type="number"
                               step="1"
                               min="1"
                               max="10000"
                               name="radius"
                               id="input-radius"
                               class="form-control form-control-lg border-secondary"
                               value="{{ old('radius', $pengaturan->radius ?? 100) }}"
                               required
                               placeholder="Contoh: 100">
                        <span class="input-group-text bg-light border-secondary fw-bold">METER</span>
                    </div>

                    <div class="form-text text-muted" style="font-size: 13px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Masukkan jarak dalam <b>METER</b>. Contoh:
                        <ul class="mt-2">
                            <li><b>100</b> = 100 meter</li>
                            <li><b>500</b> = 500 meter (½ km)</li>
                            <li><b>1000</b> = 1 kilometer</li>
                            <li><b>5000</b> = 5 kilometer</li>
                        </ul>
                    </div>

                    <!-- Visual Indicator -->
                    <div class="mt-3 p-2 bg-info bg-opacity-10 rounded" id="radius-preview">
                        Radius yang akan digunakan: <strong id="radius-display">100</strong> meter
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-save w-100">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('auth.logout')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- DITAMBAHKAN: LOGIKA MENU BURGER HP ---
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
        // ------------------------------------------

        // --- LOGIKA RADIUS INPUT REALTIME UPDATE & VALIDATION ---
        const radiusInput = document.getElementById('input-radius');
        const radiusDisplay = document.getElementById('radius-display');

        // Update display saat input berubah
        radiusInput.addEventListener('input', function() {
            radiusDisplay.textContent = this.value || '0';
        });

        // Validation sebelum submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const radius = parseFloat(radiusInput.value);

            if (isNaN(radius) || radius <= 0) {
                e.preventDefault();
                alert('❌ Radius harus lebih besar dari 0 meter!');
                radiusInput.focus();
                return false;
            }

            if (radius > 10000) {
                e.preventDefault();
                alert('❌ Radius tidak boleh lebih dari 10000 meter (10 km)!');
                radiusInput.focus();
                return false;
            }
        });
        // -------------------------------------------------------

        // --- LOGIKA MAPS (TIDAK DIUBAH) ---
        const latInput = document.getElementById('input-lat');
        const lngInput = document.getElementById('input-lng');
        const btnCurrentLoc = document.getElementById('btn-current-loc');

        // Koordinat default atau dari database
        let startLat = parseFloat(latInput.value) || -7.7509239; 
        let startLng = parseFloat(lngInput.value) || 111.9946412;

        const map = L.map('map').setView([startLat, startLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        function updateInputs(lat, lng) {
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
        }

        function updateMapFromInput() {
            let lat = parseFloat(latInput.value);
            let lng = parseFloat(lngInput.value);

            // Validasi agar map tidak error jika input dikosongkan sementara
            if (!isNaN(lat) && !isNaN(lng)) {
                let newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, map.getZoom()); // Menyesuaikan view tanpa mengubah level zoom
            }
        }

        // Mendaftarkan event listener ke kolom input
        latInput.addEventListener('input', updateMapFromInput);
        lngInput.addEventListener('input', updateMapFromInput);

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });

        // Fitur GPS Lokasi Saat Ini
        btnCurrentLoc.addEventListener('click', function() {
            if (navigator.geolocation) {
                const originalText = btnCurrentLoc.innerHTML;
                btnCurrentLoc.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...';
                btnCurrentLoc.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) { 
                        const currentLat = position.coords.latitude;
                        const currentLng = position.coords.longitude;
                        
                        const newLatLng = new L.LatLng(currentLat, currentLng);
                        marker.setLatLng(newLatLng);
                        map.setView(newLatLng, 17); 
                        
                        updateInputs(currentLat, currentLng);

                        btnCurrentLoc.innerHTML = originalText;
                        btnCurrentLoc.disabled = false;
                    }, 
                    function(error) { 
                        alert("Gagal mengambil lokasi GPS. Pastikan izin akses lokasi pada browser/HP Anda aktif.");
                        btnCurrentLoc.innerHTML = originalText;
                        btnCurrentLoc.disabled = false;
                    },
                    {
                        enableHighAccuracy: true, 
                        timeout: 10000 
                    }
                );
            } else {
                alert("Browser Anda tidak mendukung fitur lokasi.");
            }
        });
    });
</script>

</body>
</html>