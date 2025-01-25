<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BayarPinjaman extends Model
{
    protected $table = 'bayar_pinjaman';
    protected $primaryKey = 'id_bayar';
    protected $fillable = [
        'id_user',
        'id_pinjaman',
        'jumlah_bayar',
        'tgl_bayar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'id');
    }
}
