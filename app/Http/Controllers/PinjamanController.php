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
use Illuminate\Pagination\LengthAwarePaginator;

class PinjamanController extends Controller
{
    public function exportExcel(Request $request)
    {
        $filterStatus = $request->input('filter_status');
        return Excel::download(new PinjamanExport($filterStatus), 'pinjaman.xlsx');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pinjaman::where('id_user', $userId)->with('bayar_pinjaman');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_pinjaman', 'LIKE', "%{$search}%");
        }

        // Filter Status
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        // Summary calculations (before pagination but after filter)
        // Note: sum counts all rows in the query
        $totalRemaining = (clone $query)->sum('jumlah_pinjaman');
        $ids = (clone $query)->pluck('id');
        $totalPaid = \App\Models\BayarPinjaman::whereIn('id_pinjaman', $ids)->sum('jumlah_bayar');
        $totalOriginal = $totalRemaining + $totalPaid;
        $totalNextMonthInstallment = (clone $query)->where('status', 'belum_lunas')->get()->sum(function ($p) {
            return min($p->nominal_angsuran, $p->jumlah_pinjaman);
        });

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        // Allowed sort columns
        $dbSortable = ['nama_pinjaman', 'jumlah_pinjaman', 'created_at', 'status'];
        $computedSortable = ['next_due_date', 'total_loan', 'paid_amount'];

        if (in_array($sort, $dbSortable, true)) {
            $query->orderBy($sort, $direction);
            $pinjaman = $query->paginate(10)->withQueryString();
        } else {
            // For computed sorts, fetch filtered rows then sort in-memory.
            if (!in_array($sort, $computedSortable, true)) {
                $sort = 'created_at';
            }

            $items = $query->get();

            $computePaidAmount = function ($p) {
                return (float) $p->bayar_pinjaman->sum('jumlah_bayar');
            };
            $computeTotalLoan = function ($p) use ($computePaidAmount) {
                return (float) $p->jumlah_pinjaman + $computePaidAmount($p);
            };
            $computeNextDueDateKey = function ($p) use ($computePaidAmount) {
                $totalPaid = $computePaidAmount($p);
                $cumulativeExpected = 0.0;
                foreach (($p->simulasi_cicilan ?? []) as $simulasi) {
                    $cumulativeExpected += (float) ($simulasi['nominal'] ?? 0);
                    $isPaid = $totalPaid >= ($cumulativeExpected - 0.01);
                    if (!$isPaid) {
                        $date = $simulasi['tanggal'] ?? null;
                        return $date ? strtotime($date) : PHP_INT_MAX;
                    }
                }
                return PHP_INT_MAX;
            };

            $items = $items->sortBy(function ($p) use ($sort, $computePaidAmount, $computeTotalLoan, $computeNextDueDateKey) {
                return match ($sort) {
                    'paid_amount' => $computePaidAmount($p),
                    'total_loan' => $computeTotalLoan($p),
                    'next_due_date' => $computeNextDueDateKey($p),
                    default => $p->created_at?->timestamp ?? 0,
                };
            }, SORT_REGULAR, $direction === 'desc')->values();

            $perPage = 10;
            $page = (int) $request->get('page', 1);
            $page = $page > 0 ? $page : 1;
            $pageItems = $items->forPage($page, $perPage)->values();

            $pinjaman = new LengthAwarePaginator(
                $pageItems,
                $items->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        if ($request->ajax() && !$request->hasHeader('X-SPA-Navigation')) {
            return response()->json([
                'html' => view('pinjaman._table_list', compact('pinjaman'))->render(),
                'totalPinjaman' => 'Rp ' . number_format($totalRemaining, 0, ',', '.'),
                'totalPaid' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
                'totalOriginal' => 'Rp ' . number_format($totalOriginal, 0, ',', '.'),
                'totalNextMonthInstallment' => 'Rp ' . number_format($totalNextMonthInstallment, 0, ',', '.'),
            ]);
        }

        return view('pinjaman.index', compact('pinjaman', 'totalRemaining', 'totalPaid', 'totalOriginal', 'totalNextMonthInstallment'));
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
            'nominal_angsuran' => 'nullable|numeric',
            'jangka_waktu' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
            'simulasi_cicilan' => 'nullable|string',
        ]);

        $validatedData['id_user'] = Auth::id();

        if (isset($validatedData['simulasi_cicilan'])) {
            $validatedData['simulasi_cicilan'] = json_decode($validatedData['simulasi_cicilan'], true);
        }

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

        $data = Pinjaman::where('id', $id)->where('id_user', Auth::id())->firstOrFail();
        return response()->json(['result' => $data]);
    }

    public function update(Request $request, $hash)
    {
        $id = Hashids::decode($hash)[0] ?? null;
        abort_if(!$id, 404);

        $validasi = Validator::make($request->all(), [
            'nama_pinjaman' => 'required',
            'jumlah_pinjaman' => 'required|numeric',
            'nominal_angsuran' => 'nullable|numeric',
            'jangka_waktu' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
            'simulasi_cicilan' => 'nullable|string',
        ], [
            'nama_pinjaman.required' => 'Nama wajib diisi',
            'jumlah_pinjaman.required' => 'Jumlah pinjaman wajib diisi',
        ]);

        if ($validasi->fails()) {
            return response()->json(['errors' => $validasi->errors()], 422);
        }

        $pinjaman = Pinjaman::where('id', $id)->where('id_user', Auth::id())->firstOrFail();

        $data = [
            'nama_pinjaman' => $request->nama_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'nominal_angsuran' => $request->nominal_angsuran,
            'jangka_waktu' => $request->jangka_waktu,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ];

        if ($request->has('simulasi_cicilan')) {
            $data['simulasi_cicilan'] = json_decode($request->simulasi_cicilan, true);
        }

        $pinjaman->update($data);
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
