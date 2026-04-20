<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        // Show pinned notes first, then most recent
        $notes = Note::where('id_user', Auth::id())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        if ($request->wantsJson()) {
            return response()->json($notes);
        }
        return view('notes.index');
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
            'is_pinned' => false,
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

        if ($request->has('is_pinned')) {
            // ensure boolean
            $updateData['is_pinned'] = (bool) $request->is_pinned;
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

    public function clearCompleted()
    {
        Note::where('id_user', Auth::id())->where('is_checked', true)->delete();
        return response()->json(['success' => true]);
    }
}
