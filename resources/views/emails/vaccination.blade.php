<x-mail::message>
# Vaccination Reminder 💉

Hi **{{ $user->name }}**,

Your pet needs a vaccination soon!

| Detail | Info |
|--------|------|
| **Pet** | {{ $vaccination->pet->name }} |
| **Vaccine** | {{ $vaccination->vaccine_name }} |
| **Due Date** | {{ $vaccination->next_due_date->format('M j, Y') }} |
@if($vaccination->administered_by)
| **Last given by** | {{ $vaccination->administered_by }} |
@endif

<x-mail::button :url="url('/health/' . $vaccination->pet_id)" color="primary">
View Health Records
</x-mail::button>

Please schedule an appointment with your vet soon.

Thanks,
**{{ config('app.name') }}**
</x-mail::message>
