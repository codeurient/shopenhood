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
            $listing->discount_end_date &&
            now()->lt($listing->discount_end_date)
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

    // Favorite state: load auth user's favorite IDs once per request (static cache)
    $isFavorited = false;
    if (auth()->check()) {
        static $authFavoriteIds = null;
        if ($authFavoriteIds === null) {
            $authFavoriteIds = auth()->user()->favoriteListings()->pluck('listings.id')->all();
        }
        $isFavorited = in_array($listing->id, $authFavoriteIds);
    }

    // Verified badge: seller has an admin-approved business profile
    $isOwnerVerified = $listing->user?->businessProfile?->isApproved() ?? false;

    // Per-listing sold count (static cache to avoid N+1 across card renders)
    static $listingTotals = [];
    if (! array_key_exists($listing->id, $listingTotals)) {
        $listingTotals[$listing->id] = (int) \App\Models\Order::where('listing_id', $listing->id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('quantity');
    }
    $listingTotalSold = $listingTotals[$listing->id];

    // Seller total sold (static cache to avoid N+1 per seller across card renders)
    static $sellerTotals = [];
    $sellerId = $listing->user_id;
    if ($listing->user && ! array_key_exists($sellerId, $sellerTotals)) {
        $sellerTotals[$sellerId] = (int) \App\Models\Order::where('seller_id', $sellerId)
            ->whereNotIn('status', ['cancelled'])
            ->sum('quantity');
    }
    $sellerTotalSold = $sellerTotals[$sellerId] ?? 0;

    // Seller status badge
    $sellerBadge = null;
    if ($sellerTotalSold >= 100000) {
        $sellerBadge = ['label' => 'Expert Seller', 'color' => 'text-yellow-500'];
    } elseif ($sellerTotalSold >= 50000) {
        $sellerBadge = ['label' => 'Top Seller', 'color' => 'text-red-400'];
    } elseif ($sellerTotalSold >= 10000) {
        $sellerBadge = ['label' => 'Rising Seller', 'color' => 'text-blue-400'];
    }

    // Seller logo: business profile logo first, then user avatar, then initials fallback
    $sellerLogo = $listing->user?->businessProfile?->logo
        ? asset('storage/' . $listing->user->businessProfile->logo)
        : ($listing->user?->avatar ? asset('storage/' . $listing->user->avatar) : null);
    $sellerInitials = $listing->user ? strtoupper(substr($listing->user->name, 0, 2)) : 'S';
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
            <!-- Price Negotiable Icon -->
            @if($listing->is_negotiable)
                <button type="button"
                        onclick="event.preventDefault(); event.stopPropagation();"
                        class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all">
                    <i class="fa-solid fa-handshake text-gray-700" style="font-size: 16px;"></i>
                </button>
            @endif

            <!-- Favorite Button -->
            <button type="button"
                    x-data="cardFavoriteBtn({{ $listing->id }}, {{ $isFavorited ? 'true' : 'false' }})"
                    @click.prevent.stop="toggle()"
                    :title="favorited ? 'Remove from favorites' : 'Add to favorites'"
                    class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all">
                <svg class="w-5 h-5 transition-colors"
                     :class="favorited ? 'text-red-500' : 'text-gray-700'"
                     :fill="favorited ? 'currentColor' : 'none'"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>

            @if($listing->listing_mode === 'business' && (!auth()->check() || auth()->id() !== $listing->user_id))
            <!-- Add to Cart Button (business listings only, hidden for listing owner) -->
            <button type="button"
                    x-data="cardCartBtn({{ $listing->id }}, {{ $listing->defaultVariation?->id ?? 'null' }})"
                    @click.prevent.stop="add()"
                    :disabled="adding"
                    :title="added ? 'Added!' : 'Add to cart'"
                    class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full shadow-md transition-all disabled:opacity-60">
                <template x-if="!added">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2 12h10l2-8H6M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                </template>
                <template x-if="added">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
            </button>
            @endif
        </div>

        <!-- Store Badge & Wholesale Icon (Bottom Left) -->
        <div class="absolute bottom-2 left-2 flex items-center gap-2">
            @if($listing->listing_mode === 'business')
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
        <div class="flex items-center gap-1 min-w-0">
            @if($isOwnerVerified)
                <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" title="Verified seller">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"/>
                </svg>
            @endif
            <h3 class="font-semibold text-gray-900 truncate leading-tight" style="font-size: 11px;">
                {{ $listing->title }}
            </h3>
        </div>


        <!-- Sold + Seller row -->
        <div class="flex items-center gap-1.5 mt-1 overflow-hidden" style="font-size: 10px;">
            @if($listing->listing_mode === 'business')
                <span class="text-gray-500 flex-shrink-0">{{ number_format($listingTotalSold) }}+ sold</span>
                <span class="text-gray-300 flex-shrink-0">|</span>
            @endif
            <div class="flex items-center gap-1 min-w-0">
                @if($sellerLogo)
                    <img src="{{ $sellerLogo }}" alt="Seller" class="w-3 h-3 rounded-full object-cover flex-shrink-0">
                @else
                    <div class="w-3 h-3 rounded-full bg-gray-700 flex-shrink-0"></div>
                @endif
                <span class="text-gray-500 flex-shrink-0">Seller</span>
                @if($sellerBadge)
                    <svg class="w-2.5 h-2.5 {{ $sellerBadge['color'] }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="{{ $sellerBadge['color'] }} font-semibold truncate">{{ $sellerBadge['label'] }}</span>
                @endif
            </div>
        </div>


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
            @elseif($listing->condition === 'used')
                <span class="px-1.5 py-0.5 font-medium text-yellow-700 bg-yellow-50 rounded-md ml-1 flex-shrink-0">
                    Second
                </span>
            @endif
        </div>


    </div>
</a>

@once
<script>
function cardFavoriteBtn(listingId, initialFavorited) {
    return {
        favorited: initialFavorited,
        loading: false,
        toggle() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            if (this.loading) { return; }
            this.loading = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch(`/api/favorites/${listingId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            })
            .then(r => r.json())
            .then(data => {
                this.favorited = data.favorited;
                this.loading = false;
            })
            .catch(() => { this.loading = false; });
        },
    };
}

function cardCartBtn(listingId, variationId) {
    return {
        adding: false,
        added: false,
        add() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            if (this.adding) { return; }
            this.adding = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch('/api/cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ listing_id: listingId, variation_id: variationId, quantity: 1 }),
            })
            .then(r => r.json())
            .then(() => {
                this.adding = false;
                this.added = true;
                window.dispatchEvent(new CustomEvent('cart-updated'));
                setTimeout(() => { this.added = false; }, 2500);
            })
            .catch(() => { this.adding = false; });
        },
    };
}
</script>
@endonce
