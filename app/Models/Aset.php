<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Auth;

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
        'berat',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aset) {
            if (!$aset->kode_aset || $aset->kode_aset === 'AUTO' || strpos($aset->kode_aset, 'AST-') === 0) {
                $userId = $aset->id_user ?? Auth::id();

                // Find last code for THIS user with our specific prefix format
                $lastAset = Aset::where('id_user', $userId)
                    ->where('kode_aset', 'like', 'A-' . $userId . '-%')
                    ->orderBy('kode_aset', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastAset) {
                    // Extract number from A-1-0001 format
                    $parts = explode('-', $lastAset->kode_aset);
                    $lastNumber = (int)end($parts);
                }

                $nextNumber = $lastNumber + 1;
                $aset->kode_aset = sprintf('A-%d-%04d', $userId, $nextNumber);
            }
        });
    }

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
        return $this->calculateNilaiBukuAt(Carbon::now());
    }

    public function calculateNilaiBukuAt(Carbon $asOf): float
    {
        if ($this->is_disposed) {
            return 0.0;
        }

        if ($this->kategori === 'Investasi / Emas') {
            if ((float) $this->berat > 0) {
                $livePrice = GoldPriceService::getPricePerGram();
                if ($livePrice) {
                    return (float) $this->berat * (float) $livePrice;
                }
            }

            return (float) $this->harga_beli;
        }

        $tanggalPembelian = Carbon::parse($this->tanggal_pembelian);

        $totalBulan = (int) $this->masa_pakai * 12;
        $bulanTerpakai = $tanggalPembelian->diffInMonths($asOf);

        if ($bulanTerpakai <= 0 || $totalBulan <= 0) {
            return (float) $this->harga_beli;
        }

        if ($bulanTerpakai >= $totalBulan) {
            return (float) $this->nilai_sisa;
        }

        $totalPenyusutan = (float) $this->harga_beli - (float) $this->nilai_sisa;
        $penyusutanPerBulan = $totalPenyusutan / $totalBulan;
        $akumulasiPenyusutan = $penyusutanPerBulan * $bulanTerpakai;

        return (float) max((float) $this->nilai_sisa, (float) $this->harga_beli - $akumulasiPenyusutan);
    }

    /**
     * Monthly Depreciation Expense
     */
    public function getPenyusutanBulananAttribute()
    {
        if ($this->kategori === 'Investasi / Emas') {
            return 0;
        }

        $totalPenyusutan = $this->harga_beli - $this->nilai_sisa;
        $totalBulan = $this->masa_pakai * 12;

        return $totalBulan > 0 ? $totalPenyusutan / $totalBulan : 0;
    }
}
