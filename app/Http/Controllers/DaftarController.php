<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;


class DaftarController extends Controller
{
    public function index()
    {
        return view('daftar.index');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'min:3', 'max:255', 'unique:users'],
            'email' => ['required', 'email:dns', 'max:255', 'unique:users'],
            'username' => ['required', 'min:3', 'max:255', 'unique:users'],
            'password' => ['required', 'min:3', 'max:255'],
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        return redirect('/pointech')->with('success', 'Pendaftaran Berhasil, Silahkan Login');
    }
}
