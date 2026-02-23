<!-- Bottom Navigation (Mobile Only) -->
<nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-lg md:hidden">
    <div class="flex items-center justify-around h-16">
        <!-- Home -->
        <a href="{{ route('home') }}"
           class="flex flex-col items-center justify-center flex-1 h-full space-y-1 {{ request()->routeIs('home') ? 'text-primary-600' : 'text-gray-600' }} hover:text-primary-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs font-medium">HOME</span>
        </a>

        <!-- Favorites -->
        @auth
            <a href="{{ route('user.favorites.index') }}"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 {{ request()->routeIs('user.favorites.index') ? 'text-primary-600' : 'text-gray-600' }} hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('user.favorites.index') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-xs font-medium">FAVORITES</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-xs font-medium">FAVORITES</span>
            </a>
        @endauth

        <!-- Add Listing (Center Button) -->
        @auth
            <a href="{{ route('user.listings.create') }}"
               class="flex flex-col items-center justify-center flex-1 h-full -mt-8">
                <div class="flex items-center justify-center w-14 h-14 bg-primary-600 rounded-full shadow-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-primary-600 mt-1">ADD</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full -mt-8">
                <div class="flex items-center justify-center w-14 h-14 bg-primary-600 rounded-full shadow-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-primary-600 mt-1">ADD</span>
            </a>
        @endauth

        <!-- Messages -->
        @auth
            <a href="#messages"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs font-medium">MESSAGES</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs font-medium">MESSAGES</span>
            </a>
        @endauth

        <!-- Account -->
        @auth
            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs font-medium">ACCOUNT</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full space-y-1 text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="text-xs font-medium">LOGIN</span>
            </a>
        @endauth
    </div>
</nav>
