<x-guest-layout>
    <!-- Listing Type Tabs -->
    <x-mobile.listing-type-tabs :listingTypes="$listingTypes" currentType="sell" />

    <!-- Category Horizontal Scroll -->
    <x-mobile.category-scroll :categories="$categories" />

    <!-- Home Slider -->
    <x-mobile.home-slider :mainSliders="$mainSliders" :smallBanners="$smallBanners" />

    <!-- Main Content -->
    <div class="space-y-6">

        <!-- Featured/Premium Listings Section (Horizontally Scrollable) -->
        @if($featuredListings->isNotEmpty())
        <section>
            <div class="flex items-center justify-between px-4 mb-3">
                <h2 class="text-lg font-bold text-gray-900">Premium Listings</h2>
                <a href="{{ route('listings.index', ['featured' => 1]) }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Horizontal Scroll Container -->
            <div class="overflow-x-auto scrollbar-hide px-4">
                <div class="flex gap-3 pb-2">
                    @foreach($featuredListings as $listing)
                        <div class="w-40 flex-shrink-0">
                            <x-mobile.listing-card :listing="$listing" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Recent Listings Section (Grid) -->
        @if($latestListings->isNotEmpty())
        <section>
            <div class="flex items-center justify-between px-4 mb-3">
                <h2 class="text-lg font-bold text-gray-900">Recent Listings</h2>
                <a href="{{ route('listings.index') }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Listings Grid -->
            <div class="grid grid-cols-2 gap-3 px-4">
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

    </div>

    @push('scripts')
    <script>
        // Filter Panel Toggle
        function toggleFilterPanel() {
            // Filter panel functionality to be implemented
            console.log('Filter panel clicked');
        }

        // Filter by Type (AJAX with Vanilla JavaScript)
        function filterByType(typeSlug) {
            // Update active tab
            document.querySelectorAll('.listing-type-tab').forEach(tab => {
                if (tab.dataset.type === typeSlug) {
                    tab.classList.add('bg-gray-800', 'border-b-2', 'border-primary-500');
                } else {
                    tab.classList.remove('bg-gray-800', 'border-b-2', 'border-primary-500');
                }
            });

            // Fetch listings by type using AJAX
            fetch(`{{ route('home') }}?type=${typeSlug}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update listings sections
                updateListingsSection('featured', data.featuredListings);
                updateListingsSection('recent', data.latestListings);
            })
            .catch(error => console.error('Error:', error));
        }

        function updateListingsSection(section, listings) {
            // Update the DOM with new listings
            // This will be fully implemented when we add the API endpoint
            console.log(`Updating ${section} section with`, listings);
        }

        // Smooth scroll for horizontal scroll containers
        document.addEventListener('DOMContentLoaded', function() {
            const scrollContainers = document.querySelectorAll('.scrollbar-hide');

            scrollContainers.forEach(container => {
                let isDown = false;
                let startX;
                let scrollLeft;

                container.addEventListener('mousedown', (e) => {
                    isDown = true;
                    container.classList.add('cursor-grabbing');
                    startX = e.pageX - container.offsetLeft;
                    scrollLeft = container.scrollLeft;
                });

                container.addEventListener('mouseleave', () => {
                    isDown = false;
                    container.classList.remove('cursor-grabbing');
                });

                container.addEventListener('mouseup', () => {
                    isDown = false;
                    container.classList.remove('cursor-grabbing');
                });

                container.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - container.offsetLeft;
                    const walk = (x - startX) * 2;
                    container.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>

    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    @endpush
</x-guest-layout>
