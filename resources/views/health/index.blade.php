@extends('layouts.app')
@section('title', 'Health Tracker')
@section('page-title', 'Health Tracker')

@section('content')
<div class="space-y-6">

<div class="page-header">
    <div>
        <h2 class="page-title">Health Tracker</h2>
        <p class="page-subtitle">Monitor your pets' health and medical records</p>
    </div>
</div>

@if($pets->count() > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($pets as $pet)
    @php
        $overdueVax  = $pet->vaccinations->filter(fn($v) => $v->is_overdue)->count();
        $upcomingVax = $pet->vaccinations->filter(fn($v) => !$v->is_overdue && $v->next_due_date && $v->days_until_due <= 30)->count();
    @endphp
    <a href="{{ route('health.pet', $pet) }}" class="card-hover group flex flex-col">
        <div class="p-5 flex items-center gap-4 border-b border-slate-50 dark:border-slate-800">
            <img src="{{ $pet->avatar_url }}" alt="{{ $pet->name }}"
                 class="w-14 h-14 rounded-2xl object-cover shadow-md group-hover:scale-105 transition-transform flex-shrink-0">
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-slate-900 dark:text-white">{{ $pet->name }}</h3>
                <p class="text-sm text-slate-500 capitalize">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
                @if($pet->weight)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $pet->weight }} kg · {{ $pet->age }}</p>
                @endif
            </div>
            <svg class="w-5 h-5 text-slate-300 group-hover:text-primary-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="p-4 grid grid-cols-3 gap-3">
            <div class="text-center p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $pet->medicalRecords->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Records</p>
            </div>
            <div class="text-center p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $pet->vaccinations->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Vaccines</p>
            </div>
            <div class="text-center p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $pet->weight ? $pet->weight.'kg' : '—' }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Weight</p>
            </div>
        </div>
        @if($overdueVax > 0)
            <div class="mx-4 mb-4 alert-error text-xs py-2">⚠️ {{ $overdueVax }} overdue vaccination{{ $overdueVax > 1 ? 's' : '' }}</div>
        @elseif($upcomingVax > 0)
            <div class="mx-4 mb-4 alert-warning text-xs py-2">💉 {{ $upcomingVax }} vaccination{{ $upcomingVax > 1 ? 's' : '' }} due soon</div>
        @endif
    </a>
    @endforeach
</div>
@else
<div class="card p-16 text-center">
    <div class="text-6xl mb-4">🏥</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No pets to track</h3>
    <p class="text-slate-500 text-sm mb-6">Add a pet first to start tracking their health.</p>
    <a href="{{ route('pets.create') }}" class="btn-primary inline-flex">Add a Pet</a>
</div>
@endif

</div>
@endsection
