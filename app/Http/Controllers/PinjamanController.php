<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();
            $data = Pinjaman::where('id_user', $userId);
            $totalPinjaman = $data->sum('jumlah_pinjaman');

            // filter status
            if ($request->has('filter_status') && !empty($request->filter_status)) {
                // pastikan selalu array
                $filterStatus = (array)$request->filter_status;
                $data->whereIn('status', $filterStatus);
            }

            // total dihitung setelah filter diterapkan
            $totalPinjaman = $data->sum('jumlah_pinjaman');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('jumlah_pinjaman', function ($pinjaman) {
                return 'Rp ' . number_format($pinjaman->jumlah_pinjaman, 0, ',', '.');
            })
                ->editColumn('jangka_waktu', function ($pinjaman) {
                return $pinjaman->jangka_waktu . ' bulan';
            })
                ->editColumn('status', function ($pinjaman) {
                return $pinjaman->status;
            })
                ->addColumn('aksi', function ($pinjaman) {
                return view('pinjaman.tombol', ['pinjaman' => $pinjaman])->with('request', $pinjaman);
            })

                ->with('totalPinjaman', 'Rp ' . number_format($totalPinjaman, 0, ',', '.'))
                ->rawColumns(['aksi'])
                ->toJson();
        }

        // Ambil data unik status pinjaman untuk dropdown filter
        // $statusList = Pinjaman::select('status')
        //     ->where('id_user', Auth::id())
        //     ->distinct()
        //     ->pluck('status');

        return view('pinjaman.index', [
            'pinjaman' => Pinjaman::where('id_user', Auth::id())->get(),
            // 'statusList' => $statusList,
        ]);
    }

    public function create()
    {
        return view('pinjaman.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
        ]);

        $validatedData['id_user'] = Auth::id();

        Pinjaman::create($validatedData);

        return redirect()->route('pinjaman.index')->with('success', 'Pinjaman Berhasil Tersimpan.');
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with('user', 'bayar_pinjaman')->findOrFail($id);
        return view('pinjaman.show', compact('pinjaman'));
    }

    public function edit($id)
    {
        $data = Pinjaman::where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $id)
    {
        $validasi = Validator::make($request->all(), [
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
        ], [
            'nama_pinjaman.required' => 'Nama wajib diisi',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()]);
        }

        $data = [
            'nama_pinjaman' => $request->nama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'jangka_waktu' => $request->jangka_waktu,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ];

        Pinjaman::where('id', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }



    public function destroy($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $pinjaman->delete(); // Ini akan otomatis menghapus semua pembayaran terkait

        return response()->json([
            'success' => true,
            'message' => 'Pinjaman dan semua pembayaran terkait telah dihapus.'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pinjaman,id'
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records
        $deleted = Pinjaman::whereIn('id', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted loans deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No loans found or authorized to delete.'], 404);
    }
}
