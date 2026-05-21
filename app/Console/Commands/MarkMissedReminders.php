<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class MarkMissedReminders extends Command
{
    protected $signature = 'reminders:mark-missed';
    protected $description = 'Mark past pending reminders as missed';

    public function handle(ReminderService $reminderService): void
    {
        $reminderService->markMissedReminders();
        $this->info('Missed reminders marked successfully.');
    }
}
