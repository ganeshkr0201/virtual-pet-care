@extends('layouts.app')

@section('title', 'Calendar')
@section('page-title', 'Calendar')

@section('content')
<div class="space-y-6">

    <div class="page-header">
        <div>
            <h2 class="page-title">Calendar</h2>
            <p class="page-subtitle">View all reminders and appointments</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reminders.create') }}" class="btn-secondary btn-sm">+ Reminder</a>
            <a href="{{ route('appointments.create') }}" class="btn-primary btn-sm">+ Appointment</a>
        </div>
    </div>

    <!-- Legend -->
    <div class="flex gap-4 flex-wrap">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-primary-600"></div>
            <span class="text-sm text-slate-600">Reminders</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-accent-500"></div>
            <span class="text-sm text-slate-600">Appointments</span>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card p-5">
        <div id="calendar"></div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const events = @json($events);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: events,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            Swal.fire({
                title: info.event.title,
                text: props.type === 'reminder' ? 'Reminder' : 'Appointment',
                icon: 'info',
                confirmButtonColor: '#4F46E5',
            });
        },
        height: 'auto',
        eventDisplay: 'block',
        dayMaxEvents: 3,
    });

    calendar.render();
});
</script>
@endpush
