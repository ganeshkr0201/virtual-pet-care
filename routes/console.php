<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

// ── Midnight: generate today's reminder logs + send in-app notifications
Schedule::command('reminders:generate-logs')->dailyAt('00:01');

// ── Every 5 min: send in-app notifications for reminders due now
Schedule::command('reminders:send-notifications')->everyFiveMinutes();

// ── Every 5 min: send reminder EMAILS separately (won't crash in-app if mail fails)
Schedule::call(function () {
    $mailer = config('mail.default');
    if (!in_array($mailer, ['smtp', 'sendmail', 'mailgun', 'ses'])) return;

    $logs = \App\Models\ReminderLog::where('status', 'pending')
        ->whereDate('scheduled_date', today())
        ->whereBetween('scheduled_time', [
            now()->subMinutes(5)->format('H:i:s'),
            now()->addMinutes(5)->format('H:i:s'),
        ])
        ->with(['reminder.user', 'reminder.pet'])
        ->get();

    foreach ($logs as $log) {
        $user     = $log->reminder?->user;
        $reminder = $log->reminder;
        if (!$user || !$reminder || !$user->email_notifications || !$reminder->email_notify) continue;
        try {
            Mail::to($user->email)->send(new \App\Mail\ReminderMail($reminder, $user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Reminder email failed: " . $e->getMessage());
        }
    }
})->everyFiveMinutes()->name('reminder-emails');

// ── 23:55: mark missed reminders
Schedule::command('reminders:mark-missed')->dailyAt('23:55');

// ── 09:00: vaccination due alerts (in-app + email)
Schedule::call(function () {
    $vaccinations = \App\Models\Vaccination::whereNotNull('next_due_date')
        ->whereBetween('next_due_date', [now(), now()->addDays(7)])
        ->with(['pet.user'])
        ->get();

    $mailer = config('mail.default');

    foreach ($vaccinations as $vax) {
        $user = $vax->pet?->user;
        if (!$user) continue;

        // In-app notification
        try {
            $user->notify(new \App\Notifications\VaccinationDueNotification($vax));
        } catch (\Throwable $e) {}

        // Email separately
        if (in_array($mailer, ['smtp', 'sendmail', 'mailgun', 'ses']) && $user->email_notifications) {
            try {
                Mail::to($user->email)->send(new \App\Mail\VaccinationMail($vax, $user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Vaccination email failed: " . $e->getMessage());
            }
        }
    }
})->dailyAt('09:00')->name('vaccination-alerts');
