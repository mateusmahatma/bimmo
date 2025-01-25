<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UbahPasswordController extends Controller
{
    public function index()
    {
        return view('ubah_password.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:3|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password Sekarang tidak sesuai.']);
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update(['password' => Hash::make($request->new_password)]);

        return redirect()->back()->with('status', 'Password berhasil diubah!');
    }
}
