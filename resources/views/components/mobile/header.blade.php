<!-- Mobile Header -->
<header class="sticky top-0 z-40 bg-gray-800 shadow-sm">
    <div class="flex items-center gap-3 px-4 py-3">
        <!-- Hamburger Menu Button -->
        <button @click="sidebarOpen = true"
                type="button"
                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-700 transition-colors duration-200 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Search Bar with Filter -->
        <div class="flex-1 relative">
            <form action="{{ route('listings.index') }}" method="GET" class="relative">
                <!-- Search Icon & Logo -->
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search..."
                       class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">

                <!-- Filter Icon Button -->
                <button type="button"
                        onclick="toggleFilterPanel()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary-600 transition-colors">
                    <i class="fa-solid fa-filter text-lg"></i>
                </button>
            </form>
        </div>

        <!-- Favorites Icon with Badge -->
        <a href="#favorites" class="relative flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-700 transition-colors duration-200 flex-shrink-0">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2 12h10l2-8H6M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>

            <!-- Notification Badge -->
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-danger-500 rounded-full">
                2
            </span>
        </a>
    </div>
</header>
