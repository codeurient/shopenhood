<x-guest-layout>
    <x-slot name="title">Browse Listings — {{ config('app.name') }}</x-slot>

    <!-- Search Bar (mobile only — desktop uses header search) -->
    <div class="md:hidden">
        <x-mobile.search-bar />
    </div>

    <!-- Category Scroll -->
    <x-mobile.category-scroll :categories="$categories" />

    <!-- Filter Bar -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-screen-2xl mx-auto px-4 md:px-6 py-2 flex items-center justify-between gap-2">
            <!-- Active Filters -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide flex-1">
                @if(request('search'))
                    <a href="{{ route('listings.index', request()->except('search', 'page')) }}"
                       class="flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium flex-shrink-0 hover:bg-primary-200">
                        "{{ Str::limit(request('search'), 20) }}"
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
                @if(request('category'))
                    @php $cat = $categories->firstWhere('slug', request('category')); @endphp
                    @if($cat)
                        <a href="{{ route('listings.index', request()->except('category', 'page')) }}"
                           class="flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium flex-shrink-0 hover:bg-primary-200">
                            {{ $cat->name }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                @endif
            </div>

            <!-- Sort Dropdown -->
            <form method="GET" action="{{ route('listings.index') }}" class="flex-shrink-0">
                @foreach(request()->except('sort', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="sort"
                        onchange="this.form.submit()"
                        class="text-xs border-0 bg-gray-100 rounded-lg py-1.5 px-2 md:px-3 text-gray-700 focus:ring-2 focus:ring-primary-500 cursor-pointer">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low–High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="max-w-screen-2xl mx-auto px-4 md:px-6 pt-3 pb-6 md:pb-10">
        <!-- Results Count -->
        <p class="text-xs text-gray-500 mb-3">{{ number_format($listings->total()) }} listing{{ $listings->total() !== 1 ? 's' : '' }} found</p>

        @if($listings->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h2 class="text-base font-semibold text-gray-700 mb-1">No listings found</h2>
                <p class="text-sm text-gray-500 mb-6">Try adjusting your search or filters.</p>
                <a href="{{ route('listings.index') }}"
                   class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                    Clear Filters
                </a>
            </div>
        @else
            <!-- Listings Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-3 md:gap-4">
                @foreach($listings as $listing)
                    <x-mobile.listing-card :listing="$listing" />
                @endforeach
            </div>

            <!-- Pagination -->
            @if($listings->hasPages())
                <div class="mt-6 md:mt-8">
                    {{ $listings->links() }}
                </div>
            @endif
        @endif
    </div>
</x-guest-layout>
