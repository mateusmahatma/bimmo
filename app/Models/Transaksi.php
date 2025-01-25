<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $guarded = ['id'];
    protected $dates = ['tgl_transaksi'];
    protected $fillable = [
        'tgl_transaksi',
        'pemasukan',
        'nominal_pemasukan',
        'pengeluaran',
        'nominal',
        'keterangan',
        'id_user',
    ];
}
