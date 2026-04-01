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

        // Basic sanitization for Rich Text (allowing bold, italic, underline, lists, links)
        $cleanContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $request->content);
        $sanitizedContent = strip_tags($cleanContent, '<b><i><u><strong><em><ul><ol><li><a><br><p>');

        $note = Note::create([
            'id_user' => Auth::id(),
            'content' => $sanitizedContent,
            'is_checked' => false,
        ]);

        return response()->json(['success' => true, 'note' => $note]);
    }

    public function update(Request $request, Note $note)
    {
        if ($note->id_user !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updateData = [];
        if ($request->has('is_checked')) {
            $updateData['is_checked'] = $request->is_checked;
        }

        if ($request->has('content')) {
            $cleanContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $request->content);
            $updateData['content'] = strip_tags($cleanContent, '<b><i><u><strong><em><ul><ol><li><a><br><p>');
        }

        $note->update($updateData);

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
