<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Mail\VaccinationMail;
use App\Models\ReminderLog;
use App\Models\User;
use App\Models\Vaccination;
use App\Notifications\ReminderNotification;
use App\Notifications\VaccinationDueNotification;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RunSchedulerWorker extends Command
{
    protected $signature   = 'scheduler:work';
    protected $description = 'Checks reminders every minute and fires in-app + email notifications exactly on time';

    public function handle(ReminderService $reminderService): void
    {
        $this->info('');
        $this->info('🐾  Virtual Pet Care — Scheduler Worker');
        $this->info('    Sends notifications at the exact reminder time.');
        $this->info('    Press Ctrl+C to stop.');
        $this->info('');

        // Generate today's logs on startup
        $reminderService->generateDailyLogs();
        $this->line('[' . now()->format('H:i:s') . '] ✓ Today\'s reminder logs ready.');

        $lastDate   = today()->toDateString();
        $lastMinute = '';

        while (true) {
            $now        = now();
            $today      = $now->toDateString();
            $currentMin = $now->format('H:i');

            // Midnight rollover
            if ($today !== $lastDate) {
                $reminderService->generateDailyLogs();
                $reminderService->markMissedReminders();
                $this->line('[' . $now->format('H:i:s') . '] ✓ New day — logs generated, missed reminders marked.');
                $lastDate = $today;
            }

            // Only fire once per minute
            if ($currentMin !== $lastMinute) {
                $lastMinute = $currentMin;
                $this->fireRemindersForMinute($now);

                // Vaccination alerts at 9:00 AM
                if ($currentMin === '09:00') {
                    $this->sendVaccinationAlerts();
                }
            }

            sleep(10);
        }
    }

    private function fireRemindersForMinute(Carbon $now): void
    {
        // Match reminders scheduled for this exact minute (H:i format)
        $currentTime = $now->format('H:i');

        $logs = ReminderLog::where('status', 'pending')
            ->whereDate('scheduled_date', today())
            ->where(function ($q) use ($currentTime, $now) {
                // Match exact H:i or H:i:00 format
                $q->where('scheduled_time', $currentTime)
                  ->orWhere('scheduled_time', $currentTime . ':00')
                  ->orWhere('scheduled_time', 'like', $currentTime . '%');
            })
            ->with(['reminder.user', 'reminder.pet'])
            ->get();

        if ($logs->isEmpty()) return;

        $this->line('[' . $now->format('H:i:s') . '] 🔔 ' . $logs->count() . ' reminder(s) due now:');

        foreach ($logs as $log) {
            $user     = $log->reminder?->user;
            $reminder = $log->reminder;

            if (!$user || !$reminder || !$reminder->pet) continue;

            $petName = $reminder->pet->name;

            // ── In-app notification (always, never fails silently) ──────────
            try {
                $user->notify(new ReminderNotification($reminder));
                $this->line('   ✓ In-app  → ' . $user->email . ' | "' . $reminder->title . '" for ' . $petName);
            } catch (\Throwable $e) {
                $this->error('   ✗ In-app failed: ' . $e->getMessage());
                Log::error('In-app notification failed: ' . $e->getMessage());
            }

            // ── Email to the USER who owns the reminder ─────────────────────
            if ($this->canSendEmail($user, $reminder)) {
                try {
                    // Send to the REMINDER OWNER's email address
                    Mail::to($user->email)->send(new ReminderMail($reminder, $user));
                    $this->line('   ✓ Email  → ' . $user->email . ' | "' . $reminder->title . '"');
                } catch (\Throwable $e) {
                    $this->warn('   ⚠ Email failed → ' . $user->email . ': ' . substr($e->getMessage(), 0, 100));
                    Log::warning('Reminder email failed for ' . $user->email . ': ' . $e->getMessage());
                }
            }
        }
    }

    private function canSendEmail(User $user, $reminder): bool
    {
        // Must have email notifications enabled
        if (!$user->email_notifications) return false;
        // Reminder must have email notify enabled
        if (!$reminder->email_notify) return false;
        // Mail driver must be a real SMTP driver (not log/array)
        return in_array(config('mail.default'), ['smtp', 'sendmail', 'mailgun', 'ses', 'postmark']);
    }

    private function sendVaccinationAlerts(): void
    {
        $vaccinations = Vaccination::whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [now(), now()->addDays(7)])
            ->with(['pet.user'])
            ->get();

        if ($vaccinations->isEmpty()) return;

        $this->line('[' . now()->format('H:i:s') . '] 💉 Sending vaccination alerts...');

        foreach ($vaccinations as $vax) {
            $user = $vax->pet?->user;
            if (!$user) continue;

            // In-app
            try {
                $user->notify(new VaccinationDueNotification($vax));
                $this->line('   ✓ In-app  → ' . $user->email . ' | ' . $vax->vaccine_name . ' for ' . $vax->pet->name);
            } catch (\Throwable $e) {}

            // Email to the pet owner
            if (in_array(config('mail.default'), ['smtp', 'sendmail', 'mailgun', 'ses', 'postmark']) && $user->email_notifications) {
                try {
                    Mail::to($user->email)->send(new VaccinationMail($vax, $user));
                    $this->line('   ✓ Email  → ' . $user->email . ' | ' . $vax->vaccine_name);
                } catch (\Throwable $e) {
                    $this->warn('   ⚠ Vaccination email failed: ' . substr($e->getMessage(), 0, 100));
                }
            }
        }
    }
}
