<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Notifications\Notification;

/**
 * In-app (database) notification only.
 * Email is sent separately via App\Mail\ReminderMail to avoid
 * SMTP failures blocking the in-app notification from being saved.
 */
class ReminderNotification extends Notification
{
    public function __construct(public Reminder $reminder) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'       => "Reminder: {$this->reminder->title}",
            'message'     => "Time to: {$this->reminder->title} for " . ($this->reminder->pet->name ?? 'your pet'),
            'reminder_id' => $this->reminder->id,
            'pet_name'    => $this->reminder->pet->name ?? '',
            'type'        => $this->reminder->type,
            'icon'        => $this->reminder->type_icon ?? '🔔',
            'url'         => '/reminders',
        ];
    }
}
