<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = ['nama_barang', 'status', 'nama_toko', 'harga', 'jumlah', 'id_user'];

    public function getStatusTextAttribute()
    {
        return $this->status ? 'Aset Dimiliki' : 'Aset Digadaikan';
    }
}
