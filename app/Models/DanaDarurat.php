<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaDarurat extends Model
{
    use HasFactory;
    protected $table = 'dana_darurat';
    protected $primaryKey = 'id_dana_darurat';
    protected $guarded = ['id_dana_darurat'];
    // protected $fillable = ['id_user', 'tgl_transaksi_dana_darurat', 'jenis_transaksi_dana_darurat', 'nominal_dana_darurat', 'keterangan'];
}
