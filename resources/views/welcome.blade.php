<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Virtual Pet Care — Your Pet's Health Companion</title>
    <meta name="description" content="Manage your pet's daily care with smart reminders, health tracking, and appointment scheduling.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-600 to-secondary-500 rounded-xl flex items-center justify-center text-white text-lg">🐾</div>
                    <span class="font-bold text-slate-900 text-lg">Virtual Pet Care</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm text-slate-600 hover:text-primary-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="text-sm text-slate-600 hover:text-primary-600 transition-colors">How it works</a>
                    <a href="#testimonials" class="text-sm text-slate-600 hover:text-primary-600 transition-colors">Testimonials</a>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost btn-sm">Sign in</a>
                        <a href="{{ route('register') }}" class="btn-primary btn-sm">Get started free</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-primary-50 text-primary-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
                <span class="w-2 h-2 bg-primary-600 rounded-full animate-pulse"></span>
                Trusted by 10,000+ pet owners
            </div>
            <h1 class="text-5xl sm:text-6xl font-bold text-slate-900 leading-tight mb-6">
                The smarter way to<br>
                <span class="gradient-text">care for your pets</span>
            </h1>
            <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
                Never miss a feeding, medication, or vet appointment again. Virtual Pet Care keeps your furry family members healthy and happy with intelligent reminders and health tracking.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-primary btn-lg text-base px-8">
                    Start for free — no credit card
                </a>
                <a href="#features" class="btn-secondary btn-lg text-base px-8">
                    See how it works
                </a>
            </div>
            <p class="text-sm text-slate-400 mt-4">Free forever for up to 3 pets</p>
        </div>

        <!-- Hero Image / Stats -->
        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-3xl mx-auto">
            @foreach([['10K+', 'Happy pet owners'], ['50K+', 'Reminders sent'], ['99%', 'Uptime'], ['4.9★', 'App rating']] as [$num, $label])
                <div class="text-center p-5 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-2xl font-bold text-primary-600">{{ $num }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Everything your pet needs</h2>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto">A complete platform for managing your pet's health, care routines, and medical history.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['🔔', 'Smart Reminders', 'Set up daily, weekly, or custom reminders for feeding, medication, grooming, and more. Never miss a care routine.', 'bg-primary-50'],
                    ['🏥', 'Health Tracking', 'Log weight, medical records, vaccinations, and symptoms. Get a complete health timeline for each pet.', 'bg-teal-50'],
                    ['📅', 'Appointment Calendar', 'Schedule and track vet appointments, grooming sessions, and vaccinations with a beautiful calendar view.', 'bg-purple-50'],
                    ['💊', 'Medication Management', 'Track medications, dosages, and schedules. Get alerts when it\'s time for the next dose.', 'bg-orange-50'],
                    ['📊', 'Analytics Dashboard', 'Visualize your pet\'s care consistency with charts and insights. See trends and patterns over time.', 'bg-blue-50'],
                    ['🐾', 'Multi-Pet Support', 'Manage all your pets in one place. Each pet gets their own profile, health records, and reminders.', 'bg-pink-50'],
                ] as [$icon, $title, $desc, $bg])
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 hover:shadow-soft transition-all duration-200 hover:-translate-y-0.5">
                        <div class="w-12 h-12 {{ $bg }} rounded-2xl flex items-center justify-center text-2xl mb-4">{{ $icon }}</div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $title }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Get started in minutes</h2>
                <p class="text-xl text-slate-500">Three simple steps to better pet care</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['1', '🐕', 'Add your pets', 'Create profiles for each of your pets with their species, breed, age, and medical information.'],
                    ['2', '⏰', 'Set up reminders', 'Configure daily care reminders for feeding, medication, walks, and more. Customize the schedule to fit your routine.'],
                    ['3', '📱', 'Stay on track', 'Receive timely notifications and track completion. View analytics to see how well you\'re keeping up with care routines.'],
                ] as [$step, $icon, $title, $desc])
                    <div class="text-center">
                        <div class="relative inline-flex">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-600 to-secondary-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg mb-6">{{ $icon }}</div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-slate-900 text-white rounded-full text-xs font-bold flex items-center justify-center">{{ $step }}</div>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ $title }}</h3>
                        <p class="text-slate-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Loved by pet owners</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['Sarah M.', 'Dog owner', 'This app completely changed how I care for my two dogs. The medication reminders alone have been a lifesaver!', '⭐⭐⭐⭐⭐'],
                    ['James K.', 'Cat parent', 'I love how I can track all my cats\' vet visits and vaccinations in one place. The calendar view is beautiful.', '⭐⭐⭐⭐⭐'],
                    ['Priya S.', 'Multi-pet owner', 'Managing 4 pets was overwhelming until I found this app. Now everything is organized and I never miss a reminder.', '⭐⭐⭐⭐⭐'],
                ] as [$name, $role, $quote, $stars])
                    <div class="bg-white rounded-2xl p-6 border border-slate-100">
                        <p class="text-slate-600 leading-relaxed mb-4">"{{ $quote }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-secondary-400 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ $name }}</p>
                                <p class="text-sm text-slate-400">{{ $role }}</p>
                            </div>
                            <div class="ml-auto text-sm">{{ $stars }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-gradient-to-r from-primary-600 to-secondary-500 rounded-3xl p-12 text-white">
                <h2 class="text-4xl font-bold mb-4">Start caring better today</h2>
                <p class="text-primary-100 text-lg mb-8">Join thousands of pet owners who trust Virtual Pet Care to keep their pets healthy and happy.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-primary-700 font-semibold px-8 py-4 rounded-2xl hover:bg-primary-50 transition-colors text-lg">
                    Get started for free
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-secondary-500 rounded-xl flex items-center justify-center text-white">🐾</div>
                    <span class="font-bold text-white">Virtual Pet Care</span>
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('features') }}" class="hover:text-white transition-colors">Features</a>
                    <a href="{{ route('about') }}" class="hover:text-white transition-colors">About</a>
                    <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign in</a>
                </div>
                <p class="text-sm">© {{ date('Y') }} Virtual Pet Care. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
