<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = Karyawan::where('status_karyawan', 'aktif')->get();

        if ($karyawans->isEmpty()) {
            return;
        }

        Storage::disk('public')->makeDirectory('berkas_cuti');

        foreach ($karyawans as $index => $karyawan) {
            if ($index % 2 === 0) {
                $mulai = Carbon::today()->addDays(5 + $index);
                $selesai = $mulai->copy()->addDay();
                $pengajuan = $mulai->copy()->subDay();

                $berkas = "berkas_cuti/cuti_{$karyawan->id_karyawan}_{$mulai->format('Ymd')}.jpg";
                Storage::disk('public')->put($berkas, 'seed berkas cuti');

                Cuti::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal_pengajuan' => $pengajuan->toDateString(),
                    'tanggal_mulai' => $mulai->toDateString(),
                    'tanggal_selesai' => $selesai->toDateString(),
                    'alasan' => 'Cuti tahunan',
                    'jenis_cuti' => 'cuti',
                    'berkas_bukti' => $berkas,
                    'status' => 'approved',
                    'disetujui_oleh' => null,
                ]);
            }

            if ($index % 3 === 0) {
                $mulai = Carbon::today()->subDays(2 + $index);
                $selesai = $mulai->copy();
                $pengajuan = $mulai->copy();

                $berkas = "berkas_cuti/sakit_{$karyawan->id_karyawan}_{$mulai->format('Ymd')}.jpg";
                Storage::disk('public')->put($berkas, 'seed surat dokter');

                Cuti::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal_pengajuan' => $pengajuan->toDateString(),
                    'tanggal_mulai' => $mulai->toDateString(),
                    'tanggal_selesai' => $selesai->toDateString(),
                    'alasan' => 'Sakit dan perlu istirahat',
                    'jenis_cuti' => 'sakit',
                    'berkas_bukti' => $berkas,
                    'status' => 'approved',
                    'disetujui_oleh' => null,
                ]);
            }
        }
    }
}
