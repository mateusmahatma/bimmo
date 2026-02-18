<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            $query = Pengeluaran::where('id_user', $userId);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
            })
                ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d M Y H:i') : '-';
            })
                ->addColumn('aksi', function ($request) {
                return view('pengeluaran.tombol')->with('request', $request);
            })
                ->toJson();
        }
        return view('pengeluaran.index');
    }

    public function create()
    {
        return view('pengeluaran.create', [
            'pengeluaran' => new Pengeluaran(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => ['required', 'min:3', 'max:255'],
        ]);

        $validatedData['id_user'] = Auth::id();

        pengeluaran::create($validatedData);
        return redirect('/pengeluaran')->with('success', 'Kategori Pengeluaran Berhasil Ditambahkan.');
    }

    public function show()
    {
    //
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::where('id', $id)->first();

        if (request()->ajax()) {
            return response()->json(['result' => $pengeluaran]);
        }

        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required | string',
        ]);

        Pengeluaran::where('id', $id)->update($validatedData);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Berhasil Update Kategori Pengeluaran!');
    }

    public function destroy($id)
    {
        $id = Pengeluaran::where('id', $id)->delete();
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengeluaran,id'
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records
        $deleted = Pengeluaran::whereIn('id', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted categories deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No categories found or authorized to delete.'], 404);
    }
}
