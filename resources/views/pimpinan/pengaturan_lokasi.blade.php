<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lokasi Kantor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 720px;">
        <h1 class="h4 mb-4">Pengaturan Lokasi Kantor</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('pimpinan.pengaturan-lokasi.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $pengaturan->latitude ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $pengaturan->longitude ?? '') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Radius (meter)</label>
                        <input type="number" name="radius" min="1" class="form-control" value="{{ old('radius', $pengaturan->radius ?? 100) }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
