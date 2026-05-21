@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

<div class="page-header">
    <div>
        <h2 class="page-title">Notifications</h2>
        <p class="page-subtitle">{{ auth()->user()->unreadNotifications->count() }} unread</p>
    </div>
    @if(auth()->user()->unreadNotifications->count() > 0)
    <button onclick="markAllRead()" class="btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Mark all read
    </button>
    @endif
</div>

@if($notifications->count() > 0)
<div class="card divide-y divide-slate-50 dark:divide-slate-800">
    @foreach($notifications as $n)
    <div class="px-5 py-4 flex items-start gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors
                {{ $n->read_at ? '' : 'bg-primary-50/30 dark:bg-primary-900/10' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0
                    {{ $n->read_at ? 'bg-slate-100 dark:bg-slate-800' : 'bg-primary-100 dark:bg-primary-900/30' }}">
            {{ $n->data['icon'] ?? '🔔' }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ $n->data['title'] ?? 'Notification' }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $n->data['message'] ?? '' }}</p>
            <p class="text-xs text-slate-400 mt-1.5">{{ $n->created_at->diffForHumans() }}</p>
        </div>
        @if(!$n->read_at)
            <div class="w-2.5 h-2.5 rounded-full bg-primary-500 flex-shrink-0 mt-1.5"></div>
        @endif
    </div>
    @endforeach
</div>
<div>{{ $notifications->links() }}</div>
@else
<div class="card p-16 text-center">
    <div class="text-6xl mb-4">🔔</div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">All caught up!</h3>
    <p class="text-slate-500 text-sm">No notifications yet. They'll appear here when reminders are due.</p>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
function markAllRead() {
    fetch('/notifications/mark-all-read', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}
    }).then(()=>location.reload());
}
</script>
@endpush
