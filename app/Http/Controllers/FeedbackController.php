<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|min:10',
            'files.*' => 'nullable|file|max:5120',
            'files' => 'nullable|array|max:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = $request->user();
            $description = $request->description;
            $files = $request->file('files') ?: [];

            Mail::to('budgetbimmo@gmail.com')->send(new FeedbackMail($user, $description, $files));

            return response()->json([
                'success' => true,
                'message' => 'Masukan Anda telah berhasil dikirim.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
