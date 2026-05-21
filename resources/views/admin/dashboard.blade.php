@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach([
        ['👥', $stats['total_users'],     'Total Users',     'bg-primary-50'],
        ['🐾', $stats['active_pets'],     'Active Pets',     'bg-violet-50'],
        ['⏰', $stats['total_reminders'], 'Reminders',       'bg-blue-50'],
        ['✅', $stats['completed_today'], 'Done Today',      'bg-emerald-50'],
        ['💬', $stats['open_feedbacks'],  'Open Feedback',   'bg-amber-50'],
    ] as [$icon, $val, $label, $bg])
    <div class="stat-card">
        <div class="stat-icon {{ $bg }}">{{ $icon }}</div>
        <div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $val }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $label }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-5">
        <h3 class="font-bold text-slate-900 dark:text-white mb-1">New Users</h3>
        <p class="text-xs text-slate-400 mb-4">Last 7 days</p>
        <canvas id="registrationsChart" height="120"></canvas>
    </div>
    <div class="card p-5">
        <h3 class="font-bold text-slate-900 dark:text-white mb-1">Pet Species</h3>
        <p class="text-xs text-slate-400 mb-4">Distribution</p>
        <canvas id="speciesChart" height="120"></canvas>
    </div>
</div>

{{-- Recent Users --}}
<div class="card">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <h3 class="font-bold text-slate-900 dark:text-white">Recent Users</h3>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm text-primary-600">View all →</a>
    </div>
    <div class="table-container rounded-none border-0">
        <table class="table">
            <thead><tr><th>User</th><th>Email</th><th>Joined</th><th>Pets</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($recentUsers as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-xl object-cover">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-slate-500">{{ $user->email }}</td>
                    <td class="text-slate-500">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="text-slate-500">{{ $user->pets()->count() }}</td>
                    <td><span class="badge {{ $user->trashed() ? 'badge-danger' : 'badge-success' }}">{{ $user->trashed() ? 'Inactive' : 'Active' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('registrationsChart').getContext('2d'), {
    type:'bar',
    data:{
        labels: @json(collect($weeklyRegistrations)->pluck('day')),
        datasets:[{ label:'New Users', data:@json(collect($weeklyRegistrations)->pluck('count')), backgroundColor:'#4F46E5', borderRadius:6, borderSkipped:false }]
    },
    options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ x:{grid:{display:false},border:{display:false}}, y:{grid:{color:'#f1f5f9'},border:{display:false},ticks:{stepSize:1}} } }
});
new Chart(document.getElementById('speciesChart').getContext('2d'), {
    type:'doughnut',
    data:{
        labels: @json($speciesStats->pluck('species')->map(fn($s)=>ucfirst($s))),
        datasets:[{ data:@json($speciesStats->pluck('count')), backgroundColor:['#4F46E5','#7C3AED','#14B8A6','#f59e0b','#ef4444','#10b981'], borderWidth:0 }]
    },
    options:{ responsive:true, plugins:{legend:{position:'right',labels:{usePointStyle:true,padding:16}}}, cutout:'55%' }
});
</script>
@endpush
