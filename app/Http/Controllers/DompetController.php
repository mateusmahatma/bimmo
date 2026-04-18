<?php

namespace App\Http\Controllers;

use App\Models\Dompet;
use App\Models\Transaksi;
use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DompetController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $wallets = Dompet::where('id_user', $userId)->get();
        $totalBalance = $wallets->sum(fn($w) => (float)$w->saldo);
        
        return view('dompet.index', compact('wallets', 'totalBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'ikon' => 'nullable|string|max:255',
            'custom_ikon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'saldo' => 'nullable|numeric',
        ]);

        $ikon = $request->ikon ?? 'bi-wallet2';

        if ($request->hasFile('custom_ikon')) {
            $image = $request->file('custom_ikon');
            $name = str_replace(' ', '_', $request->nama) . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/img/icons/uploads');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $image->move($destinationPath, $name);
            $ikon = 'uploads/' . $name;
        }

        $initialSaldo = $request->filled('saldo') ? (float)$request->saldo : 0;

        $dompet = Dompet::create([
            'nama' => $request->nama,
            'ikon' => $ikon,
            'saldo' => $initialSaldo,
            'id_user' => Auth::id(),
            'status' => 1,
        ]);

        if ($initialSaldo > 0 && $request->has('record_income')) {
            $pemasukan = Pemasukan::firstOrCreate(
                ['nama' => $dompet->nama, 'id_user' => Auth::id()],
                ['kode_pemasukan' => 'M0000']
            );

            Transaksi::create([
                'tgl_transaksi' => now(),
                'pemasukan' => $pemasukan->id,
                'nominal_pemasukan' => $initialSaldo,
                'keterangan' => 'Saldo awal ' . $dompet->nama,
                'dompet_id' => $dompet->id,
                'id_user' => Auth::id(),
                'status' => 1,
            ]);

            Log::info('Automated initial balance transaction created', [
                'wallet_id' => $dompet->id,
                'pemasukan_id' => $pemasukan->id
            ]);
        }

        return redirect()->route('dompet.index')->with('success', 'Dompet berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        
        $query = Transaksi::where('dompet_id', $id)
            ->with(['pemasukanRelation', 'pengeluaranRelation'])
            ->orderBy('tgl_transaksi', 'desc');

        // Fetch all to handle encrypted column filtering in PHP
        $transactions = $query->get();

        if ($request->has('type')) {
            $type = $request->type;
            $transactions = $transactions->filter(function ($t) use ($type) {
                if ($type === 'income') {
                    return (float)$t->nominal_pemasukan > 0;
                } elseif ($type === 'expense') {
                    return (float)$t->nominal > 0;
                }
                return true;
            });
        }

        // Manual pagination for the filtered collection
        $perPage = 10;
        $page = $request->get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->slice($offset, $perPage)->values(),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
            
        return view('dompet.history', compact('wallet', 'transactions', 'paginatedTransactions'))
            ->with('transactions', $paginatedTransactions);
    }

    public function addBalance(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        
        if ($request->has('record_income')) {
            $pemasukan = Pemasukan::firstOrCreate(
                ['nama' => $wallet->nama, 'id_user' => Auth::id()],
                ['kode_pemasukan' => 'M0000']
            );

            Transaksi::create([
                'tgl_transaksi' => now(),
                'pemasukan' => $pemasukan->id,
                'nominal_pemasukan' => $request->nominal,
                'keterangan' => $request->keterangan ?? 'Top up saldo manual',
                'dompet_id' => $id,
                'id_user' => Auth::id(),
                'status' => 1,
            ]);

            Log::info('Manual top up transaction created', [
                'wallet_id' => $id,
                'pemasukan_id' => $pemasukan->id,
                'nominal' => $request->nominal
            ]);
        }

        $wallet->saldo = (float)$wallet->saldo + (float)$request->nominal;
        $wallet->save();

        return redirect()->route('dompet.show', $id)->with('success', 'Saldo berhasil ditambahkan');
    }

    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'saldo' => 'required|numeric|min:0',
        ]);

        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        $wallet->saldo = (float) $request->saldo;
        $wallet->save();

        return redirect()->route('dompet.index')->with('success', 'Nominal dompet berhasil diperbarui');
    }


    public function reports()
    {
        $userId = Auth::id();
        $wallets = Dompet::where('id_user', $userId)->get();
        return view('dompet.reports', compact('wallets'));
    }

    public function destroy($id)
    {
        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        
        $hasTransactions = Transaksi::where('dompet_id', $id)->exists();
        
        if ($hasTransactions) {
            return redirect()->route('dompet.index')->with('error', 'Tidak bisa menghapus dompet ini data ada di cash flow');
        }

        if ($wallet->ikon && str_starts_with($wallet->ikon, 'uploads/')) {
            $iconPath = public_path('img/icons/' . $wallet->ikon);
            if (file_exists($iconPath)) {
                unlink($iconPath);
            }
        }

        $wallet->delete();

        return redirect()->route('dompet.index')->with('success', 'Dompet berhasil dihapus');
    }
    public function transfer(Request $request)
    {
        $request->validate([
            'dari_dompet_id' => 'required|exists:dompet,id',
            'ke_dompet_id' => 'required|exists:dompet,id|different:dari_dompet_id',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $userId = Auth::id();
        $dariDompet = Dompet::where('id_user', $userId)->findOrFail($request->dari_dompet_id);
        $keDompet = Dompet::where('id_user', $userId)->findOrFail($request->ke_dompet_id);

        if ((float)$dariDompet->saldo < (float)$request->nominal) {
            return redirect()->back()->with('error', 'Saldo dompet asal tidak mencukupi');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($dariDompet, $keDompet, $request, $userId) {
            // Update balances
            $dariDompet->saldo = (float)$dariDompet->saldo - (float)$request->nominal;
            $dariDompet->save();

            $keDompet->saldo = (float)$keDompet->saldo + (float)$request->nominal;
            $keDompet->save();

            // Create transactions
            // For Expense (Pengeluaran)
            $pengeluaran = \App\Models\Pengeluaran::firstOrCreate(
                ['nama' => 'Transfer Keluar', 'id_user' => $userId],
                ['kode_pengeluaran' => 'K0000']
            );

            \App\Models\Transaksi::create([
                'tgl_transaksi' => now(),
                'pengeluaran' => $pengeluaran->id,
                'nominal' => $request->nominal,
                'keterangan' => 'Transfer ke ' . $keDompet->nama . ($request->keterangan ? ': ' . $request->keterangan : ''),
                'dompet_id' => $dariDompet->id,
                'id_user' => $userId,
                'status' => 1,
            ]);

            // For Income (Pemasukan)
            $pemasukan = \App\Models\Pemasukan::firstOrCreate(
                ['nama' => 'Transfer Masuk', 'id_user' => $userId],
                ['kode_pemasukan' => 'M0000']
            );

            \App\Models\Transaksi::create([
                'tgl_transaksi' => now(),
                'pemasukan' => $pemasukan->id,
                'nominal_pemasukan' => $request->nominal,
                'keterangan' => 'Transfer dari ' . $dariDompet->nama . ($request->keterangan ? ': ' . $request->keterangan : ''),
                'dompet_id' => $keDompet->id,
                'id_user' => $userId,
                'status' => 1,
            ]);
        });

        return redirect()->route('dompet.index')->with('success', 'Transfer antar dompet berhasil dilakukan');
    }
}
