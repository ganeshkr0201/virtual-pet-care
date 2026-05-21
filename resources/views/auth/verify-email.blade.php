@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
    <div class="text-center mb-6">
        <div class="text-5xl mb-4">📧</div>
        <h2 class="text-2xl font-bold text-slate-900">Verify your email</h2>
        <p class="text-slate-500 text-sm mt-2">
            We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
            Please check your inbox and click the link to activate your account.
        </p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary w-full btn-lg">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn-ghost w-full text-sm text-slate-500">Sign out</button>
    </form>
@endsection
