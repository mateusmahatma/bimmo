<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;

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
}
