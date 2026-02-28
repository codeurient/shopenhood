<x-guest-layout>
    <!-- Listing Type Tabs -->
    <x-mobile.listing-type-tabs :listingTypes="$listingTypes" currentType="sell" />

    <!-- Category Horizontal Scroll -->
    <x-mobile.category-scroll :categories="$categories" />

    <!-- Home Slider -->
    <x-mobile.home-slider :mainSliders="$mainSliders" :smallBanners="$smallBanners" />

    <!-- Main Content -->
    <div class="space-y-6 md:space-y-10 pb-6 md:pb-10">

        <!-- Featured/Premium Listings Section -->
        @if($featuredListings->isNotEmpty())
        <section x-data="premiumCarousel()">
            <div class="flex items-center justify-between px-4 md:px-6 mb-3 max-w-[1250px] mx-auto">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">Premium Listings</h2>
                <a href="{{ route('listings.index', ['featured' => 1]) }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Carousel wrapper -->
            <div class="relative max-w-[1250px] mx-auto overflow-hidden px-4 md:px-6">
                <!-- Track: touch scroll on mobile, overflow-hidden (button-controlled) on desktop -->
                <div class="overflow-x-auto md:overflow-hidden scrollbar-hide"
                     x-ref="track"
                     data-carousel>
                    <div class="flex gap-3 pb-2">
                        @foreach($featuredListings as $listing)
                            <div class="w-40 md:w-48 flex-shrink-0">
                                <x-mobile.listing-card :listing="$listing" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Prev Button (desktop only) -->
                <button @click="prev()"
                        :disabled="!canScrollPrev"
                        class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center bg-white shadow-md rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:shadow-lg transition-all disabled:opacity-30 disabled:cursor-default disabled:hover:bg-white disabled:hover:shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Next Button (desktop only) -->
                <button @click="next()"
                        :disabled="!canScrollNext"
                        class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 items-center justify-center bg-white shadow-md rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:shadow-lg transition-all disabled:opacity-30 disabled:cursor-default disabled:hover:bg-white disabled:hover:shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>
        @endif

        <!-- Recent Listings Section -->
        @if($latestListings->isNotEmpty())
        <section>
            <div class="flex items-center justify-between px-4 md:px-6 mb-3 max-w-[1250px] mx-auto">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">Recent Listings</h2>
                <a href="{{ route('listings.index') }}"
                   class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    View All
                </a>
            </div>

            <!-- Listings Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-3 md:gap-4 px-4 md:px-6 max-w-[1250px] mx-auto">
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
        function premiumCarousel() {
            return {
                canScrollPrev: false,
                canScrollNext: true,

                init() {
                    this.$nextTick(() => this.updateButtons());
                },

                updateButtons() {
                    const t = this.$refs.track;
                    this.canScrollPrev = t.scrollLeft > 1;
                    this.canScrollNext = t.scrollLeft < t.scrollWidth - t.clientWidth - 1;
                },

                cardWidth() {
                    const card = this.$refs.track.children[0]?.children[0];
                    return card ? card.offsetWidth + 12 : 204;
                },

                prev() {
                    if (!this.canScrollPrev) { return; }
                    this.$refs.track.scrollBy({ left: -this.cardWidth(), behavior: 'smooth' });
                    setTimeout(() => this.updateButtons(), 400);
                },

                next() {
                    if (!this.canScrollNext) { return; }
                    this.$refs.track.scrollBy({ left: this.cardWidth(), behavior: 'smooth' });
                    setTimeout(() => this.updateButtons(), 400);
                },
            };
        }

        function toggleFilterPanel() {
            console.log('Filter panel clicked');
        }

        function filterByType(typeSlug) {
            document.querySelectorAll('.listing-type-tab').forEach(tab => {
                if (tab.dataset.type === typeSlug) {
                    tab.classList.add('bg-gray-800', 'border-b-2', 'border-primary-500');
                } else {
                    tab.classList.remove('bg-gray-800', 'border-b-2', 'border-primary-500');
                }
            });

            fetch(`{{ route('home') }}?type=${typeSlug}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateListingsSection('featured', data.featuredListings);
                updateListingsSection('recent', data.latestListings);
            })
            .catch(error => console.error('Error:', error));
        }

        function updateListingsSection(section, listings) {
            console.log(`Updating ${section} section with`, listings);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scrollContainers = document.querySelectorAll('.scrollbar-hide');

            scrollContainers.forEach(container => {
                if (container.hasAttribute('data-carousel')) { return; }
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
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush
</x-guest-layout>
