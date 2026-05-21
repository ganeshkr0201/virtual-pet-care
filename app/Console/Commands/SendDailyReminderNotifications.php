<?php

namespace App\Console\Commands;

use App\Models\ReminderLog;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyReminderNotifications extends Command
{
    protected $signature = 'reminders:send-notifications {--all : Send for all pending reminders today}';
    protected $description = 'Save in-app (database) notifications for due reminders. Email is handled separately.';

    public function handle(): void
    {
        $query = ReminderLog::where('status', 'pending')
            ->whereDate('scheduled_date', today())
            ->with(['reminder.user', 'reminder.pet']);

        // In production without --all: only reminders due in ±5 min window
        if (!$this->option('all') && app()->environment('production')) {
            $now = now();
            $query->whereBetween('scheduled_time', [
                $now->copy()->subMinutes(5)->format('H:i:s'),
                $now->copy()->addMinutes(5)->format('H:i:s'),
            ]);
        }

        $logs  = $query->get();
        $sent  = 0;
        $skip  = 0;

        foreach ($logs as $log) {
            $user     = $log->reminder?->user;
            $reminder = $log->reminder;

            if (!$user || !$reminder || !$reminder->pet) {
                $skip++;
                continue;
            }

            // Only save to database channel — never touches SMTP here
            try {
                $user->notify(new ReminderNotification($reminder));
                $sent++;
            } catch (\Throwable $e) {
                Log::error("In-app notification failed for reminder #{$reminder->id}: " . $e->getMessage());
                $skip++;
            }
        }

        $this->info("✓ In-app notifications: {$sent} sent" . ($skip > 0 ? ", {$skip} skipped" : '') . '.');
    }
}
