@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-6">

<div class="page-header">
    <div>
        <h2 class="page-title">Users</h2>
        <p class="page-subtitle">{{ $users->total() }} total users</p>
    </div>
</div>

<div class="card p-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="form-input flex-1">
        <button type="submit" class="btn-primary">Search</button>
        @if(request('search'))<a href="{{ route('admin.users.index') }}" class="btn-secondary">Clear</a>@endif
    </form>
</div>

<div class="card">
    <div class="table-container rounded-none border-0">
        <table class="table">
            <thead>
                <tr><th>User</th><th>Role</th><th>Pets</th><th>Joined</th><th>Last Login</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-primary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="text-slate-500 text-sm">{{ $user->pets()->count() }}</td>
                    <td class="text-slate-500 text-sm">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="text-slate-500 text-sm">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td><span class="badge {{ $user->trashed() ? 'badge-danger' : 'badge-success' }}">{{ $user->trashed() ? 'Inactive' : 'Active' }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary btn-sm">View</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf
                                <button type="submit" class="btn-ghost btn-sm {{ $user->trashed() ? 'text-emerald-600 hover:bg-emerald-50' : 'text-red-500 hover:bg-red-50' }}">
                                    {{ $user->trashed() ? 'Activate' : 'Deactivate' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div>{{ $users->withQueryString()->links() }}</div>

</div>
@endsection
