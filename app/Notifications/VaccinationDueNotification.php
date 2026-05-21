<?php

namespace App\Notifications;

use App\Models\Vaccination;
use Illuminate\Notifications\Notification;

/**
 * In-app (database) notification only.
 * Email is sent separately via App\Mail\VaccinationMail.
 */
class VaccinationDueNotification extends Notification
{
    public function __construct(public Vaccination $vaccination) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'          => "Vaccination Due: {$this->vaccination->vaccine_name}",
            'message'        => ($this->vaccination->pet->name ?? 'Your pet') . " is due for {$this->vaccination->vaccine_name} on {$this->vaccination->next_due_date->format('M j, Y')}",
            'vaccination_id' => $this->vaccination->id,
            'pet_name'       => $this->vaccination->pet->name ?? '',
            'icon'           => '💉',
            'url'            => '/health/' . $this->vaccination->pet_id,
        ];
    }
}
