<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Virtual Pet Care</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body class="bg-slate-100 dark:bg-slate-950 min-h-screen">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"></div>

    {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col
                  bg-white dark:bg-slate-900
                  border-r border-slate-200/60 dark:border-slate-700/60
                  shadow-xl shadow-slate-200/40 dark:shadow-slate-900/60
                  transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100 dark:border-slate-800">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl shadow-lg flex-shrink-0"
                 style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">🐾</div>
            <div>
                <div class="font-bold text-slate-900 dark:text-white text-sm leading-tight">Virtual Pet Care</div>
                <div class="text-xs text-slate-400 font-medium">Pet Management</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <p class="text-xs font-700 text-slate-400 uppercase tracking-widest px-3 pt-1 pb-2">Main</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('pets.index') }}"
               class="sidebar-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                My Pets
                @php $pc = auth()->user()->pets()->count() @endphp
                @if($pc > 0)
                    <span class="ml-auto text-xs font-bold bg-primary-100 text-primary-700 rounded-full px-2 py-0.5">{{ $pc }}</span>
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
                <p class="text-xs font-700 text-slate-400 uppercase tracking-widest px-3 pb-2">Account</p>
            </div>

            <a href="{{ route('notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
                @php $uc = auth()->user()->unreadNotifications->count() @endphp
                @if($uc > 0)
                    <span class="ml-auto text-xs font-bold bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center">{{ $uc > 9 ? '9+' : $uc }}</span>
                @endif
            </a>

            <a href="{{ route('profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile
            </a>

            @if(auth()->user()->hasRole('admin'))
            <div class="pt-3 pb-1">
                <p class="text-xs font-700 text-slate-400 uppercase tracking-widest px-3 pb-2">Admin</p>
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
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                     class="w-9 h-9 rounded-xl object-cover ring-2 ring-primary-100 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md
                       border-b border-slate-200/60 dark:border-slate-700/60
                       px-4 lg:px-6 py-3 flex items-center gap-4 flex-shrink-0 z-10">

            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex-1">
                <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-1.5">

                {{-- Dark mode --}}
                <button @click="$store.darkMode.toggle()"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg x-show="!$store.darkMode.on" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="$store.darkMode.on" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                {{-- Notification bell --}}
                <div class="relative" x-data="notificationBell()" x-init="init()" @click.outside="open = false">
                    <button @click="toggle()"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span x-show="count > 0" x-text="count > 9 ? '9+' : count"
                              class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[1.1rem] h-[1.1rem] flex items-center justify-center px-1 leading-none"></span>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/60 dark:border-slate-700 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-800/50">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-900 dark:text-white">Notifications</span>
                                <span x-show="count > 0" x-text="count"
                                      class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button x-show="count > 0" @click="markAllRead()"
                                        class="text-xs text-primary-600 hover:text-primary-700 font-semibold">Mark all read</button>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-slate-400 hover:text-slate-600">View all</a>
                            </div>
                        </div>

                        <div x-show="loading" class="py-8 flex justify-center">
                            <div class="w-5 h-5 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                        </div>

                        <div x-show="!loading && items.length === 0" class="py-10 text-center">
                            <div class="text-3xl mb-2">🔔</div>
                            <p class="text-slate-400 text-sm font-medium">All caught up!</p>
                        </div>

                        <div x-show="!loading && items.length > 0" class="max-h-72 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800">
                            <template x-for="n in items" :key="n.id">
                                <div @click="markRead(n)" class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors"
                                     :class="!n.read_at ? 'bg-primary-50/40 dark:bg-primary-900/10' : ''">
                                    <div class="flex items-start gap-3">
                                        <span class="text-lg flex-shrink-0 mt-0.5" x-text="n.data.icon || '🔔'"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate" x-text="n.data.title || 'Notification'"></p>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.data.message || ''"></p>
                                            <p class="text-xs text-slate-400 mt-1" x-text="n.created_at"></p>
                                        </div>
                                        <div x-show="!n.read_at" class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0 mt-1.5"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="!loading && items.length > 0"
                             class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50">
                            <a href="{{ route('notifications.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">See all notifications →</a>
                        </div>
                    </div>
                </div>

                {{-- Avatar --}}
                <a href="{{ route('profile.edit') }}" class="ml-1">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-xl object-cover ring-2 ring-primary-200 hover:ring-primary-400 transition-all">
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-4 lg:mx-6 mt-4 alert-success" style="animation:slideUp .3s ease-out" data-auto-dismiss>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 lg:mx-6 mt-4 alert-error" style="animation:slideUp .3s ease-out" data-auto-dismiss>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6 page-transition">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script>
function notificationBell() {
    return {
        open: false, loading: false,
        count: {{ auth()->user()->unreadNotifications->count() }},
        items: [], timer: null,
        init() {
            this.fetch();
            this.timer = setInterval(() => this.fetch(), 10000);
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
