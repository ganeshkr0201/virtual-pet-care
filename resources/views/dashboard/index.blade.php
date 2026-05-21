@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

{{-- Welcome banner --}}
<div class="relative overflow-hidden rounded-2xl p-6 text-white"
     style="background:linear-gradient(135deg,#4F46E5 0%,#7C3AED 60%,#6d28d9 100%)">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-10 bg-white"></div>
        <div class="absolute -bottom-8 -left-8 w-36 h-36 rounded-full opacity-10 bg-white"></div>
    </div>
    <div class="relative flex items-center justify-between gap-4">
        <div>
            <p class="text-white/70 text-sm font-medium mb-1">{{ now()->format('l, F j, Y') }}</p>
            <h2 class="text-2xl font-bold mb-2">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}! 👋
            </h2>
            @if($today_pending > 0)
                <p class="text-white/80 text-sm">You have <strong class="text-white">{{ $today_pending }}</strong> reminder{{ $today_pending > 1 ? 's' : '' }} pending today.</p>
            @else
                <p class="text-white/80 text-sm">All caught up for today — great job! 🎉</p>
            @endif
        </div>
        <div class="hidden sm:block text-7xl opacity-30 select-none">🐾</div>
    </div>
    @if($today_total > 0)
    <div class="relative mt-4">
        <div class="flex items-center justify-between text-xs text-white/60 mb-1.5">
            <span>Today's progress</span>
            <span>{{ $completion_rate }}%</span>
        </div>
        <div class="h-2 bg-white/20 rounded-full overflow-hidden">
            <div class="h-full bg-white rounded-full transition-all duration-700" style="width:{{ $completion_rate }}%"></div>
        </div>
    </div>
    @endif
</div>

{{-- Stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
        ['🐾', $pets_count, 'My Pets', 'bg-violet-50', 'text-violet-600'],
        ['✅', $today_completed, 'Done Today', 'bg-emerald-50', 'text-emerald-600'],
        ['⏰', $today_pending, 'Pending', 'bg-amber-50', 'text-amber-600'],
        ['❌', $today_missed, 'Missed', 'bg-red-50', 'text-red-500'],
    ] as [$icon, $val, $label, $bg, $color])
    <div class="stat-card">
        <div class="stat-icon {{ $bg }}">{{ $icon }}</div>
        <div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $val }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $label }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Main grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Today's reminders --}}
    <div class="lg:col-span-2 card">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Today's Reminders</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('M j, Y') }}</p>
            </div>
            <a href="{{ route('reminders.index') }}" class="btn-ghost btn-sm text-primary-600">View all →</a>
        </div>
        <div class="divide-y divide-slate-50 dark:divide-slate-800">
            @forelse($today_logs as $log)
            @if(!$log->reminder) @continue @endif
            <div class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0
                    {{ $log->status === 'completed' ? 'bg-emerald-50' : ($log->status === 'missed' ? 'bg-red-50' : 'bg-primary-50') }}">
                    {{ $log->reminder->type_icon ?? '📌' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 dark:text-white text-sm truncate">{{ $log->reminder->title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $log->reminder->pet->name ?? '' }} · {{ \Carbon\Carbon::parse($log->scheduled_time)->format('g:i A') }}</p>
                </div>
                @if($log->status === 'completed')
                    <span class="badge badge-success">✓ Done</span>
                @elseif($log->status === 'missed')
                    <span class="badge badge-danger">Missed</span>
                @elseif($log->status === 'snoozed')
                    <span class="badge badge-warning">Snoozed</span>
                @else
                    <button onclick="completeReminder({{ $log->reminder_id }}, this)"
                            class="btn-success btn-sm text-xs">Mark Done</button>
                @endif
            </div>
            @empty
            <div class="py-12 text-center">
                <div class="text-4xl mb-3">🎉</div>
                <p class="text-slate-500 text-sm font-medium">No reminders for today</p>
                <a href="{{ route('reminders.create') }}" class="btn-primary btn-sm mt-4 inline-flex">Add Reminder</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right column --}}
    <div class="space-y-5">

        {{-- Monthly rate --}}
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Monthly Progress</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-28 h-28">
                    <canvas id="completionChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $monthly_rate }}%</span>
                        <span class="text-xs text-slate-400">complete</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-emerald-50 rounded-xl p-3 text-center">
                    <p class="text-lg font-bold text-emerald-700">{{ $today_completed }}</p>
                    <p class="text-xs text-emerald-600">Done</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center">
                    <p class="text-lg font-bold text-red-600">{{ $today_missed }}</p>
                    <p class="text-xs text-red-500">Missed</p>
                </div>
            </div>
        </div>

        {{-- Upcoming vaccinations --}}
        @if($upcoming_vaccinations->count() > 0)
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3">Upcoming Vaccinations</h3>
            <div class="space-y-2.5">
                @foreach($upcoming_vaccinations->take(3) as $vax)
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-sm flex-shrink-0">💉</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $vax->vaccine_name }}</p>
                        <p class="text-xs text-slate-400">{{ $vax->pet->name }} · {{ $vax->next_due_date->format('M j') }}</p>
                    </div>
                    @if($vax->days_until_due <= 7)
                        <span class="badge badge-warning text-xs">Soon</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Upcoming appointments --}}
        @if($upcoming_appointments->count() > 0)
        <div class="card p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3">Upcoming Appointments</h3>
            <div class="space-y-2.5">
                @foreach($upcoming_appointments->take(3) as $apt)
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center text-sm flex-shrink-0">🏥</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $apt->title }}</p>
                        <p class="text-xs text-slate-400">{{ $apt->pet->name }} · {{ $apt->appointment_datetime->format('M j, g:i A') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Weekly chart --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-slate-900 dark:text-white">Weekly Activity</h3>
            <p class="text-xs text-slate-400 mt-0.5">Last 7 days</p>
        </div>
    </div>
    <canvas id="weeklyChart" height="70"></canvas>
</div>

{{-- Pets grid --}}
@if($pets->count() > 0)
<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900 dark:text-white text-lg">My Pets</h3>
        <a href="{{ route('pets.index') }}" class="btn-ghost btn-sm text-primary-600">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @foreach($pets->take(5) as $pet)
        <a href="{{ route('pets.show', $pet) }}" class="card-hover p-4 text-center group">
            <img src="{{ $pet->avatar_url }}" alt="{{ $pet->name }}"
                 class="w-14 h-14 rounded-2xl object-cover mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-md">
            <p class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $pet->name }}</p>
            <p class="text-xs text-slate-400 capitalize mt-0.5">{{ $pet->species }}</p>
        </a>
        @endforeach
        <a href="{{ route('pets.create') }}"
           class="card border-2 border-dashed border-slate-200 hover:border-primary-300 p-4 text-center flex flex-col items-center justify-center gap-2 transition-all group hover:bg-primary-50/30">
            <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="text-xs text-slate-400 group-hover:text-primary-600 font-medium">Add Pet</span>
        </a>
    </div>
</div>
@else
<div class="card p-14 text-center">
    <div class="text-6xl mb-4">🐾</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Add your first pet</h3>
    <p class="text-slate-500 text-sm mb-6 max-w-xs mx-auto">Start tracking health, reminders, and appointments for your furry friends.</p>
    <a href="{{ route('pets.create') }}" class="btn-primary btn-lg inline-flex">Add a Pet</a>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('completionChart').getContext('2d'), {
    type:'doughnut',
    data:{ datasets:[{ data:[{{ $monthly_rate }},{{ 100-$monthly_rate }}], backgroundColor:['#4F46E5','#e2e8f0'], borderWidth:0 }] },
    options:{ cutout:'78%', plugins:{ legend:{display:false}, tooltip:{enabled:false} }, animation:{duration:1000} }
});
const wd = @json($weekly_stats);
new Chart(document.getElementById('weeklyChart').getContext('2d'), {
    type:'bar',
    data:{
        labels: wd.map(d=>d.day),
        datasets:[
            { label:'Completed', data:wd.map(d=>d.completed), backgroundColor:'#4F46E5', borderRadius:6, borderSkipped:false },
            { label:'Missed',    data:wd.map(d=>d.missed),    backgroundColor:'#fca5a5', borderRadius:6, borderSkipped:false }
        ]
    },
    options:{
        responsive:true,
        plugins:{ legend:{ position:'top', labels:{ usePointStyle:true, padding:20, font:{size:12} } } },
        scales:{
            x:{ grid:{display:false}, border:{display:false} },
            y:{ grid:{color:'#f1f5f9'}, border:{display:false}, ticks:{stepSize:1} }
        }
    }
});
function completeReminder(id, btn) {
    btn.disabled = true; btn.textContent = '...';
    fetch(`/reminders/${id}/complete`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Content-Type':'application/json'}
    }).then(()=>location.reload());
}
</script>
@endpush
