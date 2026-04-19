<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'disiplin',
        'produktivitas',
        'tanggung_jawab',
        'sikap_kerja',
        'loyalitas',
        'total_skor',
        'catatan_evaluasi',
        'dinilai_oleh'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class,'dinilai_oleh');
    }
}
