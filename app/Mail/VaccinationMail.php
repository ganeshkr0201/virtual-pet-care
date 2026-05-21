<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VaccinationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Vaccination $vaccination,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "💉 Vaccination Due: {$this->vaccination->vaccine_name} for {$this->vaccination->pet->name}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.vaccination');
    }
}
