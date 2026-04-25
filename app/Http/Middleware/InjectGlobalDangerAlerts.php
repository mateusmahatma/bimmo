<?php

namespace App\Http\Middleware;

use App\Models\Pinjaman;
use App\Models\Transaksi;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class InjectGlobalDangerAlerts
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return $next($request);
        }

        $userId = (int) Auth::id();
        $now = Carbon::now('Asia/Jakarta');

        $trxThisMonth = Transaksi::where('id_user', $userId)
            ->whereYear('tgl_transaksi', $now->year)
            ->whereMonth('tgl_transaksi', $now->month)
            ->get();

        $incomeThisMonth = (float) $trxThisMonth->sum(fn ($t) => (float) $t->nominal_pemasukan);
        $expenseThisMonth = (float) $trxThisMonth->sum(fn ($t) => (float) $t->nominal);
        $saldoThisMonth = $incomeThisMonth - $expenseThisMonth;

        $monthlyDebt = (float) Pinjaman::where('id_user', $userId)
            ->where('status', 'belum_lunas')
            ->get()
            ->sum(fn ($p) => min((float) $p->nominal_angsuran, (float) $p->jumlah_pinjaman));

        $debtServiceRatio = $incomeThisMonth > 0
            ? ($monthlyDebt / $incomeThisMonth) * 100
            : ($monthlyDebt > 0 ? 100.0 : 0.0);

        $periodVersion = $now->format('Y-m');

        $alerts = [];

        if (config('health.alerts.cashflow_deficit', true) && $saldoThisMonth < 0) {
            $alerts[] = [
                'id' => 'cashflow-deficit',
                'version' => $periodVersion,
                'severity' => 'danger',
                'title' => 'Cashflow Defisit',
                'message' => 'Cashflow bulan ini defisit Rp ' . number_format(abs($saldoThisMonth), 0, ',', '.') . '.',
            ];
        }

        $debtThreshold = (float) config('health.alerts.debt_service_ratio_danger', 35);
        if (config('health.alerts.debt_service_ratio', true) && $debtServiceRatio > $debtThreshold) {
            $alerts[] = [
                'id' => 'debt-service-ratio-high',
                'version' => $periodVersion,
                'severity' => 'danger',
                'title' => 'Rasio Utang Melewati Batas Sehat',
                'message' => 'Debt Service Ratio bulan ini ' . number_format($debtServiceRatio, 1) . '% (batas bahaya > ' . number_format($debtThreshold, 0) . '%).',
            ];
        }

        View::share('globalDangerAlerts', $alerts);

        return $next($request);
    }
}
