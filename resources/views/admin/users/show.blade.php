@extends('layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">← Back</a>
    </div>

    <!-- User Profile -->
    <div class="card p-6">
        <div class="flex items-center gap-6">
            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500">{{ $user->email }}</p>
                <div class="flex gap-2 mt-2">
                    @foreach($user->roles as $role)
                        <span class="badge-primary">{{ $role->name }}</span>
                    @endforeach
                    <span class="badge {{ $user->trashed() ? 'badge-danger' : 'badge-success' }}">
                        {{ $user->trashed() ? 'Inactive' : 'Active' }}
                    </span>
                </div>
            </div>
            <div class="ml-auto">
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                        @csrf
                        <button type="submit" class="{{ $user->trashed() ? 'btn-primary' : 'btn-danger' }} btn-sm">
                            {{ $user->trashed() ? 'Activate User' : 'Deactivate User' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-50">🐾</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->pets->count() }}</p>
                <p class="text-sm text-slate-500">Pets</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-50">⏰</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->reminders->count() }}</p>
                <p class="text-sm text-slate-500">Reminders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-slate-50">📋</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->activityLogs->count() }}</p>
                <p class="text-sm text-slate-500">Activities</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-50">📅</div>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $user->created_at->format('M j, Y') }}</p>
                <p class="text-sm text-slate-500">Joined</p>
            </div>
        </div>
    </div>

    <!-- Pets -->
    @if($user->pets->count() > 0)
    <div class="card p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Pets</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($user->pets as $pet)
                <div class="text-center p-3 rounded-xl bg-slate-50">
                    <img src="{{ $pet->avatar_url }}" class="w-12 h-12 rounded-xl object-cover mx-auto mb-2">
                    <p class="font-medium text-slate-900 text-sm">{{ $pet->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ $pet->species }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Activity Log -->
    @if($user->activityLogs->count() > 0)
    <div class="card p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Recent Activity</h3>
        <div class="space-y-2">
            @foreach($user->activityLogs as $log)
                <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                    <div class="w-2 h-2 rounded-full bg-primary-400 flex-shrink-0"></div>
                    <p class="text-sm text-slate-700 flex-1">{{ $log->description }}</p>
                    <p class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
