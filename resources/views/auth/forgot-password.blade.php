@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Reset your password</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Enter your email and we'll send you a reset link</p>
    </div>

    @if(session('status'))
        <div class="alert-success mb-4">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input @error('email') border-red-400 @enderror"
                   placeholder="you@example.com">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full btn-lg">
            Send reset link
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
        <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">← Back to sign in</a>
    </p>
@endsection
