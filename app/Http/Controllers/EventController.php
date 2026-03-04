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
            // Regular events within range
            $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_at', [$start, $end])
                        ->orWhereBetween('end_at', [$start, $end]);
                }
                )
                    // Recurring events (let FullCalendar handle expansion/filtering)
                    ->orWhereNotNull('rrule');
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

            $isAllDay = (bool)$event->all_day;
            $start = $isAllDay ? $event->start_at->format('Y-m-d') : $event->start_at->toIso8601String();

            $eventData = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $start,
                'allDay' => $isAllDay,
                'color' => $color,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'category' => $event->category,
                    'status' => $event->status,
                    'description' => $event->description,
                    'send_email' => $event->send_email,
                    'notification_email' => $event->notification_email,
                    'rrule' => $event->rrule
                ]
            ];

            if ($event->rrule) {
                // FullCalendar RRule plugin expects the string
                $eventData['rrule'] = $event->rrule;

                // If it's a recurring event, 'duration' helps FullCalendar know how long each instance is
                if (!$isAllDay && $event->start_at && $event->end_at) {
                    $diff = $event->start_at->diff($event->end_at);
                    $eventData['duration'] = sprintf('%02d:%02d', ($diff->days * 24) + $diff->h, $diff->i);
                }
                else if ($isAllDay) {
                    $eventData['duration'] = ['days' => 1];
                }
            }
            else {
                $eventData['end'] = $event->end_at ? ($isAllDay ? $event->end_at->format('Y-m-d') : $event->end_at->toIso8601String()) : null;
            }

            return $eventData;
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        try {
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
                'rrule' => 'nullable|string',
            ]);

            $validated['id_user'] = auth()->id();
            $validated['all_day'] = $request->input('all_day', false);
            $validated['send_email'] = $request->input('send_email', true);
            $validated['notification_email'] = $request->input('notification_email', auth()->user()->email);
            $validated['rrule'] = $request->input('rrule');
            $validated['status'] = 'pending';
            // Ensure category is never null if DB doesn't allow it
            $validated['category'] = $validated['category'] ?? 'reminder';

            $event = Event::create($validated);

            return response()->json($event);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Event $event)
    {
        if ($event->id_user !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
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
                'rrule' => 'nullable|string',
            ]);

            $event->update(array_filter($validated, fn($value) => $value !== null));

            return response()->json($event);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
