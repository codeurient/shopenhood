<x-guest-layout>
    <!-- Search Bar -->
    <x-mobile.search-bar />

    <!-- Category Horizontal Scroll -->
    <x-mobile.category-scroll :categories="$categories" />

    <!-- Main Content -->
    <div class="px-4 py-6 space-y-8">

        <!-- Featured Listings Section -->
        @if($featuredListings->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Premium Listings</h2>
                <a href="{{ route('listings.index', ['featured' => 1]) }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Listings Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach($featuredListings as $listing)
                    <x-mobile.listing-card :listing="$listing" />
                @endforeach
            </div>
        </section>
        @endif

        <!-- Latest Listings Section -->
        @if($latestListings->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Recent Listings</h2>
                <a href="{{ route('listings.index') }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Listings Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach($latestListings as $listing)
                    <x-mobile.listing-card :listing="$listing" />
                @endforeach
            </div>
        </section>
        @endif

        <!-- Empty State -->
        @if($featuredListings->isEmpty() && $latestListings->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4">
            <div class="w-32 h-32 mb-6 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Listings Yet</h3>
            <p class="text-sm text-gray-600 text-center mb-6 max-w-sm">
                Be the first to add a listing to our marketplace!
            </p>
            @auth
                <a href="{{ route('user.listings.create') }}"
                   class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md">
                    Add Your First Listing
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-md">
                    Sign In to Add Listing
                </a>
            @endauth
        </div>
        @endif

        <!-- Categories Grid (Optional - for better UX) -->
        @if($categories->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Browse by Category</h2>
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach($categories->take(6) as $category)
                <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
                   class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all group">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-12 h-12 mb-3 object-contain group-hover:scale-110 transition-transform">
                    @else
                        <div class="w-12 h-12 mb-3 bg-gradient-to-br from-primary-200 to-primary-300 rounded-xl"></div>
                    @endif
                    <h3 class="text-sm font-semibold text-gray-900 text-center">{{ $category->name }}</h3>
                    @if($category->listings_count > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ $category->listings_count }} items</p>
                    @endif
                </a>
                @endforeach
            </div>
        </section>
        @endif

    </div>

    @push('scripts')
    <script>
        // Smooth scroll for category navigation
        document.addEventListener('DOMContentLoaded', function() {
            const categoryScroll = document.querySelector('.overflow-x-auto');
            if (categoryScroll) {
                let isDown = false;
                let startX;
                let scrollLeft;

                categoryScroll.addEventListener('mousedown', (e) => {
                    isDown = true;
                    categoryScroll.classList.add('cursor-grabbing');
                    startX = e.pageX - categoryScroll.offsetLeft;
                    scrollLeft = categoryScroll.scrollLeft;
                });

                categoryScroll.addEventListener('mouseleave', () => {
                    isDown = false;
                    categoryScroll.classList.remove('cursor-grabbing');
                });

                categoryScroll.addEventListener('mouseup', () => {
                    isDown = false;
                    categoryScroll.classList.remove('cursor-grabbing');
                });

                categoryScroll.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - categoryScroll.offsetLeft;
                    const walk = (x - startX) * 2;
                    categoryScroll.scrollLeft = scrollLeft - walk;
                });
            }
        });
    </script>
    @endpush
</x-guest-layout>
