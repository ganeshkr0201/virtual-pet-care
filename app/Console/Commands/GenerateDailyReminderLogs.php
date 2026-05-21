<?php

namespace App\Console\Commands;

use App\Models\ReminderLog;
use App\Notifications\ReminderNotification;
use App\Services\ReminderService;
use Illuminate\Console\Command;

class GenerateDailyReminderLogs extends Command
{
    protected $signature = 'reminders:generate-logs';
    protected $description = 'Generate daily reminder log entries for today and send in-app notifications';

    public function handle(ReminderService $reminderService): void
    {
        // 1. Generate log entries for today
        $reminderService->generateDailyLogs();
        $this->info('✓ Daily reminder logs generated.');

        // 2. Send in-app notifications for all of today's pending reminders
        $logs = ReminderLog::where('status', 'pending')
            ->whereDate('scheduled_date', today())
            ->with(['reminder.user', 'reminder.pet'])
            ->get();

        $sent = 0;
        foreach ($logs as $log) {
            $user     = $log->reminder?->user;
            $reminder = $log->reminder;
            if (!$user || !$reminder || !$reminder->pet) continue;

            try {
                $user->notify(new ReminderNotification($reminder));
                $sent++;
            } catch (\Throwable $e) {
                // Never crash — just skip
            }
        }

        $this->info("✓ In-app notifications sent: {$sent}.");
    }
}
