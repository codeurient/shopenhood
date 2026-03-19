<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('user.listings.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition mb-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to My Listings
            </a>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Edit Listing</h1>
        </div>
    </div>

    {{-- ── Status Banner ────────────────────────────────────────────────── --}}
    @php
        $bannerClass = match($listing->status) {
            'active'   => 'bg-green-50 border-green-200 text-green-700',
            'pending'  => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'rejected' => 'bg-red-50 border-red-200 text-red-700',
            default    => 'bg-[#F5F5F5] border-[#E0E0E0] text-[#37474F]',
        };
        $bannerIcon = match($listing->status) {
            'active'   => 'fa-circle-check text-green-500',
            'pending'  => 'fa-clock text-yellow-500',
            'rejected' => 'fa-circle-xmark text-red-500',
            default    => 'fa-circle text-[#37474F]',
        };
    @endphp
    <div class="mb-5 flex items-start gap-3 px-4 py-3 border rounded text-[13px] {{ $bannerClass }}">
        <i class="fa-solid {{ $bannerIcon }} flex-shrink-0 mt-0.5"></i>
        <div>
            <strong>Status:</strong> {{ ucfirst($listing->status) }}
            @if($listing->status === 'rejected' && $listing->rejection_reason)
                <br><strong>Reason:</strong> {{ $listing->rejection_reason }}
            @endif
            @if($listing->status !== 'rejected')
                <span class="opacity-75"> — Updating will resubmit for review.</span>
            @endif
        </div>
    </div>

    {{-- ── Validation Errors ────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" id="listingForm"
          x-data="listingForm({{ Illuminate\Support\Js::from($listingTypes->mapWithKeys(fn($t) => [$t->id => $t->slug])) }}, '{{ old('listing_type_id', $listing->listing_type_id) }}')">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Left Column ─────────────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Basic Information --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-circle-info text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Basic Information</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        {{-- Listing Type --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Listing Type <span class="text-red-500">*</span>
                            </label>
                            <select name="listing_type_id" id="listing_type_id" required
                                    x-model="selectedTypeId"
                                    class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                @foreach($listingTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('listing_type_id', $listing->listing_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('listing_type_id')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div id="categorySelectsContainer" class="space-y-2">
                                <select id="category_level_0" required
                                        class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition category-select"
                                        data-level="0">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <input type="hidden" name="category_id" id="category_id_hidden" value="{{ old('category_id', $listing->category_id) }}" required>
                            @error('category_id')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" required maxlength="255"
                                   value="{{ old('title', $listing->title) }}"
                                   placeholder="What are you listing?"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                            @error('title')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Short Description</label>
                            <textarea name="short_description" rows="2" maxlength="500"
                                      placeholder="Brief summary for preview (optional)"
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('short_description', $listing->short_description) }}</textarea>
                            <p class="mt-1 text-[11px] text-[#37474F]">Short summary shown in listings (max 500 characters)</p>
                        </div>

                        {{-- Full Description --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('description', $listing->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Type-Specific Details ───────────────────────────── --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden" x-show="listingType !== ''">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-tag text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]"
                            x-text="{'sell':'Pricing','buy':'Budget','gift':'Item Details','barter':'Item Details','auction':'Auction Pricing'}[listingType] || 'Details'"></h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        {{-- SELL + AUCTION: Base Price --}}
                        <div x-show="['sell','auction'].includes(listingType)">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        <span x-text="listingType === 'auction' ? 'Starting Bid' : 'Price'"></span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="base_price" step="0.01" min="0.01"
                                           :required="['sell','auction'].includes(listingType)"
                                           value="{{ old('base_price', $listing->base_price) }}"
                                           placeholder="0.00"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('base_price')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Currency</label>
                                    <select name="currency"
                                            class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        <option value="USD" {{ old('currency', $listing->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency', $listing->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency', $listing->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SELL: Negotiable + Discount --}}
                        <div x-show="listingType === 'sell'" class="space-y-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_negotiable" value="1"
                                       {{ old('is_negotiable', $listing->is_negotiable) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] text-[#37474F]">Price is negotiable</span>
                            </label>

                            <div class="border-t border-[#E0E0E0] pt-4">
                                <label class="flex items-center gap-2 cursor-pointer mb-3">
                                    <input type="checkbox" id="has_discount" value="1"
                                           {{ old('discount_price', $listing->discount_price) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded accent-[#D4AF37]">
                                    <span class="text-[13px] font-semibold text-[#1A1A1A]">Apply Discount</span>
                                </label>
                                <div id="discountFields" class="{{ old('discount_price', $listing->discount_price) ? '' : 'hidden' }} space-y-4">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Discount Price</label>
                                            <input type="number" name="discount_price" id="discount_price" step="0.01" min="0"
                                                   value="{{ old('discount_price', $listing->discount_price) }}"
                                                   placeholder="0.00"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Start Date</label>
                                            <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                                   value="{{ old('discount_start_date', $listing->discount_start_date?->format('Y-m-d\TH:i')) }}"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">End Date</label>
                                            <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                                   value="{{ old('discount_end_date', $listing->discount_end_date?->format('Y-m-d\TH:i')) }}"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BUY: Max Budget --}}
                        <div x-show="listingType === 'buy'">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Max Budget (optional)</label>
                                    <input type="number" name="budget_max" step="0.01" min="0"
                                           value="{{ old('budget_max', $listing->budget_max) }}"
                                           placeholder="Leave blank if flexible"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('budget_max')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Currency</label>
                                    <select name="currency"
                                            class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        <option value="USD" {{ old('currency', $listing->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency', $listing->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency', $listing->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- BARTER --}}
                        <div x-show="listingType === 'barter'">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                What do you want in exchange? <span class="text-red-500">*</span>
                            </label>
                            <textarea name="barter_exchange_for" rows="3" maxlength="1000"
                                      :required="listingType === 'barter'"
                                      placeholder="Describe the item(s) or type of exchange you're looking for..."
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('barter_exchange_for', $listing->barter_exchange_for) }}</textarea>
                            @error('barter_exchange_for')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- AUCTION --}}
                        <div x-show="listingType === 'auction'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Auction End Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="auction_end_date"
                                           :required="listingType === 'auction'"
                                           value="{{ old('auction_end_date', $listing->auction_end_date?->format('Y-m-d\TH:i')) }}"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('auction_end_date')
                                        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Reserve Price (optional)</label>
                                    <input type="number" name="auction_reserve_price" step="0.01" min="0"
                                           value="{{ old('auction_reserve_price', $listing->auction_reserve_price) }}"
                                           placeholder="Minimum price to sell"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Min Bid Increment (optional)</label>
                                    <input type="number" name="auction_bid_increment" step="0.01" min="0.01"
                                           value="{{ old('auction_bid_increment', $listing->auction_bid_increment) }}"
                                           placeholder="e.g. 1.00"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                </div>
                            </div>
                        </div>

                        {{-- GIFT --}}
                        <div x-show="listingType === 'gift'">
                            <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded text-[13px] text-green-700">
                                <i class="fa-solid fa-gift flex-shrink-0 mt-0.5"></i>
                                <span>This item is listed as <strong>free</strong>. Specify any pickup or delivery arrangements below.</span>
                            </div>
                        </div>

                        {{-- Condition --}}
                        <div class="border-t border-[#E0E0E0] pt-4" x-show="listingType !== ''">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">
                                <span x-text="listingType === 'buy' ? 'Preferred Condition' : 'Item Condition'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div x-data="{ condition: '{{ old('condition', $listing->condition ?? 'new') }}' }" class="space-y-3">
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="condition" value="new"
                                               x-model="condition"
                                               {{ old('condition', $listing->condition ?? 'new') === 'new' ? 'checked' : '' }}
                                               class="w-4 h-4 accent-[#D4AF37]">
                                        <span class="text-[13px] text-[#37474F]">New</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="condition" value="used"
                                               x-model="condition"
                                               {{ old('condition', $listing->condition) === 'used' ? 'checked' : '' }}
                                               class="w-4 h-4 accent-[#D4AF37]">
                                        <span class="text-[13px] text-[#37474F]">Second-hand</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="condition" value="refurbished"
                                               x-model="condition"
                                               {{ old('condition', $listing->condition) === 'refurbished' ? 'checked' : '' }}
                                               class="w-4 h-4 accent-[#D4AF37]">
                                        <span class="text-[13px] text-[#37474F]">Refurbished</span>
                                    </label>
                                </div>
                                <div x-show="condition === 'refurbished'" x-cloak>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">
                                        Refurbished Details <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="refurbished_description" rows="3"
                                              placeholder="Describe which parts were modified or replaced and what changes were made..."
                                              class="w-full border border-[#E0E0E0] rounded focus:border-[#D4AF37] focus:ring-0 text-[13px] text-[#1A1A1A] placeholder-[#37474F] px-3 py-2 resize-none">{{ old('refurbished_description', $listing->refurbished_description) }}</textarea>
                                    @error('refurbished_description')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Delivery --}}
                        <div class="border-t border-[#E0E0E0] pt-4" x-show="listingType !== '' && listingType !== 'buy'">
                            <label class="flex items-center gap-2 cursor-pointer mb-4">
                                <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                       {{ old('has_delivery', $listing->has_delivery) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] font-semibold text-[#1A1A1A]">Delivery Available</span>
                            </label>
                            <div id="deliveryFields" class="{{ old('has_delivery', $listing->has_delivery) ? '' : 'hidden' }} space-y-3">
                                @foreach([
                                    ['has_same_city_delivery',     'same_city_delivery_price',     'same_city_delivery_days',     'Same City',                   $listing->has_same_city_delivery,     $listing->same_city_delivery_price,     $listing->same_city_delivery_days,     '1'],
                                    ['has_domestic_delivery',      'domestic_delivery_price',      'domestic_delivery_days',      'Same Country (Different City)',$listing->has_domestic_delivery,      $listing->domestic_delivery_price,      $listing->domestic_delivery_days,      '3'],
                                    ['has_international_delivery', 'international_delivery_price', 'international_delivery_days', 'International',               $listing->has_international_delivery, $listing->international_delivery_price, $listing->international_delivery_days, '14'],
                                ] as [$checkName, $priceName, $daysName, $label, $checkVal, $priceVal, $daysVal, $daysPlaceholder])
                                <div class="border border-[#E0E0E0] rounded p-3">
                                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                                        <input type="checkbox" name="{{ $checkName }}" value="1"
                                               {{ old($checkName, $checkVal) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded accent-[#D4AF37]">
                                        <span class="text-[13px] font-semibold text-[#1A1A1A]">{{ $label }}</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] text-[#37474F] mb-1">Price</label>
                                            <input type="number" name="{{ $priceName }}" step="0.01" min="0"
                                                   value="{{ old($priceName, $priceVal) }}" placeholder="0.00"
                                                   class="w-full h-[32px] px-3 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-[#37474F] mb-1">Days</label>
                                            <input type="number" name="{{ $daysName }}" min="1" max="365"
                                                   value="{{ old($daysName, $daysVal) }}" placeholder="{{ $daysPlaceholder }}"
                                                   class="w-full h-[32px] px-3 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Location --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-location-dot text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Location</h2>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Country</label>
                                <select id="country_select"
                                        class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    <option value="">Select Country</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">City</label>
                                <select name="location_id" id="city_select" disabled
                                        class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition disabled:opacity-50">
                                    <option value="">Select country first</option>
                                </select>
                                @error('location_id')
                                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right Column ─────────────────────────────────────────── --}}
            <div class="space-y-5">

                {{-- Variants container (injected by JS) --}}
                <div id="variantsContainer" class="space-y-4"></div>

                {{-- Hidden deletion inputs --}}
                <div id="deleteImagesInputs"></div>

                {{-- Current Images --}}
                @php $allImages = $listing->images; @endphp
                @if($allImages->count() > 0)
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-images text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Current Images</h2>
                        <span class="ml-auto text-[11px] text-[#37474F]">{{ $allImages->count() }} image(s)</span>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($allImages->sortBy('sort_order') as $image)
                            <div class="relative group" id="img-{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image"
                                     class="w-full h-24 object-cover rounded border border-[#E0E0E0]">
                                @if($image->is_primary)
                                    <span class="absolute bottom-1 left-1 text-[10px] bg-[#D4AF37] text-[#000000] font-semibold px-1.5 py-0.5 rounded">Main</span>
                                @endif
                                <button type="button"
                                        onclick="markImageForDeletion({{ $image->id }}, 'img-{{ $image->id }}')"
                                        class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition shadow"
                                        title="Remove image">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Add Product Images --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-cloud-arrow-up text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Add Product Images</h2>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-[11px] text-[#37474F] mb-3">First new image will be the main if no primary exists. Max 10 total.</p>

                        <label for="productImages"
                               class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-[#E0E0E0] rounded cursor-pointer hover:border-[#D4AF37]/60 transition bg-[#FAFAFA]">
                            <i class="fa-solid fa-cloud-arrow-up text-[#37474F] text-2xl mb-1"></i>
                            <span class="text-[12px] text-[#37474F]">Click to upload</span>
                            <span class="text-[11px] text-[#37474F]/60">JPEG, PNG, WEBP</span>
                            <input type="file" name="product_images[]" id="productImages" multiple
                                   accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                        </label>

                        <div id="productImagesPreview" class="mt-3 grid grid-cols-2 gap-2"></div>

                        <div id="productImagesControls" class="mt-2 hidden">
                            <button type="button" id="clearAllProductImages"
                                    class="text-[11px] text-red-600 hover:text-red-800 hover:underline transition">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Footer Actions ───────────────────────────────────────────── --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('user.listings.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Update Listing
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
    function listingForm(types, initialTypeId) {
        return {
            types: types,
            selectedTypeId: initialTypeId ? String(initialTypeId) : '',
            get listingType() {
                return this.selectedTypeId ? (this.types[this.selectedTypeId] || '') : '';
            },
        };
    }

    window.EDIT_VARIANT_VALUES = @json($listing->listingVariants->mapWithKeys(function($lv) {
        return [$lv->variant_id => [
            'variant_item_id' => $lv->variant_item_id,
            'custom_value' => $lv->custom_value,
        ]];
    })->toArray());

    const categoryContainer = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const variantsContainer = document.getElementById('variantsContainer');
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');

    let categoryLevelsData = {};
    let loadedVariantsByCategory = new Set();

    const preselectedLocationId = {{ $listing->location_id ?? 'null' }};
    const preselectedCountryId = {{ $listing->location?->parent_id ?? 'null' }};

    fetch('/api/locations/countries')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                data.countries.forEach(country => {
                    const opt = document.createElement('option');
                    opt.value = country.id;
                    opt.textContent = country.name;
                    if (preselectedCountryId && country.id === preselectedCountryId) { opt.selected = true; }
                    countrySelect.appendChild(opt);
                });
                if (preselectedCountryId) { loadCitiesForCountry(preselectedCountryId, preselectedLocationId); }
            }
        });

    function loadCitiesForCountry(countryId, selectCityId = null) {
        fetch(`/api/locations/${countryId}/cities`)
            .then(r => r.json())
            .then(data => {
                citySelect.innerHTML = '<option value="">Select City</option>';
                if (data.success && data.cities.length > 0) {
                    citySelect.disabled = false;
                    data.cities.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city.id;
                        opt.textContent = city.name;
                        if (selectCityId && city.id === selectCityId) { opt.selected = true; }
                        citySelect.appendChild(opt);
                    });
                }
            });
    }

    countrySelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
        if (this.value) { loadCitiesForCountry(this.value); }
    });

    function markImageForDeletion(imageId, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) { return; }
        const inputsContainer = document.getElementById('deleteImagesInputs');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_images[]';
        input.value = imageId;
        input.id = 'delete-input-' + imageId;
        inputsContainer.appendChild(input);
        wrapper.style.opacity = '0.3';
        const btn = wrapper.querySelector('button');
        if (btn) { btn.style.display = 'none'; }
        const undo = document.createElement('p');
        undo.className = 'text-[11px] text-[#D4AF37] mt-1 cursor-pointer hover:underline';
        undo.textContent = 'Undo removal';
        undo.onclick = function() {
            const delInput = document.getElementById('delete-input-' + imageId);
            if (delInput) { delInput.remove(); }
            wrapper.style.opacity = '1';
            if (btn) { btn.style.display = ''; }
            undo.remove();
        };
        wrapper.appendChild(undo);
    }

    const productImagesEl = document.getElementById('productImages');
    if (productImagesEl) {
        productImagesEl.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const previewContainer = document.getElementById('productImagesPreview');
            const controls = document.getElementById('productImagesControls');
            previewContainer.innerHTML = '';
            if (files.length > 0) {
                controls.classList.remove('hidden');
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        const badge = index === 0 ? '<span class="absolute bottom-1 left-1 text-[10px] bg-[#D4AF37] text-[#000000] font-semibold px-1.5 py-0.5 rounded">New Main</span>' : '';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded border border-[#E0E0E0]">
                            ${badge}
                            <button type="button"
                                    class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow"
                                    onclick="removeProductImage(${index})">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                controls.classList.add('hidden');
            }
        });
    }

    const clearAllProductImagesEl = document.getElementById('clearAllProductImages');
    if (clearAllProductImagesEl) {
        clearAllProductImagesEl.addEventListener('click', function() {
            document.getElementById('productImages').value = '';
            document.getElementById('productImagesPreview').innerHTML = '';
            document.getElementById('productImagesControls').classList.add('hidden');
        });
    }

    function removeProductImage(index) {
        const input = document.getElementById('productImages');
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => { if (i !== index) { dt.items.add(file); } });
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    categoryContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('category-select')) {
            const level = parseInt(e.target.dataset.level);
            const categoryId = e.target.value;
            const categoryName = e.target.options[e.target.selectedIndex].text;
            removeSelectsAfterLevel(level);
            removeVariantCardsAfterLevel(level);
            if (categoryId) {
                categoryHiddenInput.value = categoryId;
                categoryLevelsData[level] = { id: categoryId, name: categoryName };
                loadCategoriesForLevel(level + 1, categoryId);
                loadVariantsForCategory(categoryId, categoryName, level);
                window.dispatchEvent(new CustomEvent('category-changed', { detail: { categoryId, categoryName, level } }));
            } else {
                categoryHiddenInput.value = '';
                delete categoryLevelsData[level];
            }
        }
    });

    const selectClass = 'w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition category-select';

    const pendingCategoryFetches = {};

    function loadCategoriesForLevel(level, parentId) {
        if (pendingCategoryFetches[level]) { pendingCategoryFetches[level].abort(); }
        const controller = new AbortController();
        pendingCategoryFetches[level] = controller;
        const url = parentId ? `/api/categories/children/${parentId}` : '/api/categories/children';
        fetch(url, { signal: controller.signal, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            delete pendingCategoryFetches[level];
            if (data.success && data.categories.length > 0) {
                const existing = document.getElementById('category_level_' + level);
                if (existing) { existing.remove(); }
                const sel = document.createElement('select');
                sel.id = 'category_level_' + level;
                sel.className = selectClass;
                sel.dataset.level = level;
                sel.innerHTML = '<option value="">Select ' + (level === 0 ? 'Category' : 'Subcategory') + '</option>';
                data.categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    sel.appendChild(opt);
                });
                categoryContainer.appendChild(sel);
            }
        })
        .catch(err => { if (err.name !== 'AbortError') { console.error('Category load error:', err); } });
    }

    function removeSelectsAfterLevel(level) {
        categoryContainer.querySelectorAll('.category-select').forEach(sel => {
            if (parseInt(sel.dataset.level) > level) { sel.remove(); }
        });
    }

    function loadVariantsForCategory(categoryId, categoryName, level) {
        if (loadedVariantsByCategory.has(categoryId)) { return; }
        fetch(`/api/categories/${categoryId}/variants?show_all=true`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.variants.length > 0) {
                loadedVariantsByCategory.add(categoryId);
                renderVariantsCard(data.variants, categoryName, level, categoryId);
            }
        })
        .catch(err => console.error('Error loading variants:', err));
    }

    function renderVariantsCard(variants, categoryName, level, categoryId) {
        const card = document.createElement('div');
        card.className = 'bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden variant-card';
        card.dataset.level = level;
        card.dataset.categoryId = categoryId;

        const inputClass = 'w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition';
        const savedValues = window.EDIT_VARIANT_VALUES || {};
        let levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));

        let html = `
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0] bg-[#37474F]">
                <i class="fa-solid fa-sliders text-[#D4AF37] text-sm"></i>
                <h3 class="text-[13px] font-semibold text-white flex-1">${levelLabel} Attributes</h3>
                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-[#D4AF37] text-[#000000] text-[10px] font-bold">${variants.length}</span>
            </div>
            <div class="px-5 py-4 space-y-4">
                <p class="text-[11px] text-[#37474F] -mt-1">${categoryName}</p>
        `;

        variants.forEach(variant => {
            const saved = savedValues[variant.id];
            const savedItemId = saved ? saved.variant_item_id : null;
            const savedCustom = saved ? saved.custom_value : null;

            html += `<div>`;
            html += `<label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">${variant.name}${variant.is_required ? ' <span class="text-red-500">*</span>' : ''}</label>`;

            if (variant.type === 'select' || (variant.items && variant.items.length > 0)) {
                html += `<select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="${inputClass}"><option value="">Select ${variant.name}</option>`;
                (variant.items || []).forEach(item => {
                    const selected = savedItemId && savedItemId == item.id ? 'selected' : '';
                    html += `<option value="${item.id}" ${selected}>${item.display_value || item.value}</option>`;
                });
                html += `</select>`;
            } else if (variant.type === 'text') {
                const val = savedCustom ? String(savedCustom).replace(/"/g, '&quot;') : '';
                html += `<input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} value="${val}" placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            } else if (variant.type === 'number') {
                const val = savedCustom || '';
                html += `<input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} value="${val}" placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            }

            html += `</div>`;
        });

        html += `</div>`;
        card.innerHTML = html;
        variantsContainer.appendChild(card);
    }

    function removeVariantCardsAfterLevel(level) {
        variantsContainer.querySelectorAll('.variant-card').forEach(card => {
            const cardLevel = parseInt(card.dataset.level);
            if (cardLevel >= level) {
                const cid = card.dataset.categoryId;
                if (cid) { loadedVariantsByCategory.delete(parseInt(cid)); }
                card.remove();
            }
        });
    }

    // Discount toggle
    const hasDiscountCheckbox = document.getElementById('has_discount');
    const discountFields = document.getElementById('discountFields');
    if (hasDiscountCheckbox) {
        hasDiscountCheckbox.addEventListener('change', function() {
            if (this.checked) {
                discountFields.classList.remove('hidden');
            } else {
                discountFields.classList.add('hidden');
                document.getElementById('discount_price').value = '';
                document.getElementById('discount_start_date').value = '';
                document.getElementById('discount_end_date').value = '';
            }
        });
    }

    // Delivery toggle
    const hasDeliveryCheckbox = document.getElementById('has_delivery');
    const deliveryFields = document.getElementById('deliveryFields');
    if (hasDeliveryCheckbox) {
        hasDeliveryCheckbox.addEventListener('change', function() {
            deliveryFields.classList.toggle('hidden', !this.checked);
        });
    }

    // Initialize category dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        @if($listing->category_id)
            categoryHiddenInput.value = '{{ $listing->category_id }}';
            loadCategoryHierarchy({{ $listing->category_id }});
        @else
            loadCategoriesForLevel(0, null);
        @endif
    });

    function loadCategoryHierarchy(categoryId) {
        fetch(`/api/categories/${categoryId}/hierarchy`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.hierarchy) { loadCategoryLevel(data.hierarchy, 0); }
        })
        .catch(err => console.error('Error loading category hierarchy:', err));
    }

    function loadCategoryLevel(hierarchy, index) {
        if (index >= hierarchy.length) { return; }
        const item = hierarchy[index];
        const level = index;
        const parentId = index > 0 ? hierarchy[index - 1].id : null;
        const url = parentId ? `/api/categories/children/${parentId}` : '/api/categories/children';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.categories.length > 0) {
                const existing = document.getElementById('category_level_' + level);
                if (existing) { existing.remove(); }
                const sel = document.createElement('select');
                sel.id = 'category_level_' + level;
                sel.className = selectClass;
                sel.dataset.level = level;
                sel.innerHTML = '<option value="">Select ' + (level === 0 ? 'Category' : 'Subcategory') + '</option>';
                data.categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    if (cat.id === item.id) { opt.selected = true; }
                    sel.appendChild(opt);
                });
                categoryContainer.appendChild(sel);
                categoryLevelsData[level] = { id: item.id, name: item.name };
                loadVariantsForCategory(item.id, item.name, level);
                window.dispatchEvent(new CustomEvent('category-changed', { detail: { categoryId: item.id, categoryName: item.name, level } }));
                loadCategoryLevel(hierarchy, index + 1);
            }
        });
    }
    </script>
    @endpush

</x-app-layout>
