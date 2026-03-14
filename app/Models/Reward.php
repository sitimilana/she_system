<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $table = 'reward';
    protected $primaryKey = 'id_reward';

    protected $fillable = [
        'id_karyawan',
        'id_penilaian',
        'tanggal_reward',
        'keterangan',
        'diberikan_oleh'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan');
    }

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class,'id_penilaian');
    }

    public function pemberi()
    {
        return $this->belongsTo(User::class,'diberikan_oleh');
    }
}