@extends('layouts.app')

@section('title', $reminder->title)
@section('page-title', $reminder->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('reminders.index') }}" class="btn-secondary btn-sm">← Back</a>
    </div>

    <!-- Reminder Card -->
    <div class="card p-6">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center text-3xl flex-shrink-0">
                {{ $reminder->type_icon }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-xl font-bold text-slate-900">{{ $reminder->title }}</h2>
                    <span class="badge {{ $reminder->is_active ? 'badge-success' : 'badge-gray' }}">
                        {{ $reminder->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-slate-500 mt-1">{{ $reminder->description }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Pet</p>
                        <p class="font-medium text-slate-900 mt-1">{{ $reminder->pet->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Time</p>
                        <p class="font-medium text-slate-900 mt-1">{{ \Carbon\Carbon::parse($reminder->reminder_time)->format('g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Repeat</p>
                        <p class="font-medium text-slate-900 mt-1 capitalize">{{ $reminder->repeat }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Type</p>
                        <p class="font-medium text-slate-900 mt-1 capitalize">{{ str_replace('_', ' ', $reminder->type) }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reminders.edit', $reminder) }}" class="btn-primary btn-sm">Edit</a>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="card">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Recent History</h3>
        </div>
        @if($reminder->logs->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($reminder->logs as $log)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-2 h-2 rounded-full flex-shrink-0
                            {{ $log->status === 'completed' ? 'bg-emerald-400' : ($log->status === 'missed' ? 'bg-red-400' : ($log->status === 'snoozed' ? 'bg-amber-400' : 'bg-slate-300')) }}">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-700">{{ $log->scheduled_date->format('M j, Y') }}</p>
                            @if($log->notes)
                                <p class="text-xs text-slate-400">{{ $log->notes }}</p>
                            @endif
                        </div>
                        <span class="badge {{ $log->status === 'completed' ? 'badge-success' : ($log->status === 'missed' ? 'badge-danger' : ($log->status === 'snoozed' ? 'badge-warning' : 'badge-gray')) }}">
                            {{ ucfirst($log->status) }}
                        </span>
                        @if($log->completed_at)
                            <span class="text-xs text-slate-400">{{ $log->completed_at->format('g:i A') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center">
                <p class="text-slate-400 text-sm">No history yet</p>
            </div>
        @endif
    </div>

</div>
@endsection
