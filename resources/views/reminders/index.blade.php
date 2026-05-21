@extends('layouts.app')
@section('title', 'Reminders')
@section('page-title', 'Reminders')

@section('content')
<div class="space-y-6">

<div class="page-header">
    <div>
        <h2 class="page-title">Reminders</h2>
        <p class="page-subtitle">Manage your pet care schedule</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('calendar') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Calendar
        </a>
        <a href="{{ route('reminders.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Reminder
        </a>
    </div>
</div>

{{-- Today's schedule --}}
@if($todayReminders->count() > 0)
<div class="card">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
        <h3 class="font-bold text-slate-900 dark:text-white">Today's Schedule</h3>
        <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('l, M j') }}</p>
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($todayReminders as $reminder)
        @php $done = $reminder->todayLog && $reminder->todayLog->status === 'completed'; @endphp
        <div class="flex items-center gap-3 p-3 rounded-xl border transition-all
                    {{ $done ? 'border-emerald-200 bg-emerald-50/50' : 'border-slate-100 hover:border-primary-200 hover:bg-primary-50/20' }}"
             x-data="{ done: {{ $done ? 'true' : 'false' }} }">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0
                        {{ $done ? 'bg-emerald-100' : 'bg-primary-50' }}">
                {{ $reminder->type_icon }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $reminder->title }}</p>
                <p class="text-xs text-slate-400">{{ $reminder->pet->name }} · {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('g:i A') }}</p>
            </div>
            <div x-show="!done">
                <button @click="fetch('/reminders/{{ $reminder->id }}/complete',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}}).then(()=>done=true)"
                        class="w-8 h-8 rounded-full border-2 border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 flex items-center justify-center transition-all">
                    <svg class="w-4 h-4 text-slate-300 hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
            <div x-show="done">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Filters --}}
<div class="card p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Type</label>
            <select name="type" class="form-input w-44">
                <option value="">All types</option>
                @foreach(['feeding','walking','exercise','grooming','medication','vet_appointment','vaccination','training','water','other'] as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
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
        <div>
            <label class="form-label">Status</label>
            <select name="is_active" class="form-input w-32">
                <option value="">All</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['type','pet_id','is_active']))
            <a href="{{ route('reminders.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>
</div>

{{-- List --}}
@if($reminders->count() > 0)
<div class="card divide-y divide-slate-50 dark:divide-slate-800">
    @foreach($reminders as $reminder)
    @php
        $colors = ['feeding'=>'orange','walking'=>'green','exercise'=>'blue','grooming'=>'pink','medication'=>'red','vet_appointment'=>'purple','vaccination'=>'indigo','training'=>'yellow','water'=>'cyan','other'=>'gray'];
        $c = $colors[$reminder->type] ?? 'gray';
    @endphp
    <div class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 bg-slate-50 dark:bg-slate-800">
            {{ $reminder->type_icon }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-slate-900 dark:text-white">{{ $reminder->title }}</p>
                <span class="badge {{ $reminder->is_active ? 'badge-success' : 'badge-gray' }}">
                    {{ $reminder->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="flex items-center gap-3 mt-1 flex-wrap text-xs text-slate-400">
                <span>🐾 {{ $reminder->pet->name }}</span>
                <span>⏰ {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('g:i A') }}</span>
                <span>🔄 {{ ucfirst($reminder->repeat) }}</span>
                <span>📅 From {{ $reminder->start_date->format('M j, Y') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('reminders.edit', $reminder) }}" class="btn-secondary btn-sm">Edit</a>
            <form method="POST" action="{{ route('reminders.destroy', $reminder) }}"
                  onsubmit="return confirm('Delete this reminder?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm text-red-500 hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div>{{ $reminders->withQueryString()->links() }}</div>
@else
<div class="card p-16 text-center">
    <div class="text-6xl mb-4">⏰</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No reminders yet</h3>
    <p class="text-slate-500 text-sm mb-6">Create reminders to stay on top of your pet's care routine.</p>
    <a href="{{ route('reminders.create') }}" class="btn-primary inline-flex">Create Reminder</a>
</div>
@endif

</div>
@endsection
