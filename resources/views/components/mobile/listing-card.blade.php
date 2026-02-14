@props(['listing'])

<!-- Listing Card -->
<a href="{{ route('listings.show', $listing->slug) }}"
   class="block bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">

    <!-- Image Container -->
    <div class="relative aspect-square bg-gray-100">
        @if($listing->primaryImage || $listing->firstImage)
            @php
                $image = $listing->primaryImage ?? $listing->firstImage;
            @endphp
            <img src="{{ asset('storage/' . $image->image_path) }}"
                 alt="{{ $listing->title }}"
                 class="w-full h-full object-cover">
        @else
            <!-- Placeholder Image -->
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        <!-- Favorite Button -->
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation();"
                class="absolute top-3 right-3 flex items-center justify-center w-9 h-9 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all">
            <svg class="w-5 h-5 text-gray-700 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>

        <!-- Store Badge (if from business user) -->
        @if($listing->user && method_exists($listing->user, 'isBusinessUser') && $listing->user->isBusinessUser())
            <div class="absolute bottom-3 left-3">
                <span class="px-3 py-1 text-xs font-semibold text-white bg-primary-600 rounded-full shadow-md">
                    Store
                </span>
            </div>
        @endif

        <!-- Featured Badge -->
        @if($listing->is_featured)
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 text-xs font-semibold text-white bg-yellow-500 rounded-full shadow-md">
                    ⭐ Featured
                </span>
            </div>
        @endif
    </div>

    <!-- Card Content -->
    <div class="p-3 space-y-2">
        <!-- Price -->
        <div class="flex items-center gap-2">
            @if($listing->discount_price && $listing->discount_start_date && $listing->discount_end_date && now()->between($listing->discount_start_date, $listing->discount_end_date))
                <span class="text-xl font-bold text-primary-600">
                    ${{ number_format($listing->discount_price, 2) }}
                </span>
                <span class="text-sm text-gray-500 line-through">
                    ${{ number_format($listing->base_price, 2) }}
                </span>
            @elseif($listing->base_price)
                <span class="text-xl font-bold text-gray-900">
                    ${{ number_format($listing->base_price, 2) }}
                </span>
            @else
                <span class="text-sm font-medium text-gray-600">
                    Contact for price
                </span>
            @endif

            @if($listing->is_negotiable)
                <span class="text-xs text-gray-500">(Negotiable)</span>
            @endif
        </div>

        <!-- Title -->
        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-tight">
            {{ $listing->title }}
        </h3>

        <!-- Location & Time -->
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $listing->city ?? 'Location not specified' }}
            </span>
            <span>{{ $listing->created_at->diffForHumans() }}</span>
        </div>

        <!-- Icons (Condition, Wholesale, etc.) -->
        <div class="flex items-center gap-2 pt-1">
            @if($listing->condition === 'new')
                <span class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    New
                </span>
            @endif

            @if($listing->is_wholesale)
                <span class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded-full">
                    Wholesale
                </span>
            @endif

            @if($listing->has_delivery)
                <span class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-purple-700 bg-purple-50 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                    </svg>
                    Delivery
                </span>
            @endif
        </div>
    </div>
</a>
