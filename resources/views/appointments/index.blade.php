@extends('layouts.app')
@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="space-y-6">

<div class="page-header">
    <div>
        <h2 class="page-title">Appointments</h2>
        <p class="page-subtitle">Manage vet visits and other appointments</p>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Appointment
    </a>
</div>

{{-- Filters --}}
<div class="card p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-40">
                <option value="">All status</option>
                @foreach(['scheduled','completed','cancelled','missed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Pet</label>
            <select name="pet_id" class="form-input w-40">
                <option value="">All pets</option>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['status','pet_id']))
            <a href="{{ route('appointments.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>
</div>

@if($appointments->count() > 0)
<div class="card divide-y divide-slate-50 dark:divide-slate-800">
    @foreach($appointments as $apt)
    @php
        $statusBadge = match($apt->status) {
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'missed'    => 'badge-warning',
            default     => 'badge-primary',
        };
        $typeIcon = match($apt->type) {
            'vaccination' => '💉', 'grooming' => '✂️', 'dental' => '🦷', 'surgery' => '🔬', default => '🏥'
        };
    @endphp
    <div class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-2xl flex-shrink-0">
            {{ $typeIcon }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-slate-900 dark:text-white">{{ $apt->title }}</p>
                <span class="badge {{ $statusBadge }}">{{ ucfirst($apt->status) }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1 flex-wrap text-xs text-slate-400">
                <span>🐾 {{ $apt->pet->name }}</span>
                <span>📅 {{ $apt->appointment_datetime->format('M j, Y g:i A') }}</span>
                @if($apt->clinic_name)<span>🏥 {{ $apt->clinic_name }}</span>@endif
                @if($apt->vet_name)<span>👨‍⚕️ {{ $apt->vet_name }}</span>@endif
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('appointments.edit', $apt) }}" class="btn-secondary btn-sm">Edit</a>
            <form method="POST" action="{{ route('appointments.destroy', $apt) }}"
                  onsubmit="return confirm('Delete this appointment?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm text-red-500 hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div>{{ $appointments->withQueryString()->links() }}</div>
@else
<div class="card p-16 text-center">
    <div class="text-6xl mb-4">📅</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No appointments yet</h3>
    <p class="text-slate-500 text-sm mb-6">Schedule vet visits and other appointments for your pets.</p>
    <a href="{{ route('appointments.create') }}" class="btn-primary inline-flex">Schedule Appointment</a>
</div>
@endif

</div>
@endsection
