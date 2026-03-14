<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_karyawan',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_pulang',
        'longitude_pulang',
        'foto_masuk',
        'foto_pulang',
        'status'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan');
    }
}