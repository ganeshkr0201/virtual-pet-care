@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">

    <div class="page-header">
        <div>
            <h2 class="page-title">Reports</h2>
            <p class="page-subtitle">Platform analytics and statistics</p>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="card p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Monthly Trends (Last 6 Months)</h3>
        <canvas id="trendsChart" height="80"></canvas>
    </div>

    <!-- Monthly Stats Table -->
    <div class="card">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Monthly Breakdown</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>New Users</th>
                        <th>New Pets</th>
                        <th>Completed Reminders</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyStats as $stat)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $stat['month'] }}</td>
                            <td class="text-slate-600">{{ $stat['new_users'] }}</td>
                            <td class="text-slate-600">{{ $stat['new_pets'] }}</td>
                            <td class="text-slate-600">{{ $stat['completed_reminders'] }}</td>
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
new Chart(document.getElementById('trendsChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json(collect($monthlyStats)->pluck('month')),
        datasets: [
            {
                label: 'New Users',
                data: @json(collect($monthlyStats)->pluck('new_users')),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: 'New Pets',
                data: @json(collect($monthlyStats)->pluck('new_pets')),
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: 'Completed Reminders',
                data: @json(collect($monthlyStats)->pluck('completed_reminders')),
                borderColor: '#14B8A6',
                backgroundColor: 'rgba(20, 184, 166, 0.1)',
                fill: true,
                tension: 0.4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: { grid: { color: '#f1f5f9' }, border: { display: false } }
        }
    }
});
</script>
@endpush
