<?php

namespace App\Http\Controllers;

use App\Models\Dompet;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            
            // Create directory if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $image->move($destinationPath, $name);
            $ikon = 'uploads/' . $name;
        }

        Dompet::create([
            'nama' => $request->nama,
            'ikon' => $ikon,
            'saldo' => $request->saldo ?? 0,
            'id_user' => Auth::id(),
            'status' => 1,
        ]);

        return redirect()->route('dompet.index')->with('success', 'Dompet berhasil ditambahkan');
    }

    public function show($id)
    {
        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        $transactions = Transaksi::where('dompet_id', $id)
            ->orderBy('tgl_transaksi', 'desc')
            ->paginate(10);
            
        return view('dompet.history', compact('wallet', 'transactions'));
    }

    public function addBalance(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $wallet = Dompet::where('id_user', Auth::id())->findOrFail($id);
        
        Transaksi::create([
            'tgl_transaksi' => now(),
            'nominal_pemasukan' => $request->nominal,
            'keterangan' => $request->keterangan ?? 'Top up saldo manual',
            'dompet_id' => $id,
            'id_user' => Auth::id(),
        ]);

        $wallet->saldo = (float)$wallet->saldo + (float)$request->nominal;
        $wallet->save();

        return redirect()->route('dompet.show', $id)->with('success', 'Saldo berhasil ditambahkan');
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
        
        // Check if wallet has transactions
        $hasTransactions = Transaksi::where('dompet_id', $id)->exists();
        
        if ($hasTransactions) {
            return redirect()->route('dompet.index')->with('error', 'Tidak bisa menghapus dompet ini data ada di cash flow');
        }

        // Delete custom icon if exists
        if ($wallet->ikon && str_starts_with($wallet->ikon, 'uploads/')) {
            $iconPath = public_path('img/icons/' . $wallet->ikon);
            if (file_exists($iconPath)) {
                unlink($iconPath);
            }
        }

        $wallet->delete();

        return redirect()->route('dompet.index')->with('success', 'Dompet berhasil dihapus');
    }
}
