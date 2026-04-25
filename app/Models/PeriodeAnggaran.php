<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAnggaran extends Model
{
    use HasFactory;

    protected $table = 'periode_anggaran';
    protected $primaryKey = 'id_periode_anggaran';

    protected $fillable = [
        'id_user',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}

