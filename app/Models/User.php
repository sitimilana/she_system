<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Ini sudah benar ada di atas

class User extends Authenticatable
{
    // KODE YANG HARUS DITAMBAHKAN ADA DI BAWAH INI:
    use HasApiTokens; 

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role_id',
        'status_akun'
    ];

    protected $hidden = [
        'password'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'id_user', 'id_user');
    }

    // supaya login pakai username
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}