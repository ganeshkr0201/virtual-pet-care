<div class="flex items-center justify-between h-16 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="flex items-center space-x-6">
        
        <button @click="darkMode = !darkMode" 
                class="p-2 text-gray-500 transition rounded-full dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </button>

        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                <div class="overflow-hidden border-2 border-indigo-500 rounded-full w-9 h-9 dark:border-indigo-400 shrink-0">
                    <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" alt="Profile" class="object-cover w-full h-full">
                </div>
                <span class="hidden text-sm font-medium text-gray-700 md:block dark:text-gray-200">
                    {{ Auth::user()->name }}
                </span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            </div>
    </div>
</div>