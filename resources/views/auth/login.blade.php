@extends('layouts.guest')
@section('title', 'Sign In')
@section('content')
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome back 👋</h2>
        <p class="text-slate-500 text-sm mt-1.5">Sign in to manage your pets</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input @error('email') border-red-400 ring-2 ring-red-100 @enderror"
                   placeholder="you@example.com">
            @error('email')<p class="form-error"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="form-label mb-0">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Forgot password?</a>
            </div>
            <input id="password" type="password" name="password" required
                   class="form-input @error('password') border-red-400 @enderror"
                   placeholder="••••••••">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2.5">
            <input id="remember" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
            <label for="remember" class="text-sm text-slate-600 select-none">Remember me for 30 days</label>
        </div>
        <button type="submit" class="btn-primary btn-lg w-full mt-2">
            Sign in to your account
        </button>
    </form>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
        <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-slate-400">or</span></div>
    </div>

    <p class="text-center text-sm text-slate-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:text-primary-700">Create one free →</a>
    </p>
@endsection
