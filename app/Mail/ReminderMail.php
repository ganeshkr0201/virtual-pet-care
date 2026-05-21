<?php

namespace App\Mail;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reminder $reminder,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🐾 Reminder: {$this->reminder->title} for {$this->reminder->pet->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminder',
        );
    }
}
