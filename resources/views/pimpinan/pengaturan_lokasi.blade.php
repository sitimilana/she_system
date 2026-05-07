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

        /* ================= SIDEBAR (TIDAK DIUBAH) ================= */
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
        /* ========================================================== */

        /* KUSTOMISASI KONTEN UTAMA ESTETIK */
        .content { 
            margin-left: 250px; 
            padding: 40px 50px; 
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

        .form-control[readonly] {
            background-color: #f1f3f5;
            cursor: not-allowed;
            color: #6c757d;
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
            <a href="{{ route('pimpinan.karyawan_pending') }}" class="nav-link {{ Request::is('pimpinan/karyawan-pending*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Persetujuan Karyawan
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

    <div class="page-header">
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
                        <input type="number" step="0.0000001" name="latitude" id="input-lat" class="form-control" value="{{ old('latitude', $pengaturan->latitude ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-geo text-primary me-1"></i> Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" id="input-lng" class="form-control" value="{{ old('longitude', $pengaturan->longitude ?? '') }}" required readonly>
                    </div>
                </div>

                <div class="radius-box">
                    <label class="form-label text-danger fs-6"><i class="bi bi-bullseye me-1"></i> Jangkauan Radius Presensi (Kilometer)</label>
                    <input type="number" step="0.001" min="0.001" name="radius" class="form-control form-control-lg border-secondary" value="{{ old('radius', $pengaturan->radius ?? 0.1) }}" required>
                    <div class="form-text text-muted mt-2" style="font-size: 13px;">
                        Masukkan jarak dalam hitungan Kilometer (km). <br>
                        Contoh: <b class="text-dark">0.1</b> untuk 100 meter (m).
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