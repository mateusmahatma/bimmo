<?php

namespace App\Http\Controllers;

use App\Models\ThreadComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadCommentController extends Controller
{
    public function store(Request $request, $threadId)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        ThreadComment::create([
            'thread_id' => $threadId,
            'id_user' => Auth::id(),
            'body' => $request->body,
        ]);

        return redirect()->route('threads.show', $threadId)
            ->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $comment = ThreadComment::findOrFail($id);

        if ($comment->id_user !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus komentar ini.');
        }

        $threadId = $comment->thread_id;
        $comment->delete();

        return redirect()->route('threads.show', $threadId)
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
