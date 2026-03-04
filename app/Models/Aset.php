<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'aset';

    protected $fillable = [
        'id_user',
        'kode_aset',
        'nama_aset',
        'kategori',
        'merk_model',
        'nomor_seri',
        'tanggal_pembelian',
        'harga_beli',
        'masa_pakai',
        'nilai_sisa',
        'garansi_sampai',
        'kondisi',
        'lokasi',
        'pic',
        'foto',
        'dokumen',
        'is_disposed',
        'alasan_disposal',
        'tanggal_disposal',
        'nilai_disposal',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'garansi_sampai' => 'date',
        'tanggal_disposal' => 'date',
        'harga_beli' => 'decimal:2',
        'nilai_sisa' => 'decimal:2',
        'nilai_disposal' => 'decimal:2',
        'is_disposed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function maintenance()
    {
        return $this->hasMany(AsetMaintenance::class , 'id_aset');
    }

    /**
     * Calculate Book Value using Straight-Line Depreciation
     */
    public function getNilaiBukuAttribute()
    {
        if ($this->is_disposed) {
            return 0;
        }

        $tanggalPembelian = Carbon::parse($this->tanggal_pembelian);
        $sekarang = Carbon::now();

        // Total bulan masa pakai
        $totalBulan = $this->masa_pakai * 12;

        // Sudah dipakai berapa bulan
        $bulanTerpakai = $tanggalPembelian->diffInMonths($sekarang);

        if ($bulanTerpakai <= 0) {
            return $this->harga_beli;
        }

        if ($bulanTerpakai >= $totalBulan) {
            return $this->nilai_sisa;
        }

        // Penyusutan per bulan
        $totalPenyusutan = $this->harga_beli - $this->nilai_sisa;
        $penyusutanPerBulan = $totalPenyusutan / $totalBulan;

        $akumulasiPenyusutan = $penyusutanPerBulan * $bulanTerpakai;

        return max($this->nilai_sisa, $this->harga_beli - $akumulasiPenyusutan);
    }

    /**
     * Monthly Depreciation Expense
     */
    public function getPenyusutanBulananAttribute()
    {
        $totalPenyusutan = $this->harga_beli - $this->nilai_sisa;
        $totalBulan = $this->masa_pakai * 12;

        return $totalBulan > 0 ? $totalPenyusutan / $totalBulan : 0;
    }
}
