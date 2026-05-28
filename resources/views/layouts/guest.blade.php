<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$store.darkMode.on ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') — Virtual Pet Care</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.documentElement.classList.toggle('dark', localStorage.getItem('darkMode') === 'true');
    </script>
</head>
<body class="min-h-screen flex bg-gradient-to-br from-indigo-50/50 via-purple-50/50 to-teal-50/50 dark:from-slate-950 dark:via-slate-950 dark:to-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-200">

    {{-- Left decorative panel (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-5/12 flex-col justify-between p-12 relative overflow-hidden"
         style="background:linear-gradient(135deg,#4F46E5 0%,#7C3AED 60%,#6d28d9 100%)">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-20" style="background:rgba(255,255,255,.15)"></div>
            <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full opacity-10" style="background:rgba(255,255,255,.2)"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full opacity-5" style="background:white"></div>
        </div>

        {{-- Logo --}}
        <div class="relative flex items-center gap-3">
            <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center text-2xl backdrop-blur-sm">🐾</div>
            <div>
                <div class="font-bold text-white text-lg leading-tight">Virtual Pet Care</div>
                <div class="text-white/60 text-xs">Your pet's health companion</div>
            </div>
        </div>

        {{-- Center content --}}
        <div class="relative">
            <div class="text-6xl mb-6">🐕</div>
            <h2 class="text-3xl font-bold text-white mb-4 leading-tight">
                Care for your pets<br>the smart way
            </h2>
            <p class="text-white/70 text-base leading-relaxed mb-8">
                Track reminders, health records, vaccinations, and appointments — all in one beautiful place.
            </p>
            <div class="space-y-3">
                @foreach(['🔔 Smart daily reminders', '🏥 Health & vaccination tracking', '📅 Appointment calendar', '📊 Analytics dashboard'] as $f)
                    <div class="flex items-center gap-3 text-white/80 text-sm">
                        <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs">✓</div>
                        {{ $f }}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative text-white/40 text-xs">
            © {{ date('Y') }} Virtual Pet Care
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-lg"
                         style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">🐾</div>
                    <div class="text-left">
                        <div class="font-bold text-slate-900 dark:text-white text-lg">Virtual Pet Care</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500">Your pet's health companion</div>
                    </div>
                </a>
            </div>

            {{-- Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl shadow-slate-200/60 dark:shadow-slate-950/60 border border-slate-100 dark:border-slate-800 p-8">
                @yield('content')
            </div>

            {{-- Footer links --}}
            <div class="text-center mt-6 text-sm text-slate-400 dark:text-slate-500 flex items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
                <span class="text-slate-200 dark:text-slate-800">·</span>
                <a href="{{ route('contact') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Contact</a>
                <span class="text-slate-200 dark:text-slate-800">·</span>
                <a href="{{ route('about') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">About</a>
            </div>
        </div>
    </div>
</body>
</html>
