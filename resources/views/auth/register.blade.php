@extends('layouts.guest')
@section('title', 'Create Account')
@section('content')
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Create your account 🐾</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">Start caring for your pets better today — it's free</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="form-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="form-input @error('name') border-red-400 @enderror" placeholder="John Doe">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="form-input @error('email') border-red-400 @enderror" placeholder="you@example.com">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required
                   class="form-input @error('password') border-red-400 @enderror" placeholder="Min. 8 characters">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-input" placeholder="Repeat your password">
        </div>
        <button type="submit" class="btn-primary btn-lg w-full mt-2">
            Create free account
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 font-semibold hover:text-primary-700 dark:hover:text-primary-300">Sign in →</a>
    </p>
@endsection
