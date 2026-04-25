<?php

return [
    'alerts' => [
        // Cashflow bulan ini (pemasukan - pengeluaran) < 0
        'cashflow_deficit' => env('HEALTH_ALERT_CASHFLOW_DEFICIT', true),

        // Debt Service Ratio (cicilan bulanan / pemasukan bulanan) %
        'debt_service_ratio' => env('HEALTH_ALERT_DEBT_SERVICE_RATIO', true),
        'debt_service_ratio_danger' => env('HEALTH_DEBT_SERVICE_RATIO_DANGER', 35),
    ],
];

