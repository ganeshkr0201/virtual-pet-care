<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Mail\VaccinationMail;
use App\Models\User;
use App\Models\Vaccination;
use App\Notifications\ReminderNotification;
use App\Notifications\VaccinationDueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestNotification extends Command
{
    protected $signature = 'notify:test {email? : User email (defaults to demo@virtualpetcare.com)}';
    protected $description = 'Send test in-app + email notifications to verify the full pipeline';

    public function handle(): void
    {
        $email = $this->argument('email') ?? 'demo@virtualpetcare.com';
        $user  = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: {$email}");
            return;
        }

        $this->info("Testing notifications for: {$user->email}");
        $this->newLine();

        // ── 1. In-app notification (database channel) ──────────────────────
        $this->line('<comment>Step 1: In-app notification (database)</comment>');
        $reminder = $user->reminders()->with('pet')->first();

        if ($reminder) {
            try {
                $user->notify(new ReminderNotification($reminder));
                $this->line("  ✓ In-app notification saved: \"{$reminder->title}\"");
            } catch (\Throwable $e) {
                $this->error("  ✗ In-app failed: " . $e->getMessage());
            }
        } else {
            $this->warn("  ⚠ No reminders found for this user.");
        }

        // ── 2. Email notification ──────────────────────────────────────────
        $this->newLine();
        $this->line('<comment>Step 2: Email notification</comment>');
        $mailer = config('mail.default');
        $this->line("  Mail driver: <info>{$mailer}</info>");

        if ($mailer === 'log') {
            $this->warn("  ⚠ MAIL_MAILER=log — emails go to storage/logs/laravel.log");
            $this->warn("  To send real emails, update .env:");
            $this->warn("    MAIL_MAILER=smtp");
            $this->warn("    MAIL_PASSWORD=<your-16-char-gmail-app-password>");
        } elseif ($reminder) {
            try {
                Mail::to($user->email)->send(new ReminderMail($reminder, $user));
                $this->line("  ✓ Email sent to: {$user->email}");
            } catch (\Throwable $e) {
                $this->error("  ✗ Email failed: " . $e->getMessage());
                $this->warn("  Your Gmail App Password may be expired.");
                $this->warn("  Generate a new one at: https://myaccount.google.com/apppasswords");
            }
        }

        // ── 3. Vaccination notification ────────────────────────────────────
        $this->newLine();
        $this->line('<comment>Step 3: Vaccination notification</comment>');
        $vax = Vaccination::whereHas('pet', fn($q) => $q->where('user_id', $user->id))
            ->with('pet')->first();

        if ($vax) {
            try {
                $user->notify(new VaccinationDueNotification($vax));
                $this->line("  ✓ Vaccination notification saved: \"{$vax->vaccine_name}\"");
            } catch (\Throwable $e) {
                $this->error("  ✗ Vaccination notification failed: " . $e->getMessage());
            }
        }

        // ── Summary ────────────────────────────────────────────────────────
        $this->newLine();
        $unread = $user->fresh()->unreadNotifications()->count();
        $this->info("Done! User now has {$unread} unread in-app notification(s).");
        $this->line("Open the app and check the 🔔 bell: <info>http://localhost:8000/dashboard</info>");
    }
}
