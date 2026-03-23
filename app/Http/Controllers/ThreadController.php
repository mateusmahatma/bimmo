<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::with(['user', 'comments'])
            ->latest()
            ->paginate(15);

        return view('threads.index', compact('threads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        Thread::create([
            'id_user' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('threads.index')
            ->with('success', 'Thread berhasil dibuat!');
    }

    public function show($id)
    {
        $thread = Thread::with(['user', 'comments.user'])->findOrFail($id);

        return view('threads.show', compact('thread'));
    }

    public function destroy($id)
    {
        $thread = Thread::findOrFail($id);

        if ($thread->id_user !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus thread ini.');
        }

        $thread->delete();

        return redirect()->route('threads.index')
            ->with('success', 'Thread berhasil dihapus.');
    }
}
