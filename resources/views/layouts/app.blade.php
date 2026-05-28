@php
    if (auth()->check()) {
        auth()->user()->load(['roles'])->loadCount(['pets', 'unreadNotifications']);
        $layoutPetsCount = auth()->user()->pets_count;
        $layoutUnreadCount = auth()->user()->unread_notifications_count;
    } else {
        $layoutPetsCount = 0;
        $layoutUnreadCount = 0;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Virtual Pet Care</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"></div>

    {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 transition-transform duration-300 bg-white border-r shadow-xl dark:bg-slate-900 border-slate-200/60 dark:border-slate-700/60 shadow-slate-200/40 dark:shadow-slate-900/60 lg:translate-x-0 lg:static lg:inset-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-xl shadow-lg rounded-2xl"
                 style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">🐾</div>
            <div>
                <div class="text-sm font-bold leading-tight text-slate-900 dark:text-white">Virtual Pet Care</div>
                <div class="text-xs font-medium text-slate-400">Pet Management</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <p class="px-3 pt-1 pb-2 text-xs tracking-widest uppercase font-700 text-slate-400">Main</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('pets.index') }}"
               class="sidebar-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                My Pets
                @if($layoutPetsCount > 0)
                    <span class="ml-auto text-xs font-bold bg-primary-100 text-primary-700 rounded-full px-2 py-0.5">{{ $layoutPetsCount }}</span>
                @endif
            </a>

            <a href="{{ route('reminders.index') }}"
               class="sidebar-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Reminders
            </a>

            <a href="{{ route('calendar') }}"
               class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </a>

            <a href="{{ route('appointments.index') }}"
               class="sidebar-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Appointments
            </a>

            <a href="{{ route('health.index') }}"
               class="sidebar-link {{ request()->routeIs('health.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Health Tracker
            </a>

            <div class="pt-3 pb-1">
                <p class="px-3 pb-2 text-xs tracking-widest uppercase font-700 text-slate-400">Account</p>
            </div>

            <a href="{{ route('notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
                @if($layoutUnreadCount > 0)
                    <span class="flex items-center justify-center w-5 h-5 ml-auto text-xs font-bold text-white bg-red-500 rounded-full">{{ $layoutUnreadCount > 9 ? '9+' : $layoutUnreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile
            </a>

            @if(auth()->user()->hasRole('admin'))
            <div class="pt-3 pb-1">
                <p class="px-3 pb-2 text-xs tracking-widest uppercase font-700 text-slate-400">Admin</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Admin Panel
            </a>
            @endif
        </nav>

        {{-- User footer --}}
        <div class="px-4 py-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 p-2 transition-colors rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                     class="flex-shrink-0 object-cover w-9 h-9 rounded-xl ring-2 ring-primary-100">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate text-slate-400">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="flex items-center justify-center w-8 h-8 transition-all rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ─────────────────────────────────────────── --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="z-10 flex items-center flex-shrink-0 gap-4 px-4 py-3 border-b bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-slate-200/60 dark:border-slate-700/60 lg:px-6">

            <button @click="sidebarOpen = !sidebarOpen"
                    class="flex items-center justify-center transition-colors lg:hidden w-9 h-9 rounded-xl text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex-1">
                <h1 class="text-base font-bold tracking-tight text-slate-900 dark:text-white">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-1.5">

                {{-- Dark mode --}}
                <button @click="$store.darkMode.toggle()"
                        class="flex items-center justify-center transition-colors w-9 h-9 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg x-show="!$store.darkMode.on" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="$store.darkMode.on" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                {{-- Notification bell --}}
                <div class="relative" x-data="notificationBell()" x-init="init()" @click.outside="open = false">
                    <button @click="toggle()"
                            class="relative flex items-center justify-center transition-colors w-9 h-9 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span x-show="count > 0" x-text="count > 9 ? '9+' : count"
                              class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[1.1rem] h-[1.1rem] flex items-center justify-center px-1 leading-none"></span>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 z-50 mt-2 overflow-hidden bg-white border shadow-2xl w-80 dark:bg-slate-900 rounded-2xl border-slate-200/60 dark:border-slate-700">

                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">Notifications</span>
                                <span x-show="count > 0" x-text="count"
                                      class="flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button x-show="count > 0" @click="markAllRead()"
                                        class="text-xs font-semibold text-primary-600 hover:text-primary-700">Mark all read</button>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-slate-400 hover:text-slate-600">View all</a>
                            </div>
                        </div>

                        <div x-show="loading" class="flex justify-center py-8">
                            <div class="w-5 h-5 border-2 rounded-full border-primary-600 border-t-transparent animate-spin"></div>
                        </div>

                        <div x-show="!loading && items.length === 0" class="py-10 text-center">
                            <div class="mb-2 text-3xl">🔔</div>
                            <p class="text-sm font-medium text-slate-400">All caught up!</p>
                        </div>

                        <div x-show="!loading && items.length > 0" class="overflow-y-auto divide-y max-h-72 divide-slate-50 dark:divide-slate-800">
                            <template x-for="n in items" :key="n.id">
                                <div @click="markRead(n)" class="px-4 py-3 transition-colors cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                                     :class="!n.read_at ? 'bg-primary-50/40 dark:bg-primary-900/10' : ''">
                                    <div class="flex items-start gap-3">
                                        <span class="text-lg flex-shrink-0 mt-0.5" x-text="n.data.icon || '🔔'"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold truncate text-slate-900 dark:text-white" x-text="n.data.title || 'Notification'"></p>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.data.message || ''"></p>
                                            <p class="mt-1 text-xs text-slate-400" x-text="n.created_at"></p>
                                        </div>
                                        <div x-show="!n.read_at" class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0 mt-1.5"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="!loading && items.length > 0"
                             class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50">
                            <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-primary-600 hover:underline">See all notifications →</a>
                        </div>
                    </div>
                </div>

                {{-- Avatar --}}
                <a href="{{ route('profile.edit') }}" class="ml-1">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                         class="object-cover w-8 h-8 transition-all rounded-xl ring-2 ring-primary-200 hover:ring-primary-400">
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-4 mt-4 lg:mx-6 alert-success" style="animation:slideUp .3s ease-out" data-auto-dismiss>
                <svg class="flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 lg:mx-6 alert-error" style="animation:slideUp .3s ease-out" data-auto-dismiss>
                <svg class="flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 overflow-y-auto lg:p-6 page-transition">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script>
function notificationBell() {
    return {
        open: false, loading: false,
        count: {{ $layoutUnreadCount }},
        items: [], timer: null,
        init() {
            // Optimized: No blocking fetch on page boot. Fetch is deferred until clicked or polled in background.
            this.timer = setInterval(() => this.fetch(), 20000);
        },
        async fetch() {
            try {
                const r = await fetch('/notifications/unread', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const d = await r.json();
                if (!this.open && d.count > this.count && this.count !== null) this.showToast(d.notifications[0]);
                this.count = d.count; this.items = d.notifications;
            } catch(e) {}
        },
        toggle() {
            this.open = !this.open;
            if (this.open) { this.loading = true; this.fetch().finally(() => this.loading = false); }
        },
        async markRead(n) {
            if (n.read_at) return;
            try {
                await fetch('/notifications/mark-read', { method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json' }, body: JSON.stringify({ id: n.id }) });
                n.read_at = new Date().toISOString(); this.count = Math.max(0, this.count - 1);
            } catch(e) {}
        },
        async markAllRead() {
            try {
                await fetch('/notifications/mark-all-read', { method:'POST', headers:{ 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json' } });
                this.items.forEach(n => n.read_at = new Date().toISOString()); this.count = 0;
            } catch(e) {}
        },
        showToast(n) {
            if (!n) return;
            const t = document.createElement('div');
            t.className = 'notification-toast';
            t.innerHTML = `<span style="font-size:1.5rem;flex-shrink:0">${n.data.icon||'🔔'}</span><div><div style="font-weight:700;font-size:.875rem;color:#0f172a">${n.data.title||'New notification'}</div><div style="font-size:.78rem;color:#64748b;margin-top:.2rem">${n.data.message||''}</div></div>`;
            document.body.appendChild(t);
            setTimeout(() => t.classList.add('show'), 10);
            setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 4500);
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    document.documentElement.classList.toggle('dark', localStorage.getItem('darkMode') === 'true');
    setTimeout(() => {
        document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
            el.style.transition = 'opacity .4s'; el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
});
</script>
</body>
</html>
