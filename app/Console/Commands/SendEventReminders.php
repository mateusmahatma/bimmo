<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Mail\EventReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send email reminders for events starting soon';

    public function handle()
    {
        $now = now();
        $soon = now()->addMinutes(30);

        // Find events starting in the next 30 minutes that haven't been notified
        // Note: For simplicity, we're assuming events only need one reminder
        // In a production app, you might want to track 'reminder_sent' in the DB
        $events = Event::with('user')
            ->whereBetween('start_at', [$now, $soon])
            ->get();

        foreach ($events as $event) {
            if ($event->user && $event->user->email) {
                Mail::to($event->user->email)->send(new EventReminder($event));
                $this->info("Reminder sent for event: {$event->title} to {$event->user->email}");
            }
        }

        $this->info('Event reminders check completed.');
    }
}
