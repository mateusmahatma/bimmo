<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Mail\NewPasswordMail;

class LupaPasswordController extends Controller
{
    public function index()
    {
        return view('login.lupa-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'unregistered email'], 404);
        }

        $token = Str::random(60);

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        try {
            // Send link with token
            Mail::to($user->email)->send(new NewPasswordMail($token, $user->email));
            return response()->json([
                'success' => 'Link reset password telah dikirim ke email Anda.',
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    public function resetIndex(Request $request)
    {
        return view('login.reset-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function resetUpdate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Check if token is expired (e.g., 60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token reset password telah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('bimmo')->with('success', 'Password berhasil diubah. Silakan login.');
    }
}
