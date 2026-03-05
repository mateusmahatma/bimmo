<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    use HasFactory;

    protected $table = 'dompet';

    protected $fillable = [
        'nama',
        'ikon',
        'saldo',
        'id_user',
        'status',
    ];

    protected $casts = [
        'saldo' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class , 'dompet_id');
    }
}
