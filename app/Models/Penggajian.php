<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    protected $table = 'penggajian';
    protected $primaryKey = 'id_gaji';

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'gaji_pokok',
        'uang_makan',
        'tunjangan_jabatan',
        'bonus',
        'insentif_kinerja',
        'potongan_bpjs',
        'potongan_lain',
        'total_gaji',
        'tanggal_dibuat',
        'status_slip'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan');
    }
}