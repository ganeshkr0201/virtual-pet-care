<x-mail::message>
# Hi {{ $user->name }}! 🐾

It's time for your pet care reminder.

| Detail | Info |
|--------|------|
| **Reminder** | {{ $reminder->title }} |
| **Pet** | {{ $reminder->pet->name }} |
| **Type** | {{ ucfirst(str_replace('_', ' ', $reminder->type)) }} |
| **Time** | {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('g:i A') }} |

<x-mail::button :url="url('/reminders')" color="primary">
View Reminders
</x-mail::button>

Keep up the great pet care!

Thanks,
**{{ config('app.name') }}**
</x-mail::message>
