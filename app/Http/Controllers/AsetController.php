<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsetExport;
use App\Imports\AsetImport;
use Carbon\Carbon;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Aset::where('id_user', Auth::id());

            if ($request->status == 'disposed') {
                $data->where('is_disposed', true);
            }
            else {
                $data->where('is_disposed', false);
            }

            if ($request->kondisi) {
                $data->where('kondisi', $request->kondisi);
            }

            if ($request->kategori) {
                $data->where('kategori', $request->kategori);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nilai_buku', function ($row) {
                return 'Rp ' . number_format($row->nilai_buku, 0, ',', '.');
            })
                ->editColumn('tanggal_pembelian', function ($row) {
                return $row->tanggal_pembelian->format('d M Y');
            })
                ->addColumn('action', function ($row) {
                return view('aset.tombol')->with('row', $row);
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('aset.index');
    }

    public function create()
    {
        return view('aset.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_aset' => 'required',
            'kode_aset' => 'required|unique:aset,kode_aset',
            'kategori' => 'required',
            'tanggal_pembelian' => 'required|date',
            'harga_beli' => 'required|numeric',
            'masa_pakai' => 'required|integer',
            'kondisi' => 'required',
        ]);

        $data = $request->all();
        $data['id_user'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('aset/foto', 'public');
        }

        if ($request->hasFile('dokumen')) {
            $data['dokumen'] = $request->file('dokumen')->store('aset/dokumen', 'public');
        }

        Aset::create($data);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show($id)
    {
        $aset = Aset::where('id_user', Auth::id())->with('maintenance')->findOrFail($id);
        return view('aset.show', compact('aset'));
    }

    public function edit($id)
    {
        $aset = Aset::where('id_user', Auth::id())->findOrFail($id);
        return view('aset.edit', compact('aset'));
    }

    public function update(Request $request, $id)
    {
        $aset = Aset::where('id_user', Auth::id())->findOrFail($id);

        $request->validate([
            'nama_aset' => 'required',
            'kode_aset' => 'required|unique:aset,kode_aset,' . $id,
            'kategori' => 'required',
            'kondisi' => 'required',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($aset->foto)
                Storage::disk('public')->delete($aset->foto);
            $data['foto'] = $request->file('foto')->store('aset/foto', 'public');
        }

        if ($request->hasFile('dokumen')) {
            if ($aset->dokumen)
                Storage::disk('public')->delete($aset->dokumen);
            $data['dokumen'] = $request->file('dokumen')->store('aset/dokumen', 'public');
        }

        $aset->update($data);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aset = Aset::where('id_user', Auth::id())->findOrFail($id);
        if ($aset->foto)
            Storage::disk('public')->delete($aset->foto);
        if ($aset->dokumen)
            Storage::disk('public')->delete($aset->dokumen);
        $aset->delete();

        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus.');
    }

    public function addMaintenance(Request $request, $id)
    {
        $aset = Aset::where('id_user', Auth::id())->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required',
            'biaya' => 'required|numeric',
        ]);

        AsetMaintenance::create([
            'id_aset' => $aset->id,
            'tanggal' => $request->tanggal,
            'kegiatan' => $request->kegiatan,
            'teknisi' => $request->teknisi,
            'biaya' => $request->biaya,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Catatan pemeliharaan ditambahkan.');
    }

    public function dispose(Request $request, $id)
    {
        $aset = Aset::where('id_user', Auth::id())->findOrFail($id);

        $request->validate([
            'tanggal_disposal' => 'required|date',
            'alasan_disposal' => 'required',
        ]);

        $aset->update([
            'is_disposed' => true,
            'tanggal_disposal' => $request->tanggal_disposal,
            'alasan_disposal' => $request->alasan_disposal,
            'nilai_disposal' => $request->nilai_disposal ?? 0,
        ]);

        return redirect()->route('aset.index')->with('success', 'Aset telah dihapus dari inventaris aktif.');
    }

    public function report()
    {
        $user_id = Auth::id();
        $total_aset = Aset::where('id_user', $user_id)->where('is_disposed', false)->count();
        $total_nilai_beli = Aset::where('id_user', $user_id)->where('is_disposed', false)->sum('harga_beli');

        $asets = Aset::where('id_user', $user_id)->where('is_disposed', false)->get();
        $total_nilai_buku = $asets->sum('nilai_buku');

        $kondisi_stats = Aset::where('id_user', $user_id)
            ->where('is_disposed', false)
            ->selectRaw('kondisi, count(*) as total')
            ->groupBy('kondisi')
            ->get();

        return view('aset.report', compact('total_aset', 'total_nilai_beli', 'total_nilai_buku', 'kondisi_stats'));
    }

    public function export()
    {
        return Excel::download(new AsetExport, 'daftar-aset-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new AsetImport, $request->file('file'));

        return back()->with('success', 'Data aset berhasil diimpor.');
    }
}
