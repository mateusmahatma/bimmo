<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Pengeluaran;

class HasilProsesAnggaran extends Model
{
    use HasFactory;

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
        'id_user',
    ];

    protected $appends = ['nama_jenis_pengeluaran'];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function getHashAttribute()
    {
        return \Vinkla\Hashids\Facades\Hashids::encode($this->getKey());
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class , 'id_pengeluaran');
    }

    public function getNamaJenisPengeluaranAttribute()
    {
        if (empty($this->jenis_pengeluaran) || !is_array($this->jenis_pengeluaran)) {
            return '-';
        }

        $namaList = Pengeluaran::whereIn('id', $this->jenis_pengeluaran)
            ->pluck('nama')
            ->toArray();

        return implode(', ', $namaList);
    }
}
