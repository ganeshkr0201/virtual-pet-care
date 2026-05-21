@extends('layouts.app')

@section('title', $appointment->title)
@section('page-title', $appointment->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('appointments.index') }}" class="btn-secondary btn-sm">← Back</a>
    </div>

    <div class="card p-6">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-3xl flex-shrink-0">
                {{ $appointment->type === 'vaccination' ? '💉' : ($appointment->type === 'grooming' ? '✂️' : '🏥') }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-xl font-bold text-slate-900">{{ $appointment->title }}</h2>
                    <span class="badge {{ $appointment->status === 'completed' ? 'badge-success' : ($appointment->status === 'cancelled' ? 'badge-danger' : 'badge-primary') }}">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
                <p class="text-slate-500 mt-1">{{ $appointment->description }}</p>

                <dl class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Pet</dt>
                        <dd class="font-medium text-slate-900 mt-1">{{ $appointment->pet->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Date & Time</dt>
                        <dd class="font-medium text-slate-900 mt-1">{{ $appointment->appointment_datetime->format('M j, Y g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Type</dt>
                        <dd class="font-medium text-slate-900 mt-1 capitalize">{{ $appointment->type }}</dd>
                    </div>
                    @if($appointment->vet_name)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Vet</dt>
                        <dd class="font-medium text-slate-900 mt-1">{{ $appointment->vet_name }}</dd>
                    </div>
                    @endif
                    @if($appointment->clinic_name)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Clinic</dt>
                        <dd class="font-medium text-slate-900 mt-1">{{ $appointment->clinic_name }}</dd>
                    </div>
                    @endif
                    @if($appointment->clinic_phone)
                    <div>
                        <dt class="text-xs text-slate-400 uppercase tracking-wider">Phone</dt>
                        <dd class="font-medium text-slate-900 mt-1">{{ $appointment->clinic_phone }}</dd>
                    </div>
                    @endif
                </dl>

                @if($appointment->notes)
                    <div class="mt-4 p-3 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Notes</p>
                        <p class="text-sm text-slate-700">{{ $appointment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
            <a href="{{ route('appointments.edit', $appointment) }}" class="btn-primary btn-sm">Edit</a>
            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Delete this appointment?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>

</div>
@endsection
