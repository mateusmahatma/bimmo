<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PinjamanExport;

class PinjamanController extends Controller
{
    public function exportExcel(Request $request)
    {
        $filterStatus = $request->input('filter_status');
        return Excel::download(new PinjamanExport($filterStatus), 'pinjaman.xlsx');
    }

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
                ->setRowAttr([
                'data-hash' => function ($pinjaman) {
                return Hashids::encode($pinjaman->id);
            }
            ])
                ->addColumn('total_loan', function ($pinjaman) {
                $paid = $pinjaman->bayar_pinjaman->sum('jumlah_bayar');
                return 'Rp ' . number_format($pinjaman->jumlah_pinjaman + $paid, 0, ',', '.');
            })
                ->addColumn('paid_amount', function ($pinjaman) {
                $paid = $pinjaman->bayar_pinjaman->sum('jumlah_bayar');
                return 'Rp ' . number_format($paid, 0, ',', '.');
            })
                ->editColumn('jumlah_pinjaman', function ($pinjaman) {
                return 'Rp ' . number_format($pinjaman->jumlah_pinjaman, 0, ',', '.');
            })
                ->editColumn('jangka_waktu', function ($pinjaman) {
                return $pinjaman->jangka_waktu . ' bulan';
            })
                ->addColumn('hash', function ($pinjaman) {
                return Hashids::encode($pinjaman->id);
            })
                ->addColumn('aksi', function ($pinjaman) {
                $pinjaman->hash = Hashids::encode($pinjaman->id);
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
            'keterangan' => 'nullable|string',
        ]);

        $validatedData['id_user'] = Auth::id();

        Pinjaman::create($validatedData);

        return redirect()->route('pinjaman.index')->with('success', 'Pinjaman Berhasil Tersimpan.');
    }

    public function show($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $pinjaman = Pinjaman::with('user', 'bayar_pinjaman')->findOrFail($id);
        return view('pinjaman.show', compact('pinjaman'));
    }

    public function edit($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $data = Pinjaman::where('id', $id)->first();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $validasi = Validator::make($request->all(), [
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
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
            'keterangan' => $request->keterangan,
        ];

        Pinjaman::where('id', $id)->update($data);
        return response()->json(['success' => "Berhasil melakukan update data"]);
    }



    public function destroy($hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

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
        ]);

        $hashedIds = $validated['ids'];
        $ids = [];
        foreach ($hashedIds as $hash) {
            $decoded = Hashids::decode($hash)[0] ?? null;
            if ($decoded) {
                $ids[] = $decoded;
            }
        }

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Invalid IDs.'], 400);
        }

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
