<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WhatsAppService
{
    /**
     * Parse incoming WhatsApp message and store transaction.
     *
     * Format: KEYWORD [NOMINAL] [KATEGORI] [KETERANGAN]
     * Keywords:
     * - Income: PEMASUKAN, MASUK, IN
     * - Expense: PENGELUARAN, KELUAR, OUT
     */
    public function processMessage($message, $senderNumber = null)
    {
        // 1. Basic Cleaning
        $text = trim($message);
        $parts = explode(' ', $text);
        if (count($parts) < 3) {
            return "Format salah. Gunakan: [KATA_KUNCI] [NOMINAL] [KATEGORI] [KETERANGAN (Optional)]\nContoh: KELUAR 50000 MAKAN Nasi Padang";
        }
        $keyword = strtoupper($parts[0]);
        $nominal = $this->parseNominal($parts[1]);
        $kategoriName = $parts[2];
        $keterangan = count($parts) > 3 ? implode(' ', array_slice($parts, 3)) : '';
        // Default User ID (Admin)
        $userId = 1;
        // 2. Determine Type
        if (in_array($keyword, ['PEMASUKAN', 'MASUK', 'IN'])) {
            return $this->handlePemasukan($userId, $nominal, $kategoriName, $keterangan);
        } elseif (in_array($keyword, ['PENGELUARAN', 'KELUAR', 'OUT'])) {
            return $this->handlePengeluaran($userId, $nominal, $kategoriName, $keterangan);
        } else {
            return "Kata kunci '$keyword' tidak dikenali. Gunakan: MASUK atau KELUAR.";
        }
    }
    private function parseNominal($amountStr)
    {
        // Remove Rp, dots, command
        return (float) preg_replace('/[^0-9]/', '', $amountStr);
    }
    private function handlePemasukan($userId, $nominal, $kategoriName, $keterangan)
    {
        // Find Category
        $kategori = Pemasukan::where('id_user', $userId)
            ->where('nama', 'LIKE', '%' . $kategoriName . '%')
            ->first();
        if (!$kategori) {
            // Optional: Create if not exists or return error. For now, return error to be safe.
            // Or try to find a general category.
            return "Kategori pemasukan '$kategoriName' tidak ditemukan.";
        }
        try {
            DB::beginTransaction();
            Transaksi::create([
                'tgl_transaksi' => now(), // Or parse from message if provided? For now use current time.
                'pemasukan' => $kategori->id,
                'nominal_pemasukan' => $nominal,
                'pengeluaran' => null, // Important to set null for income
                'nominal' => null,
                'keterangan' => $keterangan,
                'id_user' => $userId,
                'status' => 1 // Default status
            ]);
            DB::commit();
            return "Berhasil mencatat Pemasukan: Rp " . number_format($nominal, 0, ',', '.') . " (" . $kategori->nama . ")";
        } catch (\Exception $e) {
            DB::rollBack();
            return "Gagal menyimpan transaksi: " . $e->getMessage();
        }
    }
    private function handlePengeluaran($userId, $nominal, $kategoriName, $keterangan)
    {
        // Find Category
        $kategori = Pengeluaran::where('id_user', $userId)
            ->where('nama', 'LIKE', '%' . $kategoriName . '%')
            ->first();
        if (!$kategori) {
            return "Kategori pengeluaran '$kategoriName' tidak ditemukan.";
        }
        try {
            DB::beginTransaction();
            Transaksi::create([
                'tgl_transaksi' => now(),
                'pemasukan' => null,
                'nominal_pemasukan' => null,
                'pengeluaran' => $kategori->id,
                'nominal' => $nominal,
                'keterangan' => $keterangan,
                'id_user' => $userId,
                'status' => 1
            ]);
            DB::commit();
            return "Berhasil mencatat Pengeluaran: Rp " . number_format($nominal, 0, ',', '.') . " (" . $kategori->nama . ")";
        } catch (\Exception $e) {
            DB::rollBack();
            return "Gagal menyimpan transaksi: " . $e->getMessage();
        }
    }
}
