<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventOccurrenceStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));
        $startUtc = $start->copy()->utc();
        $endUtc = $end->copy()->utc();

        $occurrenceStatuses = collect();
        if (Schema::hasTable('event_occurrence_statuses')) {
            $occurrenceStatuses = EventOccurrenceStatus::where('id_user', auth()->id())
                ->whereBetween('occurrence_start', [$startUtc->toDateTimeString(), $endUtc->toDateTimeString()])
                ->get()
                ->groupBy('event_id');
        }

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
            // placeholder; overwritten below with range-scoped occurrence completion list if available
            $completedOccurrences = [];

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
                    'rrule' => $event->rrule,
                    'completed_occurrences' => $completedOccurrences,
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

        // Inject per-range completed occurrences for recurring events (or any event, harmless)
        $events = $events->map(function ($eventData) use ($occurrenceStatuses) {
            $eventId = $eventData['id'] ?? null;
            if (!$eventId) return $eventData;

            $items = $occurrenceStatuses->get($eventId, collect());
            if ($items->isEmpty()) return $eventData;

            $eventData['extendedProps']['completed_occurrences'] = $items
                ->map(function ($row) {
                    // Return stable keys for both timed and all-day occurrences.
                    // - TIME:<isoZ> for timed events
                    // - DATE:<YYYY-MM-DD> for all-day events (client uses event.startStr)
                    $utc = $row->occurrence_start->copy()->utc();
                    return [
                        'TIME:' . $utc->toIso8601String(),
                        'DATE:' . $utc->format('Y-m-d'),
                    ];
                })
                ->flatten()
                ->values()
                ->all();

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
                'occurrence_start' => 'nullable|date',
                'occurrence_key' => 'nullable|string',
            ]);

            // For recurring events, allow marking a single occurrence as completed without changing the whole series.
            if ($event->rrule && (($validated['status'] ?? null) === 'completed') && (!empty($validated['occurrence_key']) || !empty($validated['occurrence_start']))) {
                if (!Schema::hasTable('event_occurrence_statuses')) {
                    return response()->json(['message' => 'Occurrence status table not found. Please run migrations.'], 500);
                }

                $occurrenceStartUtc = null;

                if (!empty($validated['occurrence_key'])) {
                    $key = $validated['occurrence_key'];
                    if (str_starts_with($key, 'DATE:')) {
                        $date = substr($key, 5);
                        $occurrenceStartUtc = Carbon::parse($date, 'UTC')->startOfDay();
                    } elseif (str_starts_with($key, 'TIME:')) {
                        $iso = substr($key, 5);
                        $occurrenceStartUtc = Carbon::parse($iso)->utc();
                    } else {
                        // Backward-compatible: treat as ISO datetime
                        $occurrenceStartUtc = Carbon::parse($key)->utc();
                    }
                } else {
                    // Backward-compatible payload
                    $occurrenceStartUtc = Carbon::parse($validated['occurrence_start'])->utc();
                }

                EventOccurrenceStatus::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'id_user' => auth()->id(),
                        'occurrence_start' => $occurrenceStartUtc->toDateTimeString(),
                    ],
                    [
                        'status' => 'completed',
                    ]
                );

                return response()->json(['success' => true]);
            }

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
