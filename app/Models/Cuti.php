<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'id_karyawan',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'jenis_cuti',
        'berkas_bukti',
        'status',
        'disetujui_oleh'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'id_karyawan');
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'disetujui_oleh');
    }
}