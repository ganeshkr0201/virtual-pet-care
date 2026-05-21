@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="card p-6">
        <div class="flex items-center gap-6">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-md">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500">{{ $user->email }}</p>
                @if($user->phone)
                    <p class="text-slate-500 text-sm mt-1">{{ $user->phone }}</p>
                @endif
                <div class="flex gap-2 mt-2">
                    @foreach($user->roles as $role)
                        <span class="badge-primary">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            <div class="ml-auto">
                <a href="{{ route('profile.edit') }}" class="btn-primary btn-sm">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-50">🐾</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->pets()->count() }}</p>
                <p class="text-sm text-slate-500">Pets</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-50">⏰</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->reminders()->count() }}</p>
                <p class="text-sm text-slate-500">Reminders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-teal-50">📅</div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $user->appointments()->count() }}</p>
                <p class="text-sm text-slate-500">Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-slate-50">📋</div>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $user->created_at->format('M Y') }}</p>
                <p class="text-sm text-slate-500">Member since</p>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Account Settings</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                <div>
                    <p class="font-medium text-slate-900 text-sm">Email Notifications</p>
                </div>
                <span class="badge {{ $user->email_notifications ? 'badge-success' : 'badge-gray' }}">
                    {{ $user->email_notifications ? 'On' : 'Off' }}
                </span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                <div>
                    <p class="font-medium text-slate-900 text-sm">Push Notifications</p>
                </div>
                <span class="badge {{ $user->push_notifications ? 'badge-success' : 'badge-gray' }}">
                    {{ $user->push_notifications ? 'On' : 'Off' }}
                </span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                <div>
                    <p class="font-medium text-slate-900 text-sm">Timezone</p>
                </div>
                <span class="text-sm text-slate-600">{{ $user->timezone }}</span>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('profile.edit') }}" class="btn-secondary btn-sm">Manage Settings</a>
        </div>
    </div>

</div>
@endsection
