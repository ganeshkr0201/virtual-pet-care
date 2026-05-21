@extends('layouts.app')
@section('title', 'My Pets')
@section('page-title', 'My Pets')

@section('content')
<div class="space-y-6">

<div class="page-header">
    <div>
        <h2 class="page-title">My Pets</h2>
        <p class="page-subtitle">{{ $pets->total() }} pet{{ $pets->total() !== 1 ? 's' : '' }} registered</p>
    </div>
    <a href="{{ route('pets.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Pet
    </a>
</div>

{{-- Search + Filter bar --}}
<div class="card p-5">
    <form method="GET" id="filterForm">
        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Search input --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or breed..."
                       class="form-input pl-9">
            </div>

            {{-- Species select --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <span class="text-sm">🐾</span>
                </div>
                <select name="species" class="form-input pl-9 pr-8 w-full sm:w-44 appearance-none cursor-pointer">
                    <option value="">All species</option>
                    @foreach(['dog','cat','bird','rabbit','fish','hamster','other'] as $s)
                        <option value="{{ $s }}" {{ request('species') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 sm:flex-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['search','species']))
                <a href="{{ route('pets.index') }}"
                   class="btn-secondary flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Species quick-filter pills --}}
@if($speciesStats->count() > 0)
<div class="flex flex-wrap gap-2 items-center">
    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mr-1">Quick filter:</span>
    <a href="{{ route('pets.index') }}"
       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold transition-all
              {{ !request('species')
                 ? 'text-white shadow-md shadow-primary-200'
                 : 'bg-white text-slate-600 border border-slate-200 hover:border-primary-300 hover:text-primary-600' }}"
       style="{{ !request('species') ? 'background:linear-gradient(135deg,#4F46E5,#7C3AED)' : '' }}">
        🐾 All
        <span class="text-xs {{ !request('species') ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }} rounded-full px-1.5 py-0.5 font-bold">
            {{ $speciesStats->sum('count') }}
        </span>
    </a>
    @php
        $speciesEmoji = ['dog'=>'🐕','cat'=>'🐈','bird'=>'🐦','rabbit'=>'🐇','fish'=>'🐠','hamster'=>'🐹','other'=>'🐾'];
    @endphp
    @foreach($speciesStats as $stat)
    @php $active = request('species') === $stat->species; @endphp
    <a href="{{ route('pets.index', ['species' => $stat->species]) }}"
       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold transition-all capitalize
              {{ $active
                 ? 'text-white shadow-md shadow-primary-200'
                 : 'bg-white text-slate-600 border border-slate-200 hover:border-primary-300 hover:text-primary-600' }}"
       style="{{ $active ? 'background:linear-gradient(135deg,#4F46E5,#7C3AED)' : '' }}">
        {{ $speciesEmoji[$stat->species] ?? '🐾' }} {{ ucfirst($stat->species) }}
        <span class="text-xs {{ $active ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }} rounded-full px-1.5 py-0.5 font-bold">
            {{ $stat->count }}
        </span>
    </a>
    @endforeach
</div>
@endif

{{-- Grid --}}
@if($pets->count() > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @foreach($pets as $pet)
    <div class="card-hover group flex flex-col">
        <div class="relative overflow-hidden" style="height:200px">
            <img src="{{ $pet->avatar_url }}" alt="{{ $pet->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            <div class="absolute top-3 right-3">
                <span class="badge {{ $pet->is_active ? 'badge-success' : 'badge-gray' }} shadow-sm">
                    {{ $pet->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="absolute bottom-3 left-3 text-white">
                <p class="font-bold text-lg leading-tight drop-shadow">{{ $pet->name }}</p>
                <p class="text-white/80 text-xs capitalize">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
            </div>
        </div>
        <div class="p-4 flex-1 flex flex-col">
            <div class="flex flex-wrap gap-1.5 mb-4">
                @if($pet->age !== 'Unknown')
                    <span class="badge badge-gray">{{ $pet->age }}</span>
                @endif
                @if($pet->weight)
                    <span class="badge badge-gray">{{ $pet->weight }} kg</span>
                @endif
                <span class="badge {{ $pet->activity_level === 'high' ? 'badge-danger' : ($pet->activity_level === 'moderate' ? 'badge-warning' : 'badge-success') }} capitalize">
                    {{ $pet->activity_level }}
                </span>
                <span class="badge badge-gray">{{ $pet->gender === 'male' ? '♂ Male' : ($pet->gender === 'female' ? '♀ Female' : '⚥') }}</span>
            </div>
            <div class="flex gap-2 mt-auto">
                <a href="{{ route('pets.show', $pet) }}" class="btn-primary btn-sm flex-1 justify-center">View Profile</a>
                <a href="{{ route('pets.edit', $pet) }}" class="btn-secondary btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form method="POST" action="{{ route('pets.destroy', $pet) }}"
                      onsubmit="return confirm('Delete {{ $pet->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-ghost btn-sm text-red-500 hover:bg-red-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div>{{ $pets->withQueryString()->links() }}</div>
@else
<div class="card p-16 text-center">
    <div class="text-6xl mb-4">🐾</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No pets found</h3>
    <p class="text-slate-500 text-sm mb-6">{{ request()->hasAny(['search','species']) ? 'Try adjusting your filters.' : 'Add your first pet to get started!' }}</p>
    <a href="{{ route('pets.create') }}" class="btn-primary inline-flex">Add a Pet</a>
</div>
@endif

</div>
@endsection
