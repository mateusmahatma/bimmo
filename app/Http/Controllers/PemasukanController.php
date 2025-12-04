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
        if ($request->ajax()) {
            // Mendapatkan ID pengguna yang sedang login
            $userId = Auth::id();

            $query = Pemasukan::where('id_user', $userId);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('aksi', function ($pemasukan) {
                    return view('pemasukan.tombol')->with('request', $pemasukan);
                })
                ->toJson();
        }
        return view('pemasukan.index');
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

        return view('pemasukan.edit', compact('pemasukan'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required | string',
        ]);

        Pemasukan::where('id', $id)->update($validatedData);

        return redirect()->route('pemasukan.index')
            ->with('success', 'Berhasil Update Kategori Pemasukan!');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
    }
}
