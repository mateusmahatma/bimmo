<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $dates = ['tgl_transaksi'];
    protected $fillable = [
        'id',
        'tgl_transaksi',
        'pemasukan',
        'nominal_pemasukan',
        'pengeluaran',
        'nominal',
        'keterangan',
        'id_user',
        'status'
    ];

    public function pengeluaranRelation()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran', 'id');
    }

    public function pemasukanRelation()
    {
        return $this->belongsTo(Pemasukan::class, 'pemasukan', 'id');
    }
}
