<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $guarded = ['id'];
    protected $fillable = ['kode_pengeluaran', 'nama', 'id_user'];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengeluaran) {
            $lastNumber = (int) substr(Pengeluaran::max('kode_pengeluaran'), 1);
            $nextNumber = $lastNumber + 1;

            $pengeluaran->kode_pengeluaran = sprintf('K%04d', $nextNumber);
        });
    }
}
