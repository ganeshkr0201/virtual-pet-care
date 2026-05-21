@extends('layouts.app')
@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

{{-- Profile Info --}}
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="font-bold text-slate-900 dark:text-white">Profile Information</h2>
        <p class="text-sm text-slate-400 mt-0.5">Update your name, email, and avatar</p>
    </div>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 space-y-5">
        @csrf @method('PUT')
        <div class="flex items-center gap-5" x-data="{ preview: '{{ $user->avatar_url }}' }">
            <div class="relative flex-shrink-0">
                <img :src="preview" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-primary-100 shadow-md">
                <label class="absolute -bottom-1.5 -right-1.5 w-8 h-8 rounded-xl flex items-center justify-center cursor-pointer shadow-md transition-all hover:scale-110"
                       style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <input type="file" name="avatar" accept="image/*" class="hidden"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </label>
            </div>
            <div>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                <p class="text-sm text-slate-400">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 mt-1">JPG, PNG or WebP · Max 2MB</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input @error('name') border-red-400 @enderror">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input @error('email') border-red-400 @enderror">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+1 234 567 8900">
            </div>
            <div>
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-input">
                    @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ old('timezone', $user->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn-primary">Save Changes</button>
    </form>
</div>

{{-- Change Password --}}
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="font-bold text-slate-900 dark:text-white">Change Password</h2>
        <p class="text-sm text-slate-400 mt-0.5">Choose a strong password</p>
    </div>
    <form method="POST" action="{{ route('profile.password') }}" class="p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="form-label">Current Password *</label>
            <input type="password" name="current_password" required class="form-input @error('current_password') border-red-400 @enderror" placeholder="••••••••">
            @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">New Password *</label>
                <input type="password" name="password" required class="form-input @error('password') border-red-400 @enderror" placeholder="Min. 8 characters">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Confirm New Password *</label>
                <input type="password" name="password_confirmation" required class="form-input" placeholder="Repeat password">
            </div>
        </div>
        <button type="submit" class="btn-primary">Update Password</button>
    </form>
</div>

{{-- Notification Preferences --}}
<div class="card">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="font-bold text-slate-900 dark:text-white">Preferences</h2>
        <p class="text-sm text-slate-400 mt-0.5">Notification and display settings</p>
    </div>
    <form method="POST" action="{{ route('profile.notifications') }}" class="p-6 space-y-3">
        @csrf @method('PUT')
        @foreach([
            ['email_notifications', 'Email Notifications', 'Receive reminder alerts via email', $user->email_notifications],
            ['push_notifications',  'Push Notifications',  'In-app notification alerts',         $user->push_notifications],
            ['dark_mode',           'Dark Mode',           'Use dark theme by default',           $user->dark_mode],
        ] as [$name, $title, $desc, $checked])
        <label class="flex items-center justify-between p-4 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
            <div>
                <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ $title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $desc }}</p>
            </div>
            <div class="relative flex-shrink-0 ml-4" x-data="{ on: {{ $checked ? 'true' : 'false' }} }">
                <input type="checkbox" name="{{ $name }}" value="1" :checked="on" class="sr-only">
                <div @click="on = !on" class="w-11 h-6 rounded-full cursor-pointer transition-colors duration-200"
                     :class="on ? 'bg-primary-600' : 'bg-slate-200'">
                    <div class="w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-200 mt-0.5 ml-0.5"
                         :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                </div>
            </div>
        </label>
        @endforeach
        <div class="pt-2">
            <button type="submit" class="btn-primary">Save Preferences</button>
        </div>
    </form>
</div>

</div>
@endsection
