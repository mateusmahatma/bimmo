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
        $targetDate = now()->addDays(3)->format('Y-m-d');

        // Find events starting exactly in 3 days that haven't been notified and have email enabled
        $events = Event::with('user')
            ->whereDate('start_at', $targetDate)
            ->where('send_email', true)
            ->where('reminder_sent', false)
            ->get();

        foreach ($events as $event) {
            $recipient = $event->notification_email ?: ($event->user->email ?? null);

            if ($recipient) {
                try {
                    Mail::to($recipient)->send(new EventReminder($event));

                    $event->reminder_sent = true;
                    $event->save();

                    $this->info("H-3 Reminder sent for event: {$event->title} to {$recipient}");
                }
                catch (\Exception $e) {
                    $this->error("Failed to send reminder for event #{$event->id}: " . $e->getMessage());
                }
            }
        }

        $this->info('Event reminders (H-3) check completed.');
    }
}
