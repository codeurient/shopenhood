<!-- Mobile Header -->
<header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Hamburger Menu Button -->
        <button @click="sidebarOpen = true"
                type="button"
                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center">
            <span class="text-2xl font-bold text-primary-600">{{ config('app.name', 'Shopenhood') }}</span>
        </a>

        <!-- Add Listing Button -->
        @auth
            <a href="{{ route('user.listings.create') }}"
               class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-600 hover:bg-primary-700 transition-colors duration-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-600 hover:bg-primary-700 transition-colors duration-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
        @endauth
    </div>
</header>
