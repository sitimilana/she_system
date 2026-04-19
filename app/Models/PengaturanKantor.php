<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanKantor extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_kantor';
    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'latitude',
        'longitude',
        'radius',
    ];
}
