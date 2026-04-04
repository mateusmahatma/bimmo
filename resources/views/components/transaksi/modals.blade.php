@props([
    'summaryPemasukan',
    'summaryPengeluaran',
    'totalPemasukan',
    'totalPengeluaran',
])

<x-transaksi.income-details-modal :summary-pemasukan="$summaryPemasukan" :total-pemasukan="$totalPemasukan" />
<x-transaksi.expense-details-modal :summary-pengeluaran="$summaryPengeluaran" :total-pengeluaran="$totalPengeluaran" />
<x-transaksi.email-export-modal />
<x-transaksi.open-date-modal />
<x-transaksi.import-excel-modal />
<x-transaksi.upload-modal />
