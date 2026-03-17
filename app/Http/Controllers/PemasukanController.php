<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pemasukan::where('id_user', $userId);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Allowed sort columns
        $allowedSort = ['nama', 'created_at', 'updated_at'];
        if (in_array($sort, $allowedSort)) {
            $query->orderBy($sort, $direction);
        }
        else {
            $query->orderBy('created_at', 'desc');
        }

        $pemasukan = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pemasukan._table_list', compact('pemasukan'))->render(),
            ]);
        }

        return view('pemasukan.index', compact('pemasukan'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => ['required', 'min:3', 'max:255'],
        ]);

        $validatedData['id_user'] = Auth::id();

        Pemasukan::create($validatedData);
        return redirect('/pemasukan')->with('success', 'Kategori Pemasukan Berhasil Ditambahkan.');
    }

    public function create()
    {
        return view('pemasukan.create', [
            'pemasukan' => new Pemasukan(),
        ]);
    }

    public function show()
    {
    // 
    }

    public function edit($id)
    {
        $pemasukan = Pemasukan::where('id', $id)->first();

        if (request()->ajax()) {
            return response()->json(['result' => $pemasukan]);
        }

        return view('pemasukan.edit', compact('pemasukan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required | string',
        ]);

        Pemasukan::where('id', $id)->update($validatedData);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pemasukan.index')
            ->with('success', 'Berhasil Update Kategori Pemasukan!');
    }

    public function destroy($id)
    {
        $deleted = Pemasukan::where('id', $id)
            ->where('id_user', Auth::id())
            ->delete();

        if (request()->ajax()) {
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Data deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Data not found or unauthorized'], 404);
        }

        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pemasukan,id'
        ]);

        $ids = $validated['ids'];

        // Ensure user owns these records (security)
        $deleted = Pemasukan::whereIn('id', $ids)
            ->where('id_user', Auth::id())
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => "$deleted categories deleted successfully."]);
        }

        return response()->json(['success' => false, 'message' => 'No categories found or authorized to delete.'], 404);
    }
}
