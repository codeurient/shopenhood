<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('business.listings.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition mb-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to Business Listings
            </a>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Edit Business Listing</h1>
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

    @if(session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('business.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" id="listingForm">
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
                                   placeholder="Product title"
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

                {{-- Pricing & Details --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-tag text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Pricing & Details</h2>
                    </div>
                    <div class="px-5 py-4 space-y-5">

                        {{-- Currency --}}
                        <div class="grid grid-cols-2 gap-4">
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
                        <p class="text-[11px] text-[#37474F] -mt-2">Prices are set per variation in the product variations section below.</p>

                        {{-- Condition --}}
                        <div class="border-t border-[#E0E0E0] pt-4">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">
                                Product Condition <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="condition" value="new"
                                           {{ old('condition', $listing->condition ?? 'new') === 'new' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">New</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="condition" value="used"
                                           {{ old('condition', $listing->condition) === 'used' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">Second-hand</span>
                                </label>
                            </div>
                        </div>

                        {{-- Availability --}}
                        <div class="border-t border-[#E0E0E0] pt-4">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">
                                Product Availability <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="availability_type" value="in_stock"
                                           {{ old('availability_type', $listing->availability_type ?? 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">In Stock</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="availability_type" value="available_by_order"
                                           {{ old('availability_type', $listing->availability_type) === 'available_by_order' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">Available by Order</span>
                                </label>
                            </div>
                            <p class="mt-1.5 text-[11px] text-[#37474F]">Select "Available by Order" if the item is not currently in stock but can be supplied upon request.</p>
                        </div>

                        {{-- Store Type --}}
                        <div class="border-t border-[#E0E0E0] pt-4">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">Store Type</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="has_store" value="0"
                                           {{ old('has_store', $listing->has_store ? '1' : '0') === '0' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">No Store</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="has_store" value="1"
                                           {{ old('has_store', $listing->has_store ? '1' : '0') === '1' ? 'checked' : '' }}
                                           class="w-4 h-4 accent-[#D4AF37]">
                                    <span class="text-[13px] text-[#37474F]">Store</span>
                                </label>
                            </div>
                            <p class="mt-1.5 text-[11px] text-[#37474F]">Select "Store" if this product is sold from a physical store location.</p>
                        </div>

                        {{-- Delivery --}}
                        <div class="border-t border-[#E0E0E0] pt-4">
                            <label class="flex items-center gap-2 cursor-pointer mb-4">
                                <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                       {{ old('has_delivery', $listing->has_delivery) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] font-semibold text-[#1A1A1A]">Delivery Available</span>
                            </label>
                            <div id="deliveryFields" class="{{ old('has_delivery', $listing->has_delivery) ? '' : 'hidden' }} space-y-3">
                                @foreach([
                                    ['has_same_city_delivery',     'same_city_delivery_price',     'same_city_delivery_days',     'Same City',                    $listing->has_same_city_delivery,     $listing->same_city_delivery_price,     $listing->same_city_delivery_days,     '1'],
                                    ['has_domestic_delivery',      'domestic_delivery_price',      'domestic_delivery_days',      'Same Country (Different City)', $listing->has_domestic_delivery,      $listing->domestic_delivery_price,      $listing->domestic_delivery_days,      '3'],
                                    ['has_international_delivery', 'international_delivery_price', 'international_delivery_days', 'International',                $listing->has_international_delivery, $listing->international_delivery_price, $listing->international_delivery_days, '14'],
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

                {{-- Store Information --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-store text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Store Information</h2>
                    </div>
                    <div class="px-5 py-4">
                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Store Name</label>
                        <input type="text" name="store_name" value="{{ old('store_name', $listing->store_name) }}" maxlength="255"
                               placeholder="Enter your store name"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        <p class="mt-1 text-[11px] text-[#37474F]">Displayed as "Shared from [Store Name]"</p>
                    </div>
                </div>

                {{-- SEO Settings --}}
                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                        <i class="fa-solid fa-magnifying-glass-chart text-[#37474F] text-sm"></i>
                        <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Search Engine Optimization (SEO)</h2>
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Pro Feature</span>
                    </div>
                    <div class="px-5 py-4 space-y-5">

                        {{-- Why SEO matters --}}
                        <div class="flex items-start gap-3 px-4 py-3 bg-blue-50 border border-blue-200 rounded text-[13px] text-blue-700">
                            <i class="fa-solid fa-lightbulb flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="font-semibold mb-0.5">Why does SEO matter for your listing?</p>
                                <p class="text-[12px]">The <strong>Meta Title</strong> appears as the clickable headline in Google and the <strong>Meta Description</strong> appears as the short text snippet below it. Well-written, keyword-rich fields increase your listing's visibility and click-through rate — bringing more potential buyers without paid advertising.</p>
                            </div>
                        </div>

                        {{-- Search result preview --}}
                        <div class="border border-[#E0E0E0] rounded overflow-hidden">
                            <div class="px-3 py-2 bg-[#37474F] text-[11px] text-[#D4AF37] font-semibold uppercase tracking-wider">
                                <i class="fa-brands fa-google mr-1"></i> Search result preview
                            </div>
                            <div class="px-4 py-3 bg-white">
                                <p class="text-blue-600 text-[14px] font-medium">Your Meta Title appears here as the clickable headline</p>
                                <p class="text-green-700 text-[11px] mt-0.5">https://{{ parse_url(config('app.url'), PHP_URL_HOST) }}/listing/your-product-slug</p>
                                <p class="text-[#37474F] text-[12px] mt-1">Your Meta Description appears here — a short summary that convinces the user to click through to your listing.</p>
                            </div>
                        </div>

                        {{-- Meta Title --}}
                        <div x-data="{ charCount: {{ strlen(old('meta_title', $listing->meta_title ?? '')) }} }">
                            <label class="flex items-center gap-2 text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Meta Title
                                <span class="font-normal normal-case" :class="charCount > 60 ? 'text-red-500' : 'text-[#37474F]'">
                                    (<span x-text="charCount"></span>/60)
                                </span>
                            </label>
                            <p class="text-[11px] text-[#37474F] mb-2">Aim for 50–60 characters. Place your most important keyword near the start. Example: <em>"Premium Cotton T-Shirts Wholesale | YourBrand"</em></p>
                            <input type="text" name="meta_title" maxlength="60"
                                   value="{{ old('meta_title', $listing->meta_title) }}"
                                   x-on:input="charCount = $event.target.value.length"
                                   placeholder="e.g., Premium Cotton T-Shirts Wholesale | Your Brand"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                            @error('meta_title')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Meta Description --}}
                        <div x-data="{ charCount: {{ strlen(old('meta_description', $listing->meta_description ?? '')) }} }">
                            <label class="flex items-center gap-2 text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Meta Description
                                <span class="font-normal normal-case" :class="charCount > 160 ? 'text-red-500' : (charCount > 120 ? 'text-yellow-500' : 'text-[#37474F]')">
                                    (<span x-text="charCount"></span>/160)
                                </span>
                            </label>
                            <p class="text-[11px] text-[#37474F] mb-2">Use 120–160 characters. Highlight a key benefit or call-to-action. Example: <em>"Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping. Free sample on orders over $200."</em></p>
                            <textarea name="meta_description" rows="3" maxlength="160"
                                      x-on:input="charCount = $event.target.value.length"
                                      placeholder="e.g., Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping. Free sample on orders over $200."
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('meta_description', $listing->meta_description) }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── Right Column: Non-main-shown attributes ─────────────── --}}
            <div class="space-y-5">
                <div id="nonMainShownVariantsContainer"
                     class="hidden bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden"></div>
            </div>
        </div>

        {{-- ── Product Variations ───────────────────────────────────────── --}}
        <div class="mt-5">
            @include('user.listings.partials.variation-manager', ['mode' => 'edit', 'listing' => $listing])
        </div>

        {{-- ── Footer Actions ───────────────────────────────────────────── --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('business.listings.index') }}"
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

    @php
        $variationsData = $listing->variations->map(function ($v) {
            return [
                'id'                        => $v->id,
                'sku'                       => $v->sku,
                'attributes'                => $v->attributes->mapWithKeys(fn($a) => [$a->variant_id => $a->variant_item_id])->toArray(),
                'price'                     => (float) $v->price,
                'discount_price'            => $v->discount_price ? (float) $v->discount_price : null,
                'discount_start_date'       => $v->discount_start_date ? $v->discount_start_date->format('Y-m-d') : '',
                'discount_end_date'         => $v->discount_end_date ? $v->discount_end_date->format('Y-m-d') : '',
                'stock_quantity'            => $v->stock_quantity,
                'low_stock_threshold'       => $v->low_stock_threshold,
                'manage_stock'              => $v->manage_stock,
                'allow_backorder'           => $v->allow_backorder,
                'is_default'                => $v->is_default,
                'is_active'                 => $v->is_active,
                'is_wholesale'              => (bool) $v->is_wholesale,
                'wholesale_min_order_qty'   => $v->wholesale_min_order_qty,
                'wholesale_qty_increment'   => $v->wholesale_qty_increment ?? 1,
                'wholesale_sample_available'=> (bool) $v->wholesale_sample_available,
                'wholesale_sample_price'    => $v->wholesale_sample_price ? (float) $v->wholesale_sample_price : null,
                'wholesale_terms'           => $v->wholesale_terms ?? '',
                'images'                    => [],
                'existing_images'           => $v->images->map(fn($img) => [
                    'id'  => $img->id,
                    'url' => asset('storage/'.$img->image_path),
                ])->values()->toArray(),
                'deleted_image_ids'         => [],
            ];
        })->toArray();
    @endphp

    @push('scripts')
    <script>
    window.EDIT_VARIATIONS = @json($variationsData);
    window.EDIT_VARIANT_ATTRIBUTES = @json($listing->variant_attributes ?? []);

    const categoryContainer = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');

    let categoryLevelsData = {};

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

    function loadVariantsForChain(categoryIds, savedValues) {
        if (categoryIds.length === 0) { return; }
        const promises = categoryIds.map(id =>
            fetch(`/api/categories/${id}/variants?show_all=true`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .catch(() => ({ success: false, variants: [] }))
        );
        Promise.all(promises).then(results => {
            const seen = new Set();
            const merged = [];
            results.forEach(data => {
                if (data.success && data.variants) {
                    data.variants.forEach(v => { if (!seen.has(v.id)) { seen.add(v.id); merged.push(v); } });
                }
            });
            renderNonMainShownVariants(merged, savedValues || {});
        });
    }

    function renderNonMainShownVariants(variants, savedValues) {
        const container = document.getElementById('nonMainShownVariantsContainer');
        if (!container) { return; }
        const nonMain = variants.filter(v => !v.is_main_shown);
        if (nonMain.length === 0) { container.classList.add('hidden'); return; }
        container.classList.remove('hidden');

        const inputClass = 'w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition';

        container.innerHTML = `
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0] bg-[#37474F]">
                <i class="fa-solid fa-sliders text-[#D4AF37] text-sm"></i>
                <h3 class="text-[13px] font-semibold text-white flex-1">Product Attributes</h3>
                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-[#D4AF37] text-[#000000] text-[10px] font-bold">${nonMain.length}</span>
            </div>
            <div class="px-5 py-4 space-y-4">
                <p class="text-[11px] text-[#37474F] -mt-1">Additional characteristics specific to this product.</p>
            </div>
        `;

        const body = container.querySelector('.px-5.py-4');

        nonMain.forEach(variant => {
            const savedItemId = savedValues ? savedValues[variant.id] : null;
            const div = document.createElement('div');
            let html = `<label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">${variant.name}</label>`;
            if (variant.items && variant.items.length > 0) {
                html += `<select name="variant_attributes[${variant.id}]" class="${inputClass}"><option value="">Select ${variant.name}</option>`;
                variant.items.forEach(item => {
                    const selected = savedItemId && savedItemId == item.id ? 'selected' : '';
                    html += `<option value="${item.id}" ${selected}>${item.display_value || item.value}</option>`;
                });
                html += `</select>`;
            } else {
                const val = savedItemId ? String(savedItemId).replace(/"/g, '&quot;') : '';
                html += `<input type="text" name="variant_attributes[${variant.id}]" value="${val}" placeholder="${variant.name}" class="${inputClass}">`;
            }
            div.innerHTML = html;
            body.appendChild(div);
        });
    }

    categoryContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('category-select')) {
            const level = parseInt(e.target.dataset.level);
            const categoryId = e.target.value;
            const categoryName = e.target.options[e.target.selectedIndex].text;

            removeSelectsAfterLevel(level);
            Object.keys(categoryLevelsData).forEach(l => { if (parseInt(l) > level) { delete categoryLevelsData[l]; } });

            const container = document.getElementById('nonMainShownVariantsContainer');
            if (container) { container.innerHTML = ''; container.classList.add('hidden'); }

            if (categoryId) {
                categoryHiddenInput.value = categoryId;
                categoryLevelsData[level] = { id: categoryId, name: categoryName };
                loadCategoriesForLevel(level + 1, categoryId);
                const chainIds = [];
                for (let l = 0; l <= level; l++) { if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); } }
                loadVariantsForChain(chainIds, {});
                window.dispatchEvent(new CustomEvent('category-changed', { detail: { categoryId, categoryName, level } }));
            } else {
                categoryHiddenInput.value = '';
                delete categoryLevelsData[level];
                const chainIds = [];
                for (let l = 0; l < level; l++) { if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); } }
                if (chainIds.length > 0) { loadVariantsForChain(chainIds, {}); }
            }
        }
    });

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
                window.dispatchEvent(new CustomEvent('category-changed', { detail: { categoryId: item.id, categoryName: item.name, level } }));

                if (index + 1 >= hierarchy.length) {
                    const chainIds = [];
                    for (let l = 0; l <= level; l++) { if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); } }
                    loadVariantsForChain(chainIds, window.EDIT_VARIANT_ATTRIBUTES || {});
                }

                loadCategoryLevel(hierarchy, index + 1);
            }
        });
    }
    </script>
    @endpush

</x-app-layout>
