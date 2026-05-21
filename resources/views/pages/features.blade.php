<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Features — Virtual Pet Care</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-primary-600 to-secondary-500 rounded-xl flex items-center justify-center text-white text-lg">🐾</div>
                <span class="font-bold text-slate-900">Virtual Pet Care</span>
            </a>
            <a href="{{ route('register') }}" class="btn-primary btn-sm">Get started</a>
        </div>
    </nav>
    <div class="pt-24 pb-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-4">Features</h1>
        <p class="text-xl text-slate-500 mb-12">Everything you need to keep your pets healthy and happy.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach([
                ['🔔', 'Smart Reminders', 'Daily, weekly, and custom reminders for all pet care activities.'],
                ['🏥', 'Health Records', 'Complete medical history, vaccinations, and weight tracking.'],
                ['📅', 'Calendar View', 'Visual calendar for all appointments and reminders.'],
                ['💊', 'Medication Tracking', 'Never miss a dose with medication reminders.'],
                ['📊', 'Analytics', 'Charts and insights on care consistency.'],
                ['🐾', 'Multi-Pet', 'Manage unlimited pets from one account.'],
                ['📧', 'Email Alerts', 'Get notified via email for important reminders.'],
                ['🌙', 'Dark Mode', 'Easy on the eyes with a beautiful dark theme.'],
            ] as [$icon, $title, $desc])
                <div class="p-5 rounded-2xl border border-slate-100 hover:shadow-soft transition-all">
                    <div class="text-3xl mb-3">{{ $icon }}</div>
                    <h3 class="font-semibold text-slate-900 mb-1">{{ $title }}</h3>
                    <p class="text-slate-500 text-sm">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
