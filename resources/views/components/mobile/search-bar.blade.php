<!-- Mobile Search Bar -->
<div class="px-4 py-3 bg-white">
    <form action="{{ route('listings.index') }}" method="GET" class="relative">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search for items or services..."
               class="w-full pl-11 pr-4 py-3 text-sm bg-gray-100 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
        <button type="submit"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </form>
</div>
