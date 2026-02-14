<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function updateSkin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skin' => 'required|in:light,dark,auto',
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user->skin = $validated['skin'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Tema berhasil diperbarui!',
        ]);
    }

    public function index()
    {
        return view('profil.index', [
            'active' => 'profil'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:3|max:255|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updatePassword');
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password Sekarang tidak sesuai.'], 'updatePassword');
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update(['password' => Hash::make($request->new_password)]);

        return redirect()->back()->with('password_status', 'Password berhasil diubah!');
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:dns|unique:users,email,' . auth()->user()->id
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateEmail');
        }

        DB::table('users')
            ->where('id', auth()->user()->id)
            ->update(['email' => $request->email]);

        return redirect()->back()->with('email_status', 'Email berhasil diubah!');

    }
}
