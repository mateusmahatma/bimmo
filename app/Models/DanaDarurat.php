<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaDarurat extends Model
{
    use HasFactory;
    protected $table = 'dana_darurat';
    protected $primaryKey = 'id_dana_darurat';
    protected $fillable = ['id_user', 'tgl_transaksi', 'jenis', 'nominal', 'keterangan'];
}
