<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\BayarPinjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Storage;

class BayarPinjamanController extends Controller
{
    public function bayar(Request $request, $hash)
    {
        $id_pinjaman = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id_pinjaman, 404);

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

            $bukti_bayar = null;
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
                $bukti_bayar = $path;
            }

            // Simpan pembayaran
            $bayar = BayarPinjaman::create([
                'id_user' => Auth::id(),
                'id_pinjaman' => $id_pinjaman,
                'jumlah_bayar' => $jumlah_bayar,
                'tgl_bayar' => $tgl_bayar,
                'bukti_bayar' => $bukti_bayar,
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
                $pinjaman->status = 'lunas';
            }

            $pinjaman->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        }
        catch (\Exception $e) {
            DB::rollBack();
            // Delete uploaded file if transaction fails
            if (isset($bukti_bayar) && $bukti_bayar) {
                Storage::disk('public')->delete($bukti_bayar);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id_bayar)
    {
        $bayar = BayarPinjaman::findOrFail($id_bayar);

        // Return JSON for AJAX
        return response()->json([
            'success' => true,
            'data' => [
                'id_bayar' => $bayar->id_bayar,
                'jumlah_bayar' => $bayar->jumlah_bayar,
                'tgl_bayar' => $bayar->tgl_bayar,
                'bukti_bayar' => $bayar->bukti_bayar ? asset('storage/' . $bayar->bukti_bayar) : null,
            ]
        ]);
    }

    public function update(Request $request, $id_bayar)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'tgl_bayar' => 'required|date',
            'bukti_bayar' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $bayar = BayarPinjaman::findOrFail($id_bayar);
        $pinjaman = Pinjaman::findOrFail($bayar->id_pinjaman);

        $selisih = $request->jumlah_bayar - $bayar->jumlah_bayar;

        if ($selisih > $pinjaman->jumlah_pinjaman) {
            return redirect()->back()->with('error', 'Nominal pembayaran melebihi sisa pinjaman');
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('bukti_bayar')) {
                // Hapus file lama jika ada
                if ($bayar->bukti_bayar) {
                    Storage::disk('public')->delete($bayar->bukti_bayar);
                }

                $file = $request->file('bukti_bayar');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
                $bayar->bukti_bayar = $path;
            }

            $bayar->jumlah_bayar = $request->jumlah_bayar;
            $bayar->tgl_bayar = $request->tgl_bayar;
            $bayar->save();

            // Update pinjaman
            $pinjaman->jumlah_pinjaman -= $selisih;

            if ($pinjaman->jumlah_pinjaman <= 0) {
                $pinjaman->jumlah_pinjaman = 0;
                $pinjaman->status = 'lunas';
            }
            else {
                $pinjaman->status = 'belum_lunas';
            }

            $pinjaman->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil diperbarui');
        }
        catch (\Exception $e) {
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
        }
        else {
            $pinjaman->status = 'lunas';
        }

        $pinjaman->save();

        $bayar_pinjaman->delete();

        // Hapus file jika ada
        if ($bayar_pinjaman->bukti_bayar) {
            Storage::disk('public')->delete($bayar_pinjaman->bukti_bayar);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil dihapus dan jumlah pinjaman diperbarui.');
    }
}
