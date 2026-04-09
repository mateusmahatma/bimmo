<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => 'required|in:id,en',
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user->language = $validated['language'];
        $user->save();

        session()->put('locale', $validated['language']);
        session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Bahasa berhasil diperbarui!',
        ]);
    }

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
            'message' => 'Mode tampilan berhasil diperbarui!',
        ]);
    }

    public function updateUiStyle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ui_style' => 'required|in:corporate,milenial',
        ]);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user->ui_style = $validated['ui_style'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Gaya visual berhasil diperbarui!',
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

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('password_status', 'Password berhasil diubah!');
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:dns|unique:users,email_hash,' . auth()->user()->id . ',id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateEmail');
        }

        $user = auth()->user();
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('email_status', 'Email berhasil diubah!');
    }

    public function updateName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.min'      => 'Nama minimal 2 karakter.',
            'name.max'      => 'Nama maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateName');
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('name_status', 'Nama berhasil diubah!');
    }

    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_hp' => 'nullable|numeric|unique:users,no_hp_hash,' . auth()->user()->id . ',id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updatePhoneNumber');
        }

        // Clean input (remove non-numeric)
        $cleanNumber = preg_replace('/[^0-9]/', '', $request->no_hp);

        // Ensure starts with 62 if 0
        if ($cleanNumber && substr($cleanNumber, 0, 1) === '0') {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }

        $user = auth()->user();
        $user->no_hp = $cleanNumber;
        $user->save();

        return redirect()->back()->with('phone_status', 'Nomor WhatsApp berhasil disimpan!');
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updatePhoto');
        }

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $user->profile_photo = $path;
            $user->save();
        }

        return redirect()->back()->with('photo_status', 'Foto profil berhasil diperbarui!');
    }

    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            if (Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = null;
            $user->save();
        }

        return redirect()->back()->with('photo_status', 'Foto profil berhasil dihapus!');
    }

    public function updateNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'daily_notification' => 'boolean',
            'notification_interval' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'updateNotification');
        }

        $user = auth()->user();
        $user->daily_notification = $request->has('daily_notification');
        $user->notification_interval = $request->notification_interval;
        $user->save();

        return redirect()->back()->with('notification_status', 'Pengaturan notifikasi berhasil diperbarui!');
    }

    public function showPhoto($filename)
    {
        $path = 'profile_photos/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
