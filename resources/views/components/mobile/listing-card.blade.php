@props(['listing'])

@php
    // Image: listing images first, then default variation images as fallback
    $cardImage = $listing->primaryImage
        ?? $listing->firstImage
        ?? $listing->defaultVariation?->primaryImage
        ?? $listing->defaultVariation?->firstImage;

    // Price: listing base_price first, then default variation price as fallback
    $cardPrice = null;
    $cardDiscountPrice = null;
    if ($listing->base_price) {
        $cardPrice = $listing->base_price;
        if (
            $listing->discount_price &&
            $listing->discount_start_date &&
            $listing->discount_end_date &&
            now()->between($listing->discount_start_date, $listing->discount_end_date)
        ) {
            $cardDiscountPrice = $listing->discount_price;
        }
    } elseif ($listing->defaultVariation) {
        $v = $listing->defaultVariation;
        $cardPrice = $v->price;
        if ($v->hasActiveDiscount()) {
            $cardDiscountPrice = $v->discount_price;
        }
    }

    // Location: country/city strings first, then location relationship as fallback
    $displayCode = null;
    $displayCity = null;
    if ($listing->country && $listing->city) {
        $displayCode = $listing->country_code;
        $displayCity = $listing->city;
    } elseif ($listing->location) {
        $displayCity = $listing->location->name;
        if ($listing->location->parent) {
            $displayCode = $listing->location->parent->code ?? substr($listing->location->parent->name, 0, 2);
        }
    }
@endphp

<!-- Listing Card -->
<a href="{{ route('listings.show', $listing->slug) }}"
   class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">

    <!-- Image Container -->
    <div class="relative aspect-square bg-gray-100">
        @if($cardImage)
            <img src="{{ asset('storage/' . $cardImage->image_path) }}"
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

        <!-- Delivery Icon (Left Side) -->
        @if($listing->has_delivery)
            <div class="absolute top-2 left-2">
                <i class="fa-solid fa-truck text-white" style="font-size: 25px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
            </div>
        @endif

        <!-- Right Side Icons Container -->
        <div class="absolute top-2 right-2 flex flex-col gap-2">
            <!-- Favorite Button -->
            <button type="button"
                    onclick="event.preventDefault(); event.stopPropagation();"
                    class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all">
                <i class="fa-regular fa-heart text-gray-700 hover:text-red-500 transition-colors" style="font-size: 18px;"></i>
            </button>

            <!-- Price Negotiable Icon -->
            @if($listing->is_negotiable)
                <button type="button"
                        onclick="event.preventDefault(); event.stopPropagation();"
                        class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all">
                    <i class="fa-solid fa-handshake text-gray-700" style="font-size: 16px;"></i>
                </button>
            @endif
        </div>

        <!-- Store Badge & Wholesale Icon (Bottom) -->
        <div class="absolute bottom-2 left-2 flex items-center gap-2">
            @if($listing->user && method_exists($listing->user, 'isBusinessUser') && $listing->user->isBusinessUser())
                <span class="px-2 py-0.5 text-xs font-semibold text-white bg-primary-600 rounded-md shadow-md">
                    Store
                </span>
            @endif

            @if($listing->is_by_order)
                <span class="px-2 py-0.5 text-xs font-semibold text-white bg-gray-700 rounded-md shadow-md">
                    By Order
                </span>
            @endif

            @if($listing->is_wholesale && $listing->is_by_order)
                <i class="fa-brands fa-shirtsinbulk text-white" style="font-size: 25px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
            @endif
        </div>
    </div>

    <!-- Card Content -->
    <div class="p-2 space-y-1">
        <!-- Price -->
        <div class="flex items-center gap-1">
            @if($cardDiscountPrice)
                <span class="font-bold text-primary-600" style="font-size: 15px;">
                    ${{ number_format($cardDiscountPrice, 2) }}
                </span>
                <span class="text-gray-500 line-through" style="font-size: 11px;">
                    ${{ number_format($cardPrice, 2) }}
                </span>
            @elseif($cardPrice)
                <span class="font-bold text-gray-900" style="font-size: 15px;">
                    ${{ number_format($cardPrice, 2) }}
                </span>
            @else
                <span class="font-medium text-gray-600" style="font-size: 11px;">
                    Contact for price
                </span>
            @endif
        </div>

        <!-- Title -->
        <h3 class="font-semibold text-gray-900 truncate leading-tight" style="font-size: 11px;">
            {{ $listing->title }}
        </h3>

        <!-- Location & Condition -->
        <div class="flex items-center justify-between text-gray-500" style="font-size: 11px;">
            <!-- Left: Country Code, City -->
            <span class="truncate">
                @if($displayCode && $displayCity)
                    {{ $displayCode }}, {{ $displayCity }}
                @else
                    Location N/A
                @endif
            </span>

            <!-- Right: Condition -->
            @if($listing->condition === 'new')
                <span class="flex items-center gap-0.5 px-1.5 py-0.5 font-medium text-green-700 bg-green-50 rounded-md ml-1 flex-shrink-0">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    New
                </span>
            @elseif($listing->condition === 'second_hand')
                <span class="px-1.5 py-0.5 font-medium text-yellow-700 bg-yellow-50 rounded-md ml-1 flex-shrink-0">
                    Second
                </span>
            @endif
        </div>
    </div>
</a>
