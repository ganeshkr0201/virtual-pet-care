<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About — Virtual Pet Care</title>
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
    <div class="pt-24 pb-20 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-4">About Virtual Pet Care</h1>
        <p class="text-xl text-slate-500 mb-8">We believe every pet deserves the best care possible.</p>
        <div class="prose prose-slate max-w-none">
            <p>Virtual Pet Care was built by pet lovers, for pet lovers. We understand how challenging it can be to keep track of multiple pets' care routines, medical histories, and appointments.</p>
            <p>Our mission is to make pet care management effortless, so you can spend more quality time with your furry, feathered, or scaly companions.</p>
            <h2>Our Values</h2>
            <ul>
                <li><strong>Pet-first design</strong> — Every feature is built with your pet's wellbeing in mind.</li>
                <li><strong>Simplicity</strong> — Powerful features that are easy to use.</li>
                <li><strong>Privacy</strong> — Your data is yours. We never sell it.</li>
                <li><strong>Reliability</strong> — 99.9% uptime so reminders always get through.</li>
            </ul>
        </div>
    </div>
</body>
</html>
