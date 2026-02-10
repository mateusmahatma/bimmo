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
        'nominal_awal',
        'nominal_sisa',
        'jumlah_angsuran',
        'angsuran_ke',
        'sisa_angsuran',
        'keterangan',
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

    // Tambahkan boot method untuk menghapus bayar_pinjaman terkait
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pinjaman) {
            $pinjaman->bayar_pinjaman()->delete(); // Hapus semua pembayaran terkait sebelum menghapus pinjaman
        });
    }
}
