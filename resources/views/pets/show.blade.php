@extends('layouts.app')
@section('title', $pet->name)
@section('page-title', $pet->name)

@section('content')
<div class="space-y-6">

{{-- Hero --}}
<div class="card overflow-hidden">
    <div class="h-36 relative" style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 50%,white 1px,transparent 1px);background-size:24px 24px"></div>
    </div>
    <div class="px-6 pb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-14">
            <img src="{{ $pet->avatar_url }}" alt="{{ $pet->name }}"
                 class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-xl flex-shrink-0">
            <div class="flex-1 pb-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $pet->name }}</h2>
                    <span class="badge {{ $pet->is_active ? 'badge-success' : 'badge-gray' }}">{{ $pet->is_active ? 'Active' : 'Inactive' }}</span>
                    <span class="badge badge-gray">{{ $pet->gender === 'male' ? '♂ Male' : ($pet->gender === 'female' ? '♀ Female' : '⚥ Unknown') }}</span>
                </div>
                <p class="text-slate-500 capitalize mt-0.5">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
            </div>
            <div class="flex gap-2 pb-1">
                <a href="{{ route('pets.edit', $pet) }}" class="btn-primary btn-sm">Edit Pet</a>
                <form method="POST" action="{{ route('pets.destroy', $pet) }}" onsubmit="return confirm('Delete {{ $pet->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    @foreach([
        [$pet->age, 'Age', 'bg-violet-50', 'text-violet-600'],
        [$pet->weight ? $pet->weight.' kg' : '—', 'Weight', 'bg-blue-50', 'text-blue-600'],
        [$pet->reminders->count(), 'Reminders', 'bg-amber-50', 'text-amber-600'],
        [$pet->vaccinations->count(), 'Vaccinations', 'bg-emerald-50', 'text-emerald-600'],
    ] as [$val, $label, $bg, $color])
    <div class="card p-4 text-center">
        <p class="text-xl font-bold {{ $color }}">{{ $val }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Basic info --}}
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Basic Information</h3>
            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach([
                    ['Species', ucfirst($pet->species)],
                    ['Breed', $pet->breed ?: '—'],
                    ['Date of Birth', $pet->date_of_birth ? $pet->date_of_birth->format('M j, Y') : '—'],
                    ['Color', $pet->color ?: '—'],
                    ['Activity Level', ucfirst($pet->activity_level)],
                    ['Microchip ID', $pet->microchip_id ?: '—'],
                ] as [$key, $val])
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                    <dt class="text-xs text-slate-400 font-medium uppercase tracking-wider">{{ $key }}</dt>
                    <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- Medical --}}
        @if($pet->allergies || $pet->medical_history || $pet->emergency_notes)
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Medical Information</h3>
            @if($pet->allergies)
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Allergies</p>
                <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-sm text-red-800">{{ $pet->allergies }}</div>
            </div>
            @endif
            @if($pet->medical_history)
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Medical History</p>
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $pet->medical_history }}</p>
            </div>
            @endif
            @if($pet->emergency_notes)
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Emergency Notes</p>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-sm text-amber-800">{{ $pet->emergency_notes }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Reminders --}}
        <div class="card">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-white">Active Reminders</h3>
                <a href="{{ route('reminders.create') }}?pet_id={{ $pet->id }}" class="btn-primary btn-sm">+ Add</a>
            </div>
            @forelse($pet->reminders->where('is_active', true)->take(5) as $reminder)
            <div class="px-5 py-3.5 flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 last:border-0 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <span class="text-xl">{{ $reminder->type_icon }}</span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $reminder->title }}</p>
                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($reminder->reminder_time)->format('g:i A') }} · {{ ucfirst($reminder->repeat) }}</p>
                </div>
                <a href="{{ route('reminders.edit', $reminder) }}" class="btn-ghost btn-sm text-slate-400 hover:text-primary-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            </div>
            @empty
            <div class="py-8 text-center text-slate-400 text-sm">No reminders yet</div>
            @endforelse
        </div>
    </div>

    {{-- Right --}}
    <div class="space-y-5">

        {{-- Vet --}}
        @if($pet->vet_name || $pet->vet_clinic)
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Veterinarian</h3>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🏥</div>
                <div>
                    @if($pet->vet_name)<p class="font-semibold text-slate-900 dark:text-white">{{ $pet->vet_name }}</p>@endif
                    @if($pet->vet_clinic)<p class="text-sm text-slate-500">{{ $pet->vet_clinic }}</p>@endif
                    @if($pet->vet_phone)<a href="tel:{{ $pet->vet_phone }}" class="text-sm text-primary-600 hover:underline block mt-1">{{ $pet->vet_phone }}</a>@endif
                    @if($pet->vet_email)<a href="mailto:{{ $pet->vet_email }}" class="text-sm text-primary-600 hover:underline block">{{ $pet->vet_email }}</a>@endif
                </div>
            </div>
        </div>
        @endif

        {{-- Vaccinations --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 dark:text-white">Vaccinations</h3>
                <a href="{{ route('health.pet', $pet) }}" class="text-xs text-primary-600 hover:underline font-medium">Manage →</a>
            </div>
            @forelse($pet->vaccinations->take(4) as $vax)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 dark:border-slate-800 last:border-0">
                <span class="text-lg">💉</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $vax->vaccine_name }}</p>
                    <p class="text-xs text-slate-400">{{ $vax->administered_date->format('M j, Y') }}</p>
                </div>
                @if($vax->next_due_date)
                <span class="badge {{ $vax->is_overdue ? 'badge-danger' : 'badge-warning' }} text-xs">
                    {{ $vax->is_overdue ? 'Overdue' : 'Due '.$vax->next_due_date->format('M j') }}
                </span>
                @endif
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-4">No vaccinations recorded</p>
            @endforelse
        </div>

        {{-- Quick actions --}}
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('health.pet', $pet) }}" class="btn-secondary w-full justify-start gap-3 text-sm">🏥 Health Records</a>
                <a href="{{ route('appointments.create') }}?pet_id={{ $pet->id }}" class="btn-secondary w-full justify-start gap-3 text-sm">📅 Schedule Appointment</a>
                <a href="{{ route('reminders.create') }}?pet_id={{ $pet->id }}" class="btn-secondary w-full justify-start gap-3 text-sm">⏰ Add Reminder</a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
