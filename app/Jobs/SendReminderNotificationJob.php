<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Notifications\ReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Reminder $reminder) {}

    public function handle(): void
    {
        $user = $this->reminder->user;

        if (!$user || !$user->push_notifications) {
            return;
        }

        $user->notify(new ReminderNotification($this->reminder));
    }
}
