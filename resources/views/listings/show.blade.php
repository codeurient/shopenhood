<x-guest-layout>
    @php
        $images = $listing->images->count() > 0 ? $listing->images : collect();
        $totalImages = $images->count();
        $currency = $listing->currency ?? 'USD';
        $hasDiscount = $listing->discount_price
            && $listing->discount_start_date
            && $listing->discount_end_date
            && now()->between($listing->discount_start_date, $listing->discount_end_date);
        $displayPrice = $hasDiscount ? $listing->discount_price : $listing->base_price;
        $seller = $listing->user;
    @endphp

    @push('scripts')
    <script>
        window.LISTING_PAGE = {
            allVariations: @json($variationsData->values()),
            listingImages: @json($images->map(fn($img) => ['url' => asset('storage/' . $img->image_path)])->values()),
            currency: @json($currency),
            allVariantIds: @json($variantsData->pluck('id')->values()),
            allVariantItems: @json($variantsData->mapWithKeys(fn($v) => [$v['id'] => collect($v['items'])->pluck('id')->values()->toArray()])->toArray()),
            allVariantItemValues: @json($variantsData->mapWithKeys(fn($v) => [$v['id'] => collect($v['items'])->mapWithKeys(fn($item) => [$item['id'] => $item['value']])->toArray()])->toArray()),
            listingPrice: @json($listing->base_price ? ['base_price' => (float) $listing->base_price, 'current_price' => (float) ($hasDiscount && $listing->discount_price ? $listing->discount_price : $listing->base_price), 'has_discount' => (bool) $hasDiscount] : null),
        };
    </script>
    @endpush

    <div x-data="{
            currentImage: 0,
            totalImages: 0,
            quantity: 1,
            selectedVariants: {},
            selectedVariantItemIds: {},
            titleExpanded: false,
            descExpanded: false,
            charExpanded: false,
            activeTab: 'recommended',
            dragStartX: 0,
            dragOffsetX: 0,
            isDragging: false,
            autoSlideTimer: null,

            allVariations: window.LISTING_PAGE.allVariations,
            listingImages: window.LISTING_PAGE.listingImages,
            currency: window.LISTING_PAGE.currency,
            allVariantIds: window.LISTING_PAGE.allVariantIds,
            allVariantItems: window.LISTING_PAGE.allVariantItems,
            allVariantItemValues: window.LISTING_PAGE.allVariantItemValues,

            displayImages: [],
            displayPrice: null,
            displayStock: null,

            prevImage() {
                this.currentImage = (this.currentImage - 1 + this.totalImages) % this.totalImages;
                this.resetAutoSlide();
            },
            nextImage() {
                this.currentImage = (this.currentImage + 1) % this.totalImages;
                this.resetAutoSlide();
            },
            startDrag(e) {
                this.dragStartX = e.clientX;
                this.dragOffsetX = 0;
                this.isDragging = true;
                e.target.setPointerCapture(e.pointerId);
                clearInterval(this.autoSlideTimer);
            },
            moveDrag(e) {
                if (!this.isDragging) return;
                this.dragOffsetX = e.clientX - this.dragStartX;
            },
            endDrag() {
                if (!this.isDragging) return;
                if (this.dragOffsetX < -50) this.currentImage = (this.currentImage + 1) % this.totalImages;
                else if (this.dragOffsetX > 50) this.currentImage = (this.currentImage - 1 + this.totalImages) % this.totalImages;
                this.isDragging = false;
                this.dragOffsetX = 0;
                this.resetAutoSlide();
            },
            resetAutoSlide() {
                clearInterval(this.autoSlideTimer);
                if (this.totalImages <= 1) return;
                this.autoSlideTimer = setInterval(() => {
                    this.currentImage = (this.currentImage + 1) % this.totalImages;
                }, 5000);
            },
            selectVariant(variantId, itemId) {
                this.selectedVariantItemIds[variantId] = itemId;
                this.selectedVariants[variantId] = (this.allVariantItemValues[variantId] || {})[itemId] ?? '';
                this.resolveConflicts(variantId);
                this.updateDisplay();
            },
            findMatchingVariation() {
                const ids = this.selectedVariantItemIds;
                if (!Object.keys(ids).length || !this.allVariations.length) return null;
                const matches = this.allVariations.filter(v =>
                    v.attributes.length > 0 &&
                    v.attributes.every(a => ids[a.variant_id] === a.variant_item_id)
                );
                if (!matches.length) return null;
                return matches.reduce((best, v) => v.attributes.length > best.attributes.length ? v : best);
            },
            updateDisplay() {
                const variation = this.findMatchingVariation();
                this.displayImages = (variation && variation.images && variation.images.length)
                    ? variation.images
                    : this.listingImages;
                this.totalImages = this.displayImages.length || 1;
                this.currentImage = 0;
                this.resetAutoSlide();
                const priceSrc = variation || this.allVariations.find(v => v.is_default) || null;
                this.displayPrice = priceSrc ? {
                    base_price: priceSrc.price,
                    current_price: priceSrc.current_price,
                    has_discount: priceSrc.has_discount,
                } : window.LISTING_PAGE.listingPrice;
                const stockSrc = variation ?? priceSrc;
                this.displayStock = (stockSrc && stockSrc.stock_quantity !== null && stockSrc.stock_quantity !== undefined) ? {
                    qty: stockSrc.stock_quantity,
                    is_low_stock: stockSrc.is_low_stock,
                } : null;
                if (this.displayStock && this.displayStock.qty > 0 && this.quantity > this.displayStock.qty) {
                    this.quantity = this.displayStock.qty;
                }
            },
            formatPrice(amount) {
                return parseFloat(amount).toFixed(2);
            },
            maxQty() { return (this.displayStock && this.displayStock.qty > 0) ? this.displayStock.qty : Infinity; },
            increaseQty() { if (this.quantity < this.maxQty()) this.quantity++; },
            decreaseQty() { if (this.quantity > 1) this.quantity--; },
            isVariantItemAvailable(variantId, itemId) {
                // Only apply constraints from dimensions that appear BEFORE this one.
                // This prevents deadlocks: e.g. Color (position 0) is never blocked by
                // Storage (position 1), but Storage IS filtered by the selected Color.
                const myPosition = this.allVariantIds.indexOf(variantId);
                const constraints = (myPosition > 0 ? this.allVariantIds.slice(0, myPosition) : [])
                    .flatMap(vid => {
                        const iid = this.selectedVariantItemIds[vid];
                        return iid !== undefined ? [{ vid, iid }] : [];
                    });
                return this.allVariations.some(v => {
                    const hasItem = v.attributes.some(a => a.variant_id == variantId && a.variant_item_id == itemId);
                    if (!hasItem) return false;
                    return constraints.every(c =>
                        v.attributes.some(a => a.variant_id == c.vid && a.variant_item_id == c.iid)
                    );
                });
            },
            resolveConflicts(changedVariantId) {
                // After changing a dimension, auto-correct any downstream dimensions
                // whose current selection is no longer valid.
                const changedPosition = this.allVariantIds.indexOf(changedVariantId);
                for (let i = changedPosition + 1; i < this.allVariantIds.length; i++) {
                    const vid = this.allVariantIds[i];
                    const currentIid = this.selectedVariantItemIds[vid];
                    if (currentIid !== undefined && !this.isVariantItemAvailable(vid, currentIid)) {
                        const items = this.allVariantItems[vid] || [];
                        const firstValid = items.find(iid => this.isVariantItemAvailable(vid, iid));
                        if (firstValid !== undefined) {
                            this.selectedVariantItemIds[vid] = firstValid;
                            this.selectedVariants[vid] = (this.allVariantItemValues[vid] || {})[firstValid] ?? '';
                        } else {
                            delete this.selectedVariantItemIds[vid];
                            delete this.selectedVariants[vid];
                        }
                    }
                }
            },
            init() {
                const defaultVar = this.allVariations.find(v => v.is_default) || null;
                if (defaultVar) {
                    defaultVar.attributes.forEach(a => {
                        this.selectedVariants[a.variant_id] = a.item_value;
                        this.selectedVariantItemIds[a.variant_id] = a.variant_item_id;
                    });
                }
                this.updateDisplay();
            }
         }"
         class="bg-gray-50 pb-20">

        {{-- ================================================================ --}}
        {{-- IMAGE GALLERY                                                     --}}
        {{-- ================================================================ --}}
        <div class="relative bg-white overflow-hidden select-none" style="height: 340px; touch-action: none;">

            {{-- Draggable image strip --}}
            <div x-show="displayImages.length > 0"
                 class="flex h-full"
                 :style="{ transform: `translateX(calc(-${currentImage} * 100% + ${dragOffsetX}px))`, transition: isDragging ? 'none' : 'transform 0.3s ease' }"
                 @pointerdown="startDrag($event)"
                 @pointermove="moveDrag($event)"
                 @pointerup="endDrag()"
                 @pointercancel="isDragging = false; dragOffsetX = 0; resetAutoSlide()">
                <template x-for="(img, idx) in displayImages" :key="idx">
                    <div class="w-full h-full flex-shrink-0">
                        <img :src="img.url"
                             alt="{{ $listing->title }}"
                             class="w-full h-full object-cover pointer-events-none"
                             draggable="false">
                    </div>
                </template>
            </div>
            <div x-show="displayImages.length === 0"
                 class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>

            {{-- Back button --}}
            <a href="{{ url()->previous() }}"
               class="absolute top-4 left-4 z-20 flex items-center justify-center w-9 h-9 bg-black/30 rounded-full backdrop-blur-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            {{-- Top-right actions --}}
            <div class="absolute top-4 right-4 z-20 flex flex-col gap-2">
                <button class="flex items-center justify-center w-9 h-9 bg-black/30 rounded-full backdrop-blur-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>
                <button class="flex items-center justify-center w-9 h-9 bg-black/30 rounded-full backdrop-blur-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Image counter --}}
            <div x-show="totalImages > 1"
                 class="absolute bottom-3 right-3 z-10 bg-black/50 rounded-full px-2.5 py-0.5 pointer-events-none">
                <span class="text-white text-xs" x-text="(currentImage + 1) + '/' + totalImages"></span>
            </div>

            {{-- Dots --}}
            <div x-show="totalImages > 1"
                 class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10 flex gap-1.5">
                <template x-for="(img, idx) in displayImages" :key="idx">
                    <button @click.stop="currentImage = idx; resetAutoSlide()"
                            :class="currentImage === idx ? 'bg-white w-4' : 'bg-white/50 w-1.5'"
                            class="h-1.5 rounded-full transition-all duration-300"></button>
                </template>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- PRODUCT INFO                                                      --}}
        {{-- ================================================================ --}}
        <div class="bg-white mt-2 px-4 py-3">

            {{-- Title row --}}
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-gray-900 leading-snug"
                        :class="titleExpanded ? '' : 'line-clamp-2'">
                        {{ $listing->title }}
                    </h1>
                </div>
                <button @click="titleExpanded = !titleExpanded" class="flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-gray-500 transition-transform"
                         :class="titleExpanded ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Sold + Seller row --}}
            <div class="flex items-center gap-2 mt-1.5">
                <span class="text-xs text-gray-500">{{ number_format($listing->views_count ?? 0) }}+ sold</span>
                <span class="text-gray-300 text-xs">|</span>
                <div class="flex items-center gap-1">
                    <span class="text-xs text-gray-500">Seller</span>
                    <div class="w-3 h-3 rounded-full bg-gray-700"></div>
                </div>
            </div>

            {{-- Rating --}}
            <div class="flex items-center gap-1.5 mt-1.5">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= 4 ? 'text-orange-400' : 'text-orange-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <span class="text-xs font-semibold text-gray-700">4.7</span>
            </div>

            {{-- Prices --}}
            <div class="flex items-baseline gap-2 mt-2">
                <template x-if="displayPrice && displayPrice.has_discount">
                    <div class="flex items-baseline gap-2">
                        <span class="text-gray-400 text-sm line-through"
                              x-text="formatPrice(displayPrice.base_price) + ' ' + currency"></span>
                        <span class="text-xl font-bold text-gray-900"
                              x-text="formatPrice(displayPrice.current_price) + ' ' + currency"></span>
                    </div>
                </template>
                <template x-if="displayPrice && !displayPrice.has_discount">
                    <span class="text-xl font-bold text-gray-900"
                          x-text="formatPrice(displayPrice.current_price) + ' ' + currency"></span>
                </template>
                <template x-if="!displayPrice">
                    <span class="text-base font-medium text-gray-600">Contact for price</span>
                </template>
            </div>

            {{-- Stock badge --}}
            <div x-show="displayStock && displayStock.qty !== null" class="mt-2">
                <template x-if="displayStock && displayStock.qty > 10">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="displayStock.qty + ' in stock'"></span>
                    </span>
                </template>
                <template x-if="displayStock && displayStock.qty <= 10 && displayStock.qty > 0">
                    <span class="inline-block bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-sm uppercase tracking-wide"
                          x-text="'Only ' + displayStock.qty + ' left'"></span>
                </template>
                <template x-if="displayStock && displayStock.qty === 0">
                    <span class="inline-block bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-sm uppercase tracking-wide">
                        Out of stock
                    </span>
                </template>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- VARIANTS                                                          --}}
        {{-- ================================================================ --}}
        @if(count($variantsData) > 0)
        <div class="bg-white mt-2 px-4 py-3 space-y-4">
            @foreach($variantsData as $variant)
            <div>
                {{-- Label row: "Color: Silver" --}}
                <div class="flex items-center gap-1.5 mb-2.5">
                    <span class="text-sm text-gray-500">{{ $variant['name'] }}:</span>
                    <span class="text-sm font-semibold text-gray-900"
                          x-text="selectedVariants[{{ $variant['id'] }}] ?? ''"></span>
                </div>

                @if($variant['display_type'] === 'color' || $variant['display_type'] === 'image')
                {{-- Color / Image swatches — round circles --}}
                <div class="flex items-center gap-2.5 flex-wrap">
                    @foreach($variant['items'] as $item)
                    <button type="button"
                            @click="isVariantItemAvailable({{ $variant['id'] }}, {{ $item['id'] }}) && selectVariant({{ $variant['id'] }}, {{ $item['id'] }})"
                            :class="{
                                'ring-2 ring-green-500 ring-offset-2': selectedVariantItemIds[{{ $variant['id'] }}] === {{ $item['id'] }},
                                'ring-1 ring-gray-300': selectedVariantItemIds[{{ $variant['id'] }}] !== {{ $item['id'] }},
                                'opacity-40 cursor-not-allowed': !isVariantItemAvailable({{ $variant['id'] }}, {{ $item['id'] }})
                            }"
                            class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 transition-all duration-150"
                            title="{{ $item['value'] }}">
                        @if($item['image_path'])
                            <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['value'] }}" class="w-full h-full object-cover">
                        @elseif($item['color_code'])
                            <div class="w-full h-full" style="background-color: {{ $item['color_code'] }};"></div>
                        @else
                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                <span class="text-[10px] font-semibold text-gray-600 uppercase">{{ substr($item['value'], 0, 2) }}</span>
                            </div>
                        @endif
                    </button>
                    @endforeach
                </div>

                @else
                {{-- Button pills (button, text, select, etc.) --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach($variant['items'] as $item)
                    <button type="button"
                            @click="isVariantItemAvailable({{ $variant['id'] }}, {{ $item['id'] }}) && selectVariant({{ $variant['id'] }}, {{ $item['id'] }})"
                            :class="{
                                'border-green-500 text-green-700 bg-green-50 font-semibold': selectedVariantItemIds[{{ $variant['id'] }}] === {{ $item['id'] }},
                                'border-gray-300 text-gray-700 bg-white': selectedVariantItemIds[{{ $variant['id'] }}] !== {{ $item['id'] }},
                                'opacity-40 cursor-not-allowed line-through': !isVariantItemAvailable({{ $variant['id'] }}, {{ $item['id'] }})
                            }"
                            class="px-3 py-1.5 rounded-lg border text-sm transition-colors">
                        {{ $item['value'] }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- QUANTITY                                                          --}}
        {{-- ================================================================ --}}
        <div class="bg-white mt-2 px-4 py-3 flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">Qty</span>
            <div class="flex items-center gap-4">
                <button @click="decreaseQty()"
                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:border-gray-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <span x-text="quantity" class="text-base font-semibold text-gray-900 w-6 text-center"></span>
                <button @click="increaseQty()"
                        :disabled="quantity >= maxQty()"
                        :class="quantity >= maxQty() ? 'opacity-40 cursor-not-allowed border-gray-200' : 'hover:border-gray-400'"
                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- DESCRIPTION & CHARACTERISTICS                                     --}}
        {{-- ================================================================ --}}
        <div class="bg-white mt-2 px-4 py-4">

            {{-- Description --}}
            <h2 class="text-base font-bold text-gray-900">Description</h2>

            @if($listing->short_description)
            <p class="text-sm font-semibold text-gray-800 mt-2">{{ $listing->short_description }}</p>
            @endif

            @if($listing->description)
            <div class="mt-1.5">
                <p class="text-sm text-gray-600 leading-relaxed"
                   :class="descExpanded ? '' : 'line-clamp-4'">
                    {{ $listing->description }}
                </p>
                <button @click="descExpanded = !descExpanded"
                        class="text-sm font-medium text-green-600 mt-1"
                        x-text="descExpanded ? 'Show less' : 'Read more ...'">
                </button>
            </div>
            @endif

            {{-- Divider --}}
            <div class="border-t border-gray-100 my-4"></div>

            {{-- Characteristics --}}
            <h2 class="text-base font-bold text-gray-900">Characteristics</h2>

            <div class="mt-3 space-y-0">

                {{-- Category --}}
                @if($listing->category)
                <div class="py-2.5 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Category</span>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Category</span>
                        <span class="text-sm text-green-600 font-medium">{{ $listing->category->name }}</span>
                    </div>
                </div>
                @endif

                {{-- Condition --}}
                @if($listing->condition)
                <div class="py-2.5 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Condition</span>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Condition</span>
                        <span class="text-sm text-green-600 font-medium">{{ ucfirst(str_replace('_', ' ', $listing->condition)) }}</span>
                    </div>
                </div>
                @endif

                {{-- Listing Type --}}
                @if($listing->listingType)
                <div class="py-2.5 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Listing Type</span>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Type</span>
                        <span class="text-sm text-green-600 font-medium">{{ $listing->listingType->name }}</span>
                    </div>
                </div>
                @endif

                {{-- Location --}}
                @if($listing->country || $listing->city)
                <div class="py-2.5 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Location</span>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Location</span>
                        <span class="text-sm text-green-600 font-medium">
                            {{ implode(', ', array_filter([$listing->city, $listing->country])) }}
                        </span>
                    </div>
                </div>
                @endif

                {{-- Store name --}}
                @if($listing->store_name)
                <div class="py-2.5 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Store</span>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Store name</span>
                        <span class="text-sm text-green-600 font-medium">{{ $listing->store_name }}</span>
                    </div>
                </div>
                @endif

                {{-- More characteristics collapsed --}}
                <div x-show="charExpanded" style="display: none;">
                    @if($listing->is_negotiable)
                    <div class="py-2.5 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Price negotiable</span>
                            <span class="text-sm text-green-600 font-medium">Yes</span>
                        </div>
                    </div>
                    @endif
                    @if($listing->has_delivery)
                    <div class="py-2.5 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Delivery available</span>
                            <span class="text-sm text-green-600 font-medium">Yes</span>
                        </div>
                    </div>
                    @endif
                </div>

                <button @click="charExpanded = !charExpanded"
                        class="text-sm font-medium text-green-600 mt-3"
                        x-text="charExpanded ? 'Show less' : 'Read more ...'">
                </button>
            </div>

            {{-- Disclaimer --}}
            <div class="mt-4 space-y-1.5">
                <p class="text-xs text-gray-400">* Product details and specifications may vary. Please verify before purchase.</p>
                <p class="text-xs text-gray-400">* Product specifications may change without notice by the seller.</p>
                <p class="text-xs text-gray-400">* Description is based on available product information.</p>
            </div>
        </div>

        {{-- ================================================================ --}}
        {{-- DELIVERY                                                          --}}
        {{-- ================================================================ --}}
        <div class="bg-white mt-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3.5 border-b border-gray-100">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">
                        @if($listing->has_delivery && ($listing->domestic_delivery_price == 0 || !$listing->domestic_delivery_price))
                            Free delivery
                        @elseif($listing->has_delivery && $listing->domestic_delivery_price)
                            Delivery: {{ $currency }} {{ number_format($listing->domestic_delivery_price, 2) }}
                        @else
                            Delivery not available
                        @endif
                    </p>
                    <div class="flex gap-4 mt-0.5">
                        <p class="text-xs text-gray-500">
                            @if($listing->has_delivery)
                                Standard:
                                @if(!$listing->domestic_delivery_price || $listing->domestic_delivery_price == 0)
                                    FREE.
                                @else
                                    {{ $currency }} {{ number_format($listing->domestic_delivery_price, 2) }}.
                                @endif
                                Arrives in 7+ days.
                            @else
                                Pickup only
                            @endif
                        </p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- ================================================================ --}}
        {{-- RATINGS & REVIEWS                                                 --}}
        {{-- ================================================================ --}}
        @php
            $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : 0;
            $reviewCount = $reviews->count();
        @endphp
        <div class="bg-white mt-2 px-4 py-4" id="reviews">

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="mb-3 px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                {{ session('error') }}
            </div>
            @endif

            {{-- Overall rating --}}
            <div class="flex items-center gap-2 mb-4">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $avgRating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                @if($reviewCount > 0)
                <span class="text-sm font-bold text-gray-900">{{ $avgRating }}</span>
                <span class="text-sm text-gray-500">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                @else
                <span class="text-sm text-gray-500">No reviews yet</span>
                @endif
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 my-4"></div>

            {{-- Write a review form --}}
            @if($canReview)
            <div class="mb-5" x-data="{ rating: 0, hover: 0 }">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Write a Review</h3>
                <form action="{{ route('listings.reviews.store', $listing) }}" method="POST">
                    @csrf

                    {{-- Star picker --}}
                    <div class="flex items-center gap-1 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                                @click="rating = {{ $i }}"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                class="focus:outline-none">
                            <svg class="w-8 h-8 transition-colors"
                                 :class="(hover || rating) >= {{ $i }} ? 'text-orange-400' : 'text-gray-300'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                        @endfor
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    @error('rating')
                    <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                    @enderror

                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           placeholder="Review title (optional)"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2 focus:outline-none focus:border-orange-400">
                    @error('title')
                    <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                    @enderror

                    <textarea name="body"
                              rows="3"
                              placeholder="Share your experience with this product..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm resize-none focus:outline-none focus:border-orange-400">{{ old('body') }}</textarea>
                    @error('body')
                    <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="mt-2 w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        Submit Review
                    </button>
                </form>
                <div class="border-t border-gray-100 my-4"></div>
            </div>
            @elseif($alreadyReviewed)
            <p class="text-sm text-gray-500 mb-4 text-center">You have already reviewed this product.</p>
            @elseif(!auth()->check())
            <a href="{{ route('login') }}" class="block text-center text-sm text-orange-600 font-medium mb-4">
                Login to write a review
            </a>
            @endif

            {{-- Review list --}}
            @if($reviews->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">Be the first to leave a review!</p>
            @else
            <div class="space-y-5">
                @foreach($reviews as $review)
                @php
                    $initials = strtoupper(substr($review->user->name, 0, 2));
                    $colors = ['bg-orange-400', 'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500'];
                    $color = $colors[$review->user_id % count($colors)];
                @endphp
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full {{ $color }} flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-white">{{ $initials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900 truncate">{{ $review->user->name }}</span>
                            @if($review->is_verified_purchase)
                            <span class="text-xs text-green-600 font-medium flex-shrink-0">✓ Verified</span>
                            @endif
                            <span class="text-xs text-gray-400 ml-auto flex-shrink-0">{{ $review->created_at->format('d M. Y') }}</span>
                        </div>
                        <div class="flex items-center gap-0.5 mt-0.5">
                            @for($s = 1; $s <= 5; $s++)
                            <svg class="w-3 h-3 {{ $s <= $review->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        @if($review->title)
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $review->title }}</p>
                        @endif
                        @if($review->body)
                        <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $review->body }}</p>
                        @endif
                        @if(auth()->id() === $review->user_id)
                        <form action="{{ route('listings.reviews.destroy', $review) }}" method="POST" class="mt-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete review</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ================================================================ --}}
        {{-- SELLER INFO                                                       --}}
        {{-- ================================================================ --}}
        @if($seller)
        <div class="bg-white mt-2 px-4 py-4">
            <div class="flex items-center gap-3">
                {{-- Avatar --}}
                <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                    @if($seller->profile_photo_path)
                        <img src="{{ asset('storage/' . $seller->profile_photo_path) }}" alt="{{ $seller->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-700">
                            <span class="text-sm font-bold text-white">{{ strtoupper(substr($seller->name, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Seller details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900 truncate">{{ $seller->name }}</span>
                        @if(method_exists($seller, 'isBusinessUser') && $seller->isBusinessUser())
                        <span class="flex items-center gap-0.5 text-xs text-orange-500 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Star Seller
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if($listing->store_name){{ $listing->store_name }} · @endif
                        4.7 ★
                    </p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-xs text-gray-500">Member since {{ $seller->created_at->format('Y') }}</span>
                    </div>
                </div>

                <a href="#" class="text-xs text-primary-600 font-medium border border-primary-300 px-3 py-1.5 rounded-lg">
                    Visit
                </a>
            </div>
        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- RECOMMENDED LISTINGS                                              --}}
        {{-- ================================================================ --}}
        @if($relatedListings->count() > 0)
        <div class="bg-white mt-2">

            {{-- Tab bar --}}
            <div class="flex border-b border-gray-100 overflow-x-auto scrollbar-hide">
                <button @click="activeTab = 'recommended'"
                        :class="activeTab === 'recommended' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-500'"
                        class="flex-shrink-0 px-4 py-3 text-sm font-medium whitespace-nowrap">
                    Recommended
                </button>
                <button @click="activeTab = 'category'"
                        :class="activeTab === 'category' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-500'"
                        class="flex-shrink-0 px-4 py-3 text-sm font-medium whitespace-nowrap">
                    {{ $listing->category->name ?? 'Category' }}
                </button>
            </div>

            {{-- Product grid --}}
            <div class="grid grid-cols-2 gap-2 p-2">
                @foreach($relatedListings as $related)
                <x-mobile.listing-card :listing="$related" />
                @endforeach
            </div>
        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- STICKY BOTTOM ACTION BAR                                          --}}
        {{-- ================================================================ --}}
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 flex items-center gap-3 z-30">
            <button class="flex-shrink-0 flex items-center justify-center w-11 h-11 border border-gray-300 rounded-xl">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </button>
            <button class="flex-1 h-11 bg-orange-400 hover:bg-orange-500 text-white font-semibold rounded-xl text-sm transition-colors">
                Add to Cart
            </button>
            <button class="flex-1 h-11 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl text-sm transition-colors">
                Buy Now
            </button>
        </div>
    </div>
</x-guest-layout>
