<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('id_user', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $note = Note::create([
            'id_user' => Auth::id(),
            'content' => $request->content,
            'is_checked' => false,
        ]);

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function update(Request $request, Note $note)
    {
        if ($note->id_user !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note->update([
            'is_checked' => $request->has('is_checked') ? $request->is_checked : $note->is_checked,
            'content' => $request->has('content') ? $request->content : $note->content,
        ]);

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function destroy(Note $note)
    {
        if ($note->id_user !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note->delete();

        return response()->json(['success' => true]);
    }
}
