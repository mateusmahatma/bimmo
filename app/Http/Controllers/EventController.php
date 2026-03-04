<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));

        $events = Event::where('id_user', auth()->id())
            ->where(function ($query) use ($start, $end) {
            $query->whereBetween('start_at', [$start, $end])
                ->orWhereBetween('end_at', [$start, $end]);
        })
            ->get()
            ->map(function ($event) {
            $colors = [
                'reminder' => '#0dcaf0',
                'task' => '#0d6efd',
                'meeting' => '#ffc107',
                'deadline' => '#dc3545',
            ];

            $color = $event->color ?: ($colors[$event->category] ?? '#3788d8');

            return [
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start_at->toIso8601String(),
            'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
            'allDay' => $event->all_day,
            'description' => $event->description,
            'category' => $event->category,
            'status' => $event->status,
            'color' => $color,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
            'category' => $event->category,
            'status' => $event->status,
            'description' => $event->description,
            'send_email' => $event->send_email,
            'notification_email' => $event->notification_email
            ]
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'all_day' => 'nullable|boolean',
            'category' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'send_email' => 'nullable|boolean',
            'notification_email' => 'nullable|email|max:255',
        ]);

        $validated['id_user'] = auth()->id();
        $validated['all_day'] = $request->input('all_day', false);
        $validated['send_email'] = $request->input('send_email', true);
        $validated['notification_email'] = $request->input('notification_email', auth()->user()->email);
        $validated['status'] = 'pending';
        // Ensure category is never null if DB doesn't allow it
        $validated['category'] = $validated['category'] ?? 'reminder';

        $event = Event::create($validated);

        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        if ($event->id_user !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'all_day' => 'nullable|boolean',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'send_email' => 'nullable|boolean',
            'notification_email' => 'nullable|email|max:255',
        ]);

        $event->update(array_filter($validated, fn($value) => $value !== null));

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        if ($event->id_user !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['success' => true]);
    }
}
