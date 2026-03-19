<x-app-layout>

    @php
        $user = auth()->user();
        $lCounts = $user->listings()->selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
        $lTotal   = $lCounts->sum();
        $lActive  = $lCounts->get('active', 0);
        $lPending = $lCounts->get('pending', 0);
        $lLimit   = $user->getListingLimit();
        $lPct     = $lLimit > 0 ? min(100, round(($lTotal / $lLimit) * 100)) : 0;
    @endphp

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('user.listings.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition mb-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to My Listings
            </a>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Create Listing</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Fill in the details below to publish your listing</p>
        </div>
    </div>

    {{-- ── Flash / Validation ───────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('user.listings.store') }}" method="POST" enctype="multipart/form-data" id="listingForm"
          x-data="listingForm({{ Illuminate\Support\Js::from($listingTypes->mapWithKeys(fn($t) => [$t->id => $t->slug])) }}, '{{ old('listing_type_id', '') }}')">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ════════════════════════════════════════════════════════════
                 LEFT  (col-span-2) — Main form sections
            ════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- ── Basic Information ─────────────────────────────────── --}}
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
                                <option value="">— Select Type —</option>
                                @foreach($listingTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('listing_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                    <option value="">— Select Category —</option>
                                </select>
                            </div>
                            <input type="hidden" name="category_id" id="category_id_hidden" required>
                            @error('category_id')
                                <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required maxlength="255"
                                   value="{{ old('title') }}"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                            @error('title')
                                <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Short Description</label>
                            <textarea name="short_description" rows="2" maxlength="500"
                                      placeholder="Brief summary for preview (optional)"
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('short_description') }}</textarea>
                            <p class="text-[11px] text-[#37474F] mt-1">Max 500 characters</p>
                        </div>

                        {{-- Full Description --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Type-Specific Details ──────────────────────────────── --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden" x-show="listingType !== ''">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-sliders text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]"
                            x-text="{'sell':'Pricing & Details','buy':'Budget','gift':'Item Details','barter':'Exchange Details','auction':'Auction Pricing'}[listingType] || 'Details'"></h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        {{-- SELL + AUCTION: Price --}}
                        <div x-show="['sell','auction'].includes(listingType)">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        <span x-text="listingType === 'auction' ? 'Starting Bid' : 'Price'"></span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="base_price" step="0.01" min="0.01"
                                           :required="['sell','auction'].includes(listingType)"
                                           value="{{ old('base_price') }}"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('base_price')<p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Currency</label>
                                    <select name="currency"
                                            class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        <option value="USD" {{ old('currency','USD')==='USD'?'selected':'' }}>USD</option>
                                        <option value="EUR" {{ old('currency')==='EUR'?'selected':'' }}>EUR</option>
                                        <option value="GBP" {{ old('currency')==='GBP'?'selected':'' }}>GBP</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SELL: Negotiable + Discount --}}
                        <div x-show="listingType === 'sell'" class="space-y-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_negotiable" id="is_negotiable" value="1"
                                       {{ old('is_negotiable') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] text-[#37474F]">Price is negotiable</span>
                            </label>

                            <div class="border-t border-[#E0E0E0] pt-4">
                                <label class="flex items-center gap-2 cursor-pointer mb-3">
                                    <input type="checkbox" id="has_discount" class="w-4 h-4 rounded accent-[#D4AF37]">
                                    <span class="text-[13px] font-semibold text-[#37474F]">Apply Discount</span>
                                </label>
                                <div id="discountFields" class="hidden grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Discount Price</label>
                                        <input type="number" name="discount_price" id="discount_price" step="0.01" min="0"
                                               value="{{ old('discount_price') }}"
                                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Start Date</label>
                                        <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                               value="{{ old('discount_start_date') }}"
                                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">End Date</label>
                                        <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                               value="{{ old('discount_end_date') }}"
                                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
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
                                           value="{{ old('budget_max') }}" placeholder="Leave blank if flexible"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('budget_max')<p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Currency</label>
                                    <select name="currency"
                                            class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        <option value="USD" {{ old('currency','USD')==='USD'?'selected':'' }}>USD</option>
                                        <option value="EUR" {{ old('currency')==='EUR'?'selected':'' }}>EUR</option>
                                        <option value="GBP" {{ old('currency')==='GBP'?'selected':'' }}>GBP</option>
                                    </select>
                                </div>
                            </div>
                            <p class="text-[11px] text-[#37474F] mt-2">Post a request for an item you want to buy. Sellers with matching items can contact you.</p>
                        </div>

                        {{-- BARTER: Exchange --}}
                        <div x-show="listingType === 'barter'">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                What do you want in exchange? <span class="text-red-500">*</span>
                            </label>
                            <textarea name="barter_exchange_for" rows="3" maxlength="1000"
                                      :required="listingType === 'barter'"
                                      placeholder="Describe the item(s) or type of exchange you're looking for..."
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('barter_exchange_for') }}</textarea>
                            @error('barter_exchange_for')<p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- AUCTION: Details --}}
                        <div x-show="listingType === 'auction'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Auction End Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="auction_end_date"
                                           :required="listingType === 'auction'"
                                           value="{{ old('auction_end_date') }}"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                    @error('auction_end_date')<p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Reserve Price (optional)</label>
                                    <input type="number" name="auction_reserve_price" step="0.01" min="0"
                                           value="{{ old('auction_reserve_price') }}" placeholder="Minimum price to sell"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Min Bid Increment (optional)</label>
                                <input type="number" name="auction_bid_increment" step="0.01" min="0.01"
                                       value="{{ old('auction_bid_increment') }}" placeholder="e.g. 1.00"
                                       class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                            </div>
                        </div>

                        {{-- GIFT: Info --}}
                        <div x-show="listingType === 'gift'">
                            <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded text-[13px] text-green-700">
                                <i class="fa-solid fa-gift flex-shrink-0 mt-0.5"></i>
                                <p>This item will be listed as <strong>free</strong>. Describe it above and specify pickup or delivery arrangements.</p>
                            </div>
                        </div>

                        {{-- Condition: all types --}}
                        <div class="border-t border-[#E0E0E0] pt-4" x-show="listingType !== ''">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">
                                <span x-text="listingType === 'buy' ? 'Preferred Condition' : 'Item Condition'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="condition" value="new"
                                           {{ old('condition','new') === 'new' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#1A1A1A]">New</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="condition" value="used"
                                           {{ old('condition') === 'used' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#1A1A1A]">Second-hand</span>
                                </label>
                            </div>
                        </div>

                        {{-- Delivery: all except Buy --}}
                        <div class="border-t border-[#E0E0E0] pt-4" x-show="listingType !== '' && listingType !== 'buy'">
                            <label class="flex items-center gap-2 cursor-pointer mb-4">
                                <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                       {{ old('has_delivery') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] font-semibold text-[#37474F]">Delivery Available</span>
                            </label>
                            <div id="deliveryFields" class="{{ old('has_delivery') ? '' : 'hidden' }} space-y-3">
                                @foreach([
                                    ['Same City',                    'has_same_city_delivery',    'same_city_delivery_price',    'same_city_delivery_days'],
                                    ['Same Country (Different City)','has_domestic_delivery',     'domestic_delivery_price',     'domestic_delivery_days'],
                                    ['International',                'has_international_delivery','international_delivery_price','international_delivery_days'],
                                ] as [$label, $checkName, $priceName, $daysName])
                                <div class="border border-[#E0E0E0] rounded p-3">
                                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                                        <input type="checkbox" name="{{ $checkName }}" value="1"
                                               {{ old($checkName) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded accent-[#D4AF37]">
                                        <span class="text-[13px] font-semibold text-[#1A1A1A]">{{ $label }}</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">Price</label>
                                            <input type="number" name="{{ $priceName }}" step="0.01" min="0"
                                                   value="{{ old($priceName) }}" placeholder="0.00"
                                                   class="w-full h-[32px] px-3 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">Days</label>
                                            <input type="number" name="{{ $daysName }}" min="1" max="365"
                                                   value="{{ old($daysName) }}" placeholder="e.g. 3"
                                                   class="w-full h-[32px] px-3 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── Location ───────────────────────────────────────────── --}}
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
                                    <option value="">— Select Country —</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">City</label>
                                <select name="location_id" id="city_select" disabled
                                        class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition disabled:opacity-50">
                                    <option value="">— Select country first —</option>
                                </select>
                                @error('location_id')<p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Submit Actions ─────────────────────────────────────── --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('user.listings.index') }}"
                       class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        Submit Listing
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 RIGHT (col-span-1) — Dashboard stats + Variants + Images
            ════════════════════════════════════════════════════════════ --}}
            <div class="space-y-5">

                {{-- ── Listing Limit (dashboard widget) ─────────────────── --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-gauge text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Your Listing Stats</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        {{-- Usage bar --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider">Usage</span>
                                <span class="text-[12px] font-bold text-[#1A1A1A]">
                                    {{ $lTotal }} / {{ $lLimit > 0 ? $lLimit : '∞' }}
                                </span>
                            </div>
                            <div class="h-2 bg-[#F5F5F5] rounded-full overflow-hidden border border-[#E0E0E0]">
                                <div class="h-full rounded-full transition-all {{ $lPct >= 90 ? 'bg-red-500' : ($lPct >= 70 ? 'bg-yellow-500' : 'bg-[#D4AF37]') }}"
                                     style="width: {{ $lPct }}%"></div>
                            </div>
                            @if($lLimit > 0 && $lTotal >= $lLimit)
                            <p class="text-[11px] text-red-500 mt-1">Limit reached — upgrade to add more listings.</p>
                            @endif
                        </div>
                        {{-- Status breakdown --}}
                        <div class="space-y-2">
                            @foreach([
                                ['Active',   $lActive,                          'text-green-600',  'fa-circle-check'],
                                ['Pending',  $lPending,                         'text-yellow-600', 'fa-clock'],
                                ['Expired',  $lCounts->get('expired', 0),       'text-red-400',    'fa-calendar-xmark'],
                                ['Rejected', $lCounts->get('rejected', 0),      'text-red-600',    'fa-ban'],
                            ] as [$label, $count, $color, $icon])
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-1.5 text-[12px] text-[#37474F]">
                                    <i class="fa-solid {{ $icon }} text-[10px] {{ $color }}"></i>
                                    {{ $label }}
                                </span>
                                <span class="text-[12px] font-semibold text-[#1A1A1A]">{{ $count }}</span>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('user.listings.index') }}"
                           class="flex items-center justify-center gap-2 w-full h-[30px] border border-[#E0E0E0] text-[12px] font-semibold text-[#37474F] rounded hover:bg-[#F5F5F5] transition">
                            <i class="fa-solid fa-list text-[10px]"></i>
                            View All Listings
                        </a>
                    </div>
                </div>

                {{-- ── Variant Selectors (injected by JS) ───────────────── --}}
                <div id="variantsContainer" class="space-y-5"></div>

                {{-- ── Product Images ────────────────────────────────────── --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-images text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Product Images</h2>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-[11px] text-[#37474F] mb-3">First image becomes the main image. Max 10 files.</p>

                        <label class="flex flex-col items-center justify-center w-full h-[80px] border-2 border-dashed border-[#E0E0E0] rounded cursor-pointer hover:border-[#D4AF37]/50 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-[#37474F] text-xl mb-1"></i>
                            <span class="text-[12px] text-[#37474F]">Click to upload</span>
                            <input type="file" name="product_images[]" id="productImages" multiple
                                   accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                        </label>

                        <div id="productImagesPreview" class="mt-3 grid grid-cols-3 gap-2"></div>

                        <div id="productImagesContainer" class="hidden mt-2">
                            <button type="button" id="clearAllProductImages"
                                    class="text-[11px] text-red-500 hover:text-red-700 transition">
                                <i class="fa-solid fa-trash text-[10px] mr-1"></i>Clear all
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- /right --}}
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

    // ── DOM references ────────────────────────────────────────────────────
    const categoryContainer  = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const variantsContainer  = document.getElementById('variantsContainer');
    const countrySelect      = document.getElementById('country_select');
    const citySelect         = document.getElementById('city_select');

    let categoryLevelsData      = {};
    let loadedVariantsByCategory = new Set();

    // ── Luxury CSS classes used in dynamically created elements ──────────
    const selectClass = 'w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition category-select';
    const inputClass  = 'w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition';

    // ── Load countries ────────────────────────────────────────────────────
    fetch('/api/locations/countries')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                data.countries.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    countrySelect.appendChild(opt);
                });
            }
        });

    countrySelect.addEventListener('change', function () {
        citySelect.innerHTML = '<option value="">— Select City —</option>';
        citySelect.disabled = true;
        if (this.value) {
            fetch(`/api/locations/${this.value}/cities`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.cities.length > 0) {
                        citySelect.disabled = false;
                        data.cities.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.name;
                            citySelect.appendChild(opt);
                        });
                    }
                });
        }
    });

    // ── Category loading ──────────────────────────────────────────────────
    const pendingCategoryFetches = {};
    loadCategoriesForLevel(0, null);

    categoryContainer.addEventListener('change', function (e) {
        if (!e.target.classList.contains('category-select')) { return; }
        const level      = parseInt(e.target.dataset.level);
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
    });

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
                    sel.innerHTML = '<option value="">— Select ' + (level === 0 ? 'Category' : 'Subcategory') + ' —</option>';
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

    // ── Variant cards ─────────────────────────────────────────────────────
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
        .catch(err => console.error('Variant load error:', err));
    }

    function renderVariantsCard(variants, categoryName, level, categoryId) {
        const card = document.createElement('div');
        card.className = 'bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden variant-card';
        card.dataset.level = level;
        card.dataset.categoryId = categoryId;

        const levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));
        let html = `
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-tags text-[#37474F] text-sm"></i>
                <div>
                    <p class="text-[13px] font-semibold text-[#1A1A1A]">${levelLabel} Attributes</p>
                    <p class="text-[11px] text-[#37474F]">${categoryName}</p>
                </div>
                <span class="ml-auto px-2 py-0.5 text-[10px] font-semibold bg-[#D4AF37]/15 text-[#1A1A1A] rounded-full">${variants.length}</span>
            </div>
            <div class="px-5 py-4 space-y-4">
        `;

        variants.forEach(variant => {
            html += `<div>`;
            html += `<label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">${variant.name}${variant.is_required ? ' <span class="text-red-500">*</span>' : ''}</label>`;

            if (variant.type === 'select' || (variant.items && variant.items.length > 0)) {
                html += `<select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="${inputClass}"><option value="">— Select ${variant.name} —</option>`;
                (variant.items || []).forEach(item => {
                    html += `<option value="${item.id}">${item.display_value || item.value}</option>`;
                });
                html += `</select>`;
            } else if (variant.type === 'text') {
                html += `<input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            } else if (variant.type === 'number') {
                html += `<input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            }

            html += `</div>`;
        });

        html += `</div>`;
        card.innerHTML = html;
        variantsContainer.appendChild(card);
    }

    function removeVariantCardsAfterLevel(level) {
        variantsContainer.querySelectorAll('.variant-card').forEach(card => {
            if (parseInt(card.dataset.level) >= level) {
                const cid = card.dataset.categoryId;
                if (cid) { loadedVariantsByCategory.delete(parseInt(cid)); }
                card.remove();
            }
        });
    }

    // ── Image preview ─────────────────────────────────────────────────────
    const productImagesEl = document.getElementById('productImages');
    if (productImagesEl) {
        productImagesEl.addEventListener('change', function (e) {
            const files = Array.from(e.target.files);
            const preview = document.getElementById('productImagesPreview');
            const container = document.getElementById('productImagesContainer');
            preview.innerHTML = '';
            if (files.length > 0) {
                container.classList.remove('hidden');
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.className = 'relative group aspect-square';
                        const badge = index === 0
                            ? '<span class="absolute bottom-1 left-1 px-1 text-[10px] font-bold bg-[#D4AF37] text-[#000000] rounded">Main</span>'
                            : '';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover rounded border border-[#E0E0E0]">
                            ${badge}
                            <button type="button"
                                    class="absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[10px]"
                                    onclick="removeProductImage(${index})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                container.classList.add('hidden');
            }
        });
    }

    document.getElementById('clearAllProductImages')?.addEventListener('click', function () {
        document.getElementById('productImages').value = '';
        document.getElementById('productImagesPreview').innerHTML = '';
        document.getElementById('productImagesContainer').classList.add('hidden');
    });

    function removeProductImage(index) {
        const input = document.getElementById('productImages');
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => { if (i !== index) { dt.items.add(file); } });
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    // ── Discount toggle ───────────────────────────────────────────────────
    document.getElementById('has_discount')?.addEventListener('change', function () {
        const fields = document.getElementById('discountFields');
        if (this.checked) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
            ['discount_price','discount_start_date','discount_end_date'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.value = ''; }
            });
        }
    });

    // ── Delivery toggle ───────────────────────────────────────────────────
    document.getElementById('has_delivery')?.addEventListener('change', function () {
        document.getElementById('deliveryFields').classList.toggle('hidden', !this.checked);
    });
    </script>
    @endpush

</x-app-layout>
