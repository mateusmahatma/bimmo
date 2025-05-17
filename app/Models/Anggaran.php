<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pengeluaran;

class Anggaran extends Model
{
    use HasFactory;

    protected $table = 'anggaran';

    protected $fillable = [
        'nama_anggaran',
        'persentase_anggaran',
        'id_pengeluaran',
        'id_user',
    ];

    // Cast id_pengeluaran sebagai array otomatis
    protected $casts = [
        'id_pengeluaran' => 'array',
    ];

    // Ambil nama-nama pengeluaran berdasar array id_pengeluaran
    public function getNamaPengeluaranAttribute()
    {
        if (empty($this->id_pengeluaran) || !is_array($this->id_pengeluaran)) {
            return '-';
        }

        $namaList = Pengeluaran::whereIn('id', $this->id_pengeluaran)->pluck('nama')->toArray();

        return implode(', ', $namaList);
    }
}
