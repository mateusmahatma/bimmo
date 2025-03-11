<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\BayarPinjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BayarPinjamanController extends Controller
{
    public function bayar(Request $request, $id_pinjaman)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'tgl_bayar' => 'required|date',
        ]);

        $pinjaman = Pinjaman::findOrFail($id_pinjaman);
        $jumlah_bayar = $request->input('jumlah_bayar');
        $tgl_bayar = $request->input('tgl_bayar');

        if ($jumlah_bayar > $pinjaman->jumlah_pinjaman) {
            return redirect()->back()->with('error', 'Nominal pembayaran melebihi jumlah pinjaman');
        }

        try {
            DB::beginTransaction();

            // Simpan pembayaran
            $bayar = BayarPinjaman::create([
                'id_user' => Auth::id(),
                'id_pinjaman' => $id_pinjaman,
                'jumlah_bayar' => $jumlah_bayar,
                'tgl_bayar' => $tgl_bayar,
                'status' => 'sukses',
            ]);

            if (!$bayar) {
                throw new \Exception('Gagal menyimpan pembayaran');
            }

            // Update jumlah pinjaman
            $pinjaman->jumlah_pinjaman -= $jumlah_bayar;

            // Jika sudah lunas, ubah status menjadi "Lunas"
            if ($pinjaman->jumlah_pinjaman <= 0) {
                $pinjaman->jumlah_pinjaman = 0;
                $pinjaman->status = 'Lunas';
            }

            $pinjaman->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $bayar_pinjaman = BayarPinjaman::findOrFail($id);

        $pinjaman = Pinjaman::findOrFail($bayar_pinjaman->id_pinjaman);

        $pinjaman->jumlah_pinjaman += $bayar_pinjaman->jumlah_bayar;

        if ($pinjaman->jumlah_pinjaman > 0) {
            $pinjaman->status = 'belum_lunas';
        } else {
            $pinjaman->status = 'lunas';
        }

        $pinjaman->save();

        $bayar_pinjaman->delete();

        return redirect()->back()->with('success', 'Pembayaran berhasil dihapus dan jumlah pinjaman diperbarui.');
    }
}
