<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: white; color: #000; padding: 30px; }
        .table th { background-color: #f1f5f9 !important; color: #334155 !important; font-weight: 600; border: 1px solid #cbd5e1; }
        .table td { border: 1px solid #cbd5e1; vertical-align: middle; }
        @media print {
            body { padding: 0; }
            @page { size: A4 landscape; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">LAPORAN DATA KARYAWAN</h2>
        <p class="text-muted">Departemen Operasional | Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}</p>
        @if($search)
            <span class="badge bg-secondary p-2">Kriteria Filter: "{{ $search }}"</span>
        @endif
    </div>

    <table class="table table-bordered w-100">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="25%">Nama Lengkap</th>
                <th width="15%">Divisi</th>
                <th width="15%">Kontak</th>
                <th width="25%">Alamat</th>
                <th width="15%" class="text-center">Status Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataKaryawan as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $user->nama_lengkap }}</td>
                <td class="text-capitalize">{{ $user->karyawan->divisi ?? '-' }}</td>
                <td>{{ $user->karyawan->no_hp ?? '-' }}</td>
                <td>{{ $user->karyawan->alamat ?? '-' }}</td>
                <td class="text-center text-capitalize">{{ $user->karyawan->status_karyawan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data karyawan yang sesuai dengan kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        <h5 class="fw-bold mb-3">Rekap Status Karyawan</h5>

        <table class="table table-bordered" style="width: 400px;">
            <tbody>
                <tr>
                    <th width="70%">Karyawan Aktif</th>
                    <td class="text-center fw-bold">
                        {{ $jumlahAktif }}
                    </td>
                </tr>

                <tr>
                    <th>Karyawan Pending</th>
                    <td class="text-center fw-bold">
                        {{ $jumlahPending }}
                    </td>
                </tr>

                <tr>
                    <th>Karyawan Keluar</th>
                    <td class="text-center fw-bold">
                        {{ $jumlahKeluar }}
                    </td>
                </tr>

                <tr class="table-secondary">
                    <th>Total Karyawan</th>
                    <td class="text-center fw-bold">
                        {{ $jumlahAktif + $jumlahPending + $jumlahKeluar }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        // Otomatis membuka jendela print/save PDF setelah halaman terload sempurna
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>