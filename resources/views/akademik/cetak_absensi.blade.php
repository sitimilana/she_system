<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Karyawan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            font-family: Arial, sans-serif;
            background: white;
            color: #000;
            padding: 30px;
        }

        .table th{
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1;
        }

        .table td{
            border: 1px solid #cbd5e1;
        }

        @media print {

            body{
                padding: 0;
            }

            @page{
                size: A4 landscape;
                margin: 15mm;
            }
        }

    </style>
</head>
<body>

    <div class="text-center mb-4">

        <h2 class="fw-bold mb-1">
            LAPORAN RIWAYAT ABSENSI
        </h2>

        <p class="text-muted">
            Dicetak pada:
            {{ now()->translatedFormat('d F Y') }}
        </p>

        @if(request('bulan'))
            <div class="fw-bold">
                Periode:
                {{ \Carbon\Carbon::parse(request('bulan').'-01')->translatedFormat('F Y') }}
            </div>
        @endif

    </div>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Karyawan</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse($dataAbsensi as $index => $absen)

            <tr>

                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $absen->karyawan->nama ?? '-' }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}
                </td>

                <td>
                    {{ $absen->jam_masuk ?? '-' }}
                </td>

                <td>
                    {{ $absen->jam_pulang ?? '-' }}
                </td>

                <td class="text-capitalize">
                    {{ $absen->status }}
                </td>

            </tr>

            @empty

            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    Tidak ada data absensi.
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

    {{-- REKAP --}}
    <div class="mt-4">

        <h5 class="fw-bold mb-3">
            Rekap Absensi
        </h5>

        <table class="table table-bordered w-50">

            <tr>
                <th>Hadir</th>
                <td>{{ $jumlahHadir }}</td>
            </tr>

            <tr>
                <th>Terlambat</th>
                <td>{{ $jumlahTerlambat }}</td>
            </tr>

            <tr>
                <th>Izin</th>
                <td>{{ $jumlahIzin }}</td>
            </tr>

            <tr>
                <th>Sakit</th>
                <td>{{ $jumlahSakit }}</td>
            </tr>

            <tr>
                <th>Alfa</th>
                <td>{{ $jumlahAlfa }}</td>
            </tr>

            <tr>
                <th>Cuti</th>
                <td>{{ $jumlahCuti }}</td>
            </tr>

        </table>

    </div>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>

</body>
</html>