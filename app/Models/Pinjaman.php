<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;
    protected $table = 'pinjaman';
    protected $guarded = ['id'];
    protected $fillable = [
        'id_user',
        'nama_pinjaman',
        'jumlah_pinjaman',
        'jangka_waktu',
        'start_date',
        'end_date',
        'status'
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke model BayarPinjaman
    public function bayar_pinjaman()
    {
        return $this->hasMany(BayarPinjaman::class, 'id_pinjaman');
    }
}
