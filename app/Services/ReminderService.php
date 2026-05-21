<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Repositories\ReminderRepository;
use Carbon\Carbon;

class ReminderService
{
    public function __construct(private ReminderRepository $reminderRepository) {}

    public function createReminder(int $userId, array $data): Reminder
    {
        $data['user_id'] = $userId;
        $reminder = $this->reminderRepository->create($data);
        ActivityLog::log('created', "Created reminder: {$reminder->title}", $reminder);

        // Fire an immediate in-app notification so the bell lights up
        $reminder->load('pet');
        $reminder->user->notify(new \App\Notifications\ReminderNotification($reminder));

        return $reminder;
    }

    public function updateReminder(Reminder $reminder, array $data): Reminder
    {
        $updated = $this->reminderRepository->update($reminder, $data);
        ActivityLog::log('updated', "Updated reminder: {$reminder->title}", $reminder);
        return $updated;
    }

    public function deleteReminder(Reminder $reminder): bool
    {
        ActivityLog::log('deleted', "Deleted reminder: {$reminder->title}", $reminder);
        return $this->reminderRepository->delete($reminder);
    }

    public function markComplete(Reminder $reminder, ?string $notes = null): ReminderLog
    {
        $log = ReminderLog::updateOrCreate(
            [
                'reminder_id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'scheduled_date' => today(),
            ],
            [
                'scheduled_time' => $reminder->reminder_time,
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $notes,
            ]
        );

        return $log;
    }

    public function snoozeReminder(Reminder $reminder, int $minutes = null): ReminderLog
    {
        $minutes = $minutes ?? $reminder->snooze_minutes;

        $log = ReminderLog::updateOrCreate(
            [
                'reminder_id' => $reminder->id,
                'user_id' => $reminder->user_id,
                'scheduled_date' => today(),
            ],
            [
                'scheduled_time' => $reminder->reminder_time,
                'status' => 'snoozed',
                'snoozed_until' => now()->addMinutes($minutes),
            ]
        );

        return $log;
    }

    public function generateDailyLogs(): void
    {
        $today = today();
        $dayOfWeek = $today->dayOfWeek;

        $reminders = Reminder::where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->where(function ($q) use ($dayOfWeek) {
                $q->where('repeat', 'daily')
                  ->orWhere('repeat', 'none')
                  ->orWhere(function ($q2) use ($dayOfWeek) {
                      $q2->where('repeat', 'weekly')
                         ->whereJsonContains('repeat_days', $dayOfWeek);
                  });
            })
            ->get();

        foreach ($reminders as $reminder) {
            ReminderLog::firstOrCreate(
                [
                    'reminder_id' => $reminder->id,
                    'user_id' => $reminder->user_id,
                    'scheduled_date' => $today,
                ],
                [
                    'scheduled_time' => $reminder->reminder_time,
                    'status' => 'pending',
                ]
            );
        }
    }

    public function markMissedReminders(): void
    {
        ReminderLog::where('status', 'pending')
            ->where('scheduled_date', '<', today())
            ->update(['status' => 'missed']);
    }
}
