<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact — Virtual Pet Care</title>
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
    <div class="pt-24 pb-20 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-4">Contact Us</h1>
        <p class="text-xl text-slate-500 mb-8">Have a question or feedback? We'd love to hear from you.</p>

        @if(session('success'))
            <div class="alert-success mb-6">{{ session('success') }}</div>
        @endif

        <div class="card p-6">
            <form method="POST" action="{{ route('contact') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" required class="form-input" placeholder="Your name">
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" required class="form-input" placeholder="you@example.com">
                    </div>
                </div>
                <div>
                    <label class="form-label">Subject *</label>
                    <input type="text" name="subject" required class="form-input" placeholder="How can we help?">
                </div>
                <div>
                    <label class="form-label">Message *</label>
                    <textarea name="message" rows="5" required class="form-input" placeholder="Tell us more..."></textarea>
                </div>
                <button type="submit" class="btn-primary btn-lg">Send Message</button>
            </form>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 text-center">
                <div class="text-2xl mb-2">📧</div>
                <p class="font-medium text-slate-900">Email</p>
                <p class="text-sm text-slate-500">support@virtualpetcare.com</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 text-center">
                <div class="text-2xl mb-2">⏱️</div>
                <p class="font-medium text-slate-900">Response Time</p>
                <p class="text-sm text-slate-500">Within 24 hours</p>
            </div>
        </div>
    </div>
</body>
</html>
