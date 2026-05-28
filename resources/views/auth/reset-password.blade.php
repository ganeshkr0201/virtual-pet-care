@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Set new password</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Choose a strong password for your account</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required
                   class="form-input @error('email') border-red-400 @enderror">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="form-label">New Password</label>
            <input id="password" type="password" name="password" required
                   class="form-input @error('password') border-red-400 @enderror"
                   placeholder="Min. 8 characters">
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-input" placeholder="Repeat your password">
        </div>

        <button type="submit" class="btn-primary w-full btn-lg">Reset Password</button>
    </form>
@endsection
