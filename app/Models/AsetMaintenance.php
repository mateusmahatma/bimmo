<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetMaintenance extends Model
{
    use HasFactory;

    protected $table = 'aset_maintenance';

    protected $fillable = [
        'id_aset',
        'tanggal',
        'kegiatan',
        'teknisi',
        'biaya',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class , 'id_aset');
    }
}
