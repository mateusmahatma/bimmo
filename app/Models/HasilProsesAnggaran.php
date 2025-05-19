<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pengeluaran;

class HasilProsesAnggaran extends Model
{
    protected $table = 'hasil_proses_anggaran';

    protected $primaryKey = 'id_proses_anggaran';

    protected $casts = [
        'jenis_pengeluaran' => 'array',
        'nominal_anggaran' => 'float',
        'anggaran_yang_digunakan' => 'float',
    ];

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_anggaran',
        'jenis_pengeluaran',
        'persentase_anggaran',
        'nominal_anggaran',
        'anggaran_yang_digunakan',
        // 'sisa_anggaran',
    ];

    protected $appends = ['nama_jenis_pengeluaran'];

    public function getNamaJenisPengeluaranAttribute()
    {
        if (empty($this->jenis_pengeluaran) || !is_array($this->jenis_pengeluaran)) {
            return '-';
        }

        // Ambil nama pengeluaran berdasarkan ID
        $namaList = \App\Models\Pengeluaran::whereIn('id', $this->jenis_pengeluaran)
            ->pluck('nama')
            ->toArray();

        return implode(', ', $namaList);
    }
}
