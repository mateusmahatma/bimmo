<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $guarded = ['id'];
    protected $fillable = ['kode_pemasukan', 'nama', 'id_user'];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pemasukan) {
            $lastNumber = (int) substr(Pemasukan::max('kode_pemasukan'), 1);
            $nextNumber = $lastNumber + 1;

            $pemasukan->kode_pemasukan = sprintf('M%04d', $nextNumber);
        });
    }
}
