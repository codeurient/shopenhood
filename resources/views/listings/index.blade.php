<x-guest-layout>
    <x-slot name="title">Browse Listings — {{ config('app.name') }}</x-slot>

    <!-- Category Scroll -->
    <x-mobile.category-scroll :categories="$categories" />

    <!-- Filter Bar -->
    <div class="bg-white border-b border-[#E0E0E0]">
        <div class="max-w-[1250px] mx-auto px-4 md:px-6 py-2 flex items-center justify-between gap-2">
            <!-- Active Filter Chips -->
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide flex-1">

                @php
                    $activeFilters = array_filter([
                        'search'    => request('search'),
                        'category'  => request('category'),
                        'type'      => request('type'),
                        'condition' => request('condition'),
                        'country'   => request('country'),
                        'city'      => request('city'),
                        'min_price' => request('min_price'),
                        'max_price' => request('max_price'),
                    ]);
                    $hasVariants = count(request('variant', [])) > 0;
                @endphp

                @if(count($activeFilters) > 0 || $hasVariants)
                    <!-- Clear all -->
                    <a href="{{ route('listings.index') }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] border border-[#E0E0E0] text-[#37474F] rounded text-[11px] font-medium flex-shrink-0 hover:border-[#D4AF37] hover:text-[#D4AF37] transition-colors">
                        <i class="fa-solid fa-xmark text-[9px]"></i>
                        Clear all
                    </a>
                @endif

                @if(request('search'))
                    <a href="{{ route('listings.index', request()->except('search', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-[9px] text-[#37474F]"></i>
                        "{{ Str::limit(request('search'), 18) }}"
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if(request('type'))
                    @php $type = $listingTypes->firstWhere('id', (int) request('type')); @endphp
                    @if($type)
                        <a href="{{ route('listings.index', request()->except('type', 'page')) }}"
                           class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                            {{ $type->name }}
                            <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                        </a>
                    @endif
                @endif

                @if(request('category'))
                    @php
                        $activeCat = \App\Models\Category::where('slug', request('category'))->first();
                    @endphp
                    @if($activeCat)
                        <a href="{{ route('listings.index', request()->except('category', 'page')) }}"
                           class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                            {{ $activeCat->name }}
                            <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                        </a>
                    @endif
                @endif

                @if(request('condition'))
                    <a href="{{ route('listings.index', request()->except('condition', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        {{ ucfirst(request('condition')) }}
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if(request('country'))
                    <a href="{{ route('listings.index', request()->except('country', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        <i class="fa-solid fa-earth-americas text-[9px] text-[#37474F]"></i>
                        {{ request('country') }}
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if(request('city'))
                    <a href="{{ route('listings.index', request()->except('city', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        <i class="fa-solid fa-location-dot text-[9px] text-[#37474F]"></i>
                        {{ request('city') }}
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if(request('min_price') || request('max_price'))
                    <a href="{{ route('listings.index', request()->except('min_price', 'max_price', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        @if(request('min_price') && request('max_price'))
                            {{ request('min_price') }}–{{ request('max_price') }}
                        @elseif(request('min_price'))
                            Min {{ request('min_price') }}
                        @else
                            Max {{ request('max_price') }}
                        @endif
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if($hasVariants)
                    <a href="{{ route('listings.index', request()->except('variant', 'page')) }}"
                       class="flex items-center gap-1 px-2.5 h-[24px] bg-[#D4AF37]/10 border border-[#D4AF37] text-[#1A1A1A] rounded text-[11px] font-medium flex-shrink-0 hover:bg-[#D4AF37]/20 transition-colors">
                        {{ count(request('variant', [])) }} {{ Str::plural('attribute', count(request('variant', []))) }}
                        <i class="fa-solid fa-xmark text-[9px] text-[#37474F]"></i>
                    </a>
                @endif

                @if(count($activeFilters) === 0 && !$hasVariants)
                    <button type="button"
                            onclick="toggleFilterPanel()"
                            class="flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition-colors">
                        <i class="fa-solid fa-filter text-[11px]"></i>
                        Add filters
                    </button>
                @endif
            </div>

            <!-- Sort Dropdown -->
            <form method="GET" action="{{ route('listings.index') }}" class="flex-shrink-0">
                @foreach(request()->except('sort', 'page') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $subKey => $subVal)
                            <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subVal }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="sort"
                        onchange="this.form.submit()"
                        class="text-[12px] border border-[#E0E0E0] bg-white rounded py-1.5 px-2 md:px-3 text-[#37474F] focus:outline-none focus:ring-0 focus:border-[#D4AF37] cursor-pointer transition-colors">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low–High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="max-w-[1250px] mx-auto px-4 md:px-6 pt-3 pb-6 md:pb-10">
        <!-- Results Count -->
        <p class="text-[12px] text-[#37474F] mb-3">{{ number_format($listings->total()) }} listing{{ $listings->total() !== 1 ? 's' : '' }} found</p>

        @if($listings->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="fa-regular fa-folder-open text-[#E0E0E0] mb-4" style="font-size: 3.5rem;"></i>
                <h2 class="text-base font-semibold text-[#1A1A1A] mb-1">No listings found</h2>
                <p class="text-sm text-[#37474F] mb-6">Try adjusting your search or filters.</p>
                <a href="{{ route('listings.index') }}"
                   class="inline-flex items-center justify-center px-5 h-[38px] bg-[#D4AF37] text-[#000000] text-sm font-semibold rounded hover:brightness-110 transition-colors">
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
