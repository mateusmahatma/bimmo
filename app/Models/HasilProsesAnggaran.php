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
        'jenis_pemasukan' => 'array',
        'nominal_anggaran' => 'encrypted',
        'anggaran_yang_digunakan' => 'encrypted',
    ];

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_anggaran',
        'jenis_pengeluaran',
        'persentase_anggaran',
        'nominal_anggaran',
        'anggaran_yang_digunakan',
        'sisa_anggaran',
        'id_user',
        'jenis_pemasukan',
    ];

    protected $appends = ['nama_jenis_pengeluaran', 'remaining_budget', 'hash'];

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

    public function getRemainingBudgetAttribute()
    {
        $nominal = (float)$this->nominal_anggaran;
        $used = (float)$this->anggaran_yang_digunakan;
        return $nominal - $used;
    }

    public function calculateBurnRate()
    {
        $startDate  = \Carbon\Carbon::parse($this->tanggal_mulai)->startOfDay();
        $endDate    = \Carbon\Carbon::parse($this->tanggal_selesai)->endOfDay();
        $today      = \Carbon\Carbon::now()->startOfDay();

        // Only compute when the period has started
        if ($today->lt($startDate)) {
            return null;
        }

        $totalDays     = (int) $startDate->diffInDays($endDate) + 1;
        $daysElapsed   = (int) $startDate->diffInDays($today->min($endDate)) + 1;
        $daysRemaining = max(0, (int) $today->diffInDays($endDate));

        $totalSpent    = (float) $this->anggaran_yang_digunakan;
        $totalBudget   = (float) $this->nominal_anggaran;

        if ($daysElapsed <= 0 || $totalBudget <= 0) {
            return null;
        }

        $dailyRate           = $totalSpent / $daysElapsed;
        $projectedTotal      = $dailyRate * $totalDays;
        $projectedRemaining  = $dailyRate * $daysRemaining;
        $spentPercentage     = ($totalSpent / $totalBudget) * 100;
        $idealPercentage     = ($daysElapsed / $totalDays) * 100;
        $daysUntilBudgetOut  = $dailyRate > 0 ? (int) (($totalBudget - $totalSpent) / $dailyRate) : null;

        $isOverBurning       = $projectedTotal > $totalBudget;
        $isBehindPace        = $spentPercentage > ($idealPercentage * 1.2);

        return [
            'total_days'           => $totalDays,
            'days_elapsed'         => $daysElapsed,
            'days_remaining'       => $daysRemaining,
            'total_spent'          => $totalSpent,
            'total_budget'         => $totalBudget,
            'daily_rate'           => $dailyRate,
            'projected_total'      => $projectedTotal,
            'projected_remaining'  => $projectedRemaining,
            'spent_percentage'     => $spentPercentage,
            'ideal_percentage'     => $idealPercentage,
            'days_until_out'       => $daysUntilBudgetOut,
            'is_over_burning'      => $isOverBurning,
            'is_behind_pace'       => $isBehindPace,
            'alert_triggered'      => $isOverBurning || $isBehindPace,
        ];
    }
}