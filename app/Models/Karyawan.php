<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'id_user',
        'nama',
        'jabatan',
        'divisi',
        'no_hp',
        'email',
        'alamat',
        'foto',
        'status_karyawan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_user');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class,'id_karyawan');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class,'id_karyawan');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class,'id_karyawan');
    }

    public function gaji()
    {
        return $this->hasMany(Penggajian::class,'id_karyawan');
    }

    public function reward()
    {
        return $this->hasMany(Reward::class,'id_karyawan');
    }
}