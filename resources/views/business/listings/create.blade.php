<x-app-layout>
    <div class="bg-[#FFFFFF] min-h-screen py-8">
        <div class="w-full">

            {{-- Page header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-[0.15em] mb-1">Business Panel</p>
                    <h1 class="font-bold text-[#1A1A1A]" style="font-size:clamp(1.1rem,2vw,1.35rem);">Create Business Listing</h1>
                </div>
                <a href="{{ route('business.listings.index') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-medium rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back
                </a>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-5 p-4 border-l-4 border-red-500 bg-red-50 rounded-r-[4px]">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-red-600 flex items-start gap-1.5">
                                <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>{{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 border-l-4 border-red-500 bg-red-50 rounded-r-[4px] text-xs text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('business.listings.store') }}" method="POST" enctype="multipart/form-data" id="listingForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    {{-- ── Left / Main Column ── --}}
                    <div class="lg:col-span-2 space-y-5">

                        {{-- Basic Information --}}
                        <div class="bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5" style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <h2 class="text-[13px] font-semibold text-[#1A1A1A] mb-4 pb-3 border-b border-[#E0E0E0]">
                                <i class="fa-solid fa-circle-info text-[#D4AF37] mr-2"></i>Basic Information
                            </h2>
                            <div class="space-y-4">

                                {{-- Listing Type --}}
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Listing Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="listing_type_id" id="listing_type_id" required
                                            class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                        <option value="">Select type…</option>
                                        @foreach($listingTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('listing_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('listing_type_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Category --}}
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <div id="categorySelectsContainer" class="space-y-2">
                                        <select id="category_level_0" required
                                                class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 category-select"
                                                data-level="0">
                                            <option value="">Select category…</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="category_id" id="category_id_hidden" required>
                                    @error('category_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Title --}}
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" required maxlength="255"
                                           value="{{ old('title') }}"
                                           placeholder="Product title…"
                                           class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                    @error('title')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Short Description --}}
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Short Description
                                    </label>
                                    <textarea name="short_description" rows="2" maxlength="500"
                                              placeholder="Brief summary shown in listing cards (optional, max 500 characters)…"
                                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 resize-none placeholder-[#37474F]">{{ old('short_description') }}</textarea>
                                </div>

                                {{-- Full Description --}}
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                        Full Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description" rows="6" required
                                              placeholder="Detailed product description…"
                                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 resize-none placeholder-[#37474F]">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Pricing & Options --}}
                        <div class="bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5" style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <h2 class="text-[13px] font-semibold text-[#1A1A1A] mb-4 pb-3 border-b border-[#E0E0E0]">
                                <i class="fa-solid fa-tag text-[#D4AF37] mr-2"></i>Pricing &amp; Options
                            </h2>
                            <div class="space-y-5">

                                {{-- Currency --}}
                                <div class="max-w-[160px]">
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Currency</label>
                                    <select name="currency"
                                            class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                        <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                    </select>
                                </div>
                                <p class="text-[11px] text-[#37474F]">Prices are set per variation in the product variations section below.</p>

                                {{-- Product Condition --}}
                                <div class="border-t border-[#E0E0E0] pt-4">
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">
                                        Product Condition <span class="text-red-500">*</span>
                                    </label>
                                    <div x-data="{ condition: '{{ old('condition', 'new') }}' }" class="space-y-3">
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="condition" value="new"
                                                       x-model="condition"
                                                       {{ old('condition', 'new') === 'new' ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37]">
                                                <span class="text-[13px] text-[#37474F]">New</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="condition" value="used"
                                                       x-model="condition"
                                                       {{ old('condition') === 'used' ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37]">
                                                <span class="text-[13px] text-[#37474F]">Second-hand</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="condition" value="refurbished"
                                                       x-model="condition"
                                                       {{ old('condition') === 'refurbished' ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37]">
                                                <span class="text-[13px] text-[#37474F]">Refurbished</span>
                                            </label>
                                        </div>
                                        <div x-show="condition === 'refurbished'" x-cloak>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">
                                                Refurbished Details <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="refurbished_description" rows="3"
                                                      placeholder="Describe which parts were modified or replaced and what changes were made…"
                                                      class="w-full border border-[#E0E0E0] rounded focus:border-[#D4AF37] focus:ring-0 text-[13px] text-[#1A1A1A] placeholder-[#37474F] px-3 py-2 resize-none">{{ old('refurbished_description') }}</textarea>
                                            @error('refurbished_description')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
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
                                                   {{ old('availability_type', 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                                   class="w-4 h-4 accent-[#D4AF37]">
                                            <span class="text-[13px] text-[#37474F]">In Stock</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="availability_type" value="available_by_order"
                                                   {{ old('availability_type') === 'available_by_order' ? 'checked' : '' }}
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
                                                   {{ old('has_store', '0') === '0' ? 'checked' : '' }}
                                                   class="w-4 h-4 accent-[#D4AF37]">
                                            <span class="text-[13px] text-[#37474F]">Online only</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="has_store" value="1"
                                                   {{ old('has_store') === '1' ? 'checked' : '' }}
                                                   class="w-4 h-4 accent-[#D4AF37]">
                                            <span class="text-[13px] text-[#37474F]">Physical store</span>
                                        </label>
                                    </div>
                                    <p class="mt-1.5 text-[11px] text-[#37474F]">Select "Physical store" if this product is sold from a physical store location.</p>
                                </div>

                                {{-- Delivery --}}
                                <div class="border-t border-[#E0E0E0] pt-4">
                                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                                        <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                               {{ old('has_delivery') ? 'checked' : '' }}
                                               class="w-4 h-4 accent-[#D4AF37] rounded">
                                        <span class="text-[13px] font-semibold text-[#1A1A1A]">Delivery Available</span>
                                    </label>
                                    <div id="deliveryFields" class="{{ old('has_delivery') ? '' : 'hidden' }} space-y-3">
                                        {{-- Same City --}}
                                        <div class="border border-[#E0E0E0] rounded-[6px] p-3">
                                            <label class="flex items-center gap-2 cursor-pointer mb-3">
                                                <input type="checkbox" name="has_same_city_delivery" value="1"
                                                       {{ old('has_same_city_delivery') ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37] rounded">
                                                <span class="text-[13px] font-semibold text-[#1A1A1A]">Same City</span>
                                            </label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Price</label>
                                                    <input type="number" name="same_city_delivery_price" step="0.01" min="0"
                                                           value="{{ old('same_city_delivery_price') }}" placeholder="0.00"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Days</label>
                                                    <input type="number" name="same_city_delivery_days" min="1" max="365"
                                                           value="{{ old('same_city_delivery_days') }}" placeholder="e.g. 1"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Domestic --}}
                                        <div class="border border-[#E0E0E0] rounded-[6px] p-3">
                                            <label class="flex items-center gap-2 cursor-pointer mb-3">
                                                <input type="checkbox" name="has_domestic_delivery" value="1"
                                                       {{ old('has_domestic_delivery') ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37] rounded">
                                                <span class="text-[13px] font-semibold text-[#1A1A1A]">Same Country (Different City)</span>
                                            </label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Price</label>
                                                    <input type="number" name="domestic_delivery_price" step="0.01" min="0"
                                                           value="{{ old('domestic_delivery_price') }}" placeholder="0.00"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Days</label>
                                                    <input type="number" name="domestic_delivery_days" min="1" max="365"
                                                           value="{{ old('domestic_delivery_days') }}" placeholder="e.g. 3"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- International --}}
                                        <div class="border border-[#E0E0E0] rounded-[6px] p-3">
                                            <label class="flex items-center gap-2 cursor-pointer mb-3">
                                                <input type="checkbox" name="has_international_delivery" value="1"
                                                       {{ old('has_international_delivery') ? 'checked' : '' }}
                                                       class="w-4 h-4 accent-[#D4AF37] rounded">
                                                <span class="text-[13px] font-semibold text-[#1A1A1A]">International</span>
                                            </label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Price</label>
                                                    <input type="number" name="international_delivery_price" step="0.01" min="0"
                                                           value="{{ old('international_delivery_price') }}" placeholder="0.00"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-[#37474F] mb-1">Days</label>
                                                    <input type="number" name="international_delivery_days" min="1" max="365"
                                                           value="{{ old('international_delivery_days') }}" placeholder="e.g. 14"
                                                           class="w-full h-[34px] border border-[#E0E0E0] text-[13px] text-[#1A1A1A] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5" style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <h2 class="text-[13px] font-semibold text-[#1A1A1A] mb-4 pb-3 border-b border-[#E0E0E0]">
                                <i class="fa-solid fa-location-dot text-[#D4AF37] mr-2"></i>Location
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Country</label>
                                    <select id="country_select"
                                            class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                        <option value="">Select country…</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">City</label>
                                    <select name="location_id" id="city_select" disabled
                                            class="w-full h-[34px] border border-[#E0E0E0] text-[#37474F] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 disabled:opacity-50">
                                        <option value="">Select country first</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Store Information --}}
                        <div class="bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5" style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <h2 class="text-[13px] font-semibold text-[#1A1A1A] mb-4 pb-3 border-b border-[#E0E0E0]">
                                <i class="fa-solid fa-store text-[#D4AF37] mr-2"></i>Store Information
                            </h2>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Store Name</label>
                                <input type="text" name="store_name" value="{{ old('store_name') }}" maxlength="255"
                                       placeholder="Enter your store name"
                                       class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                <p class="mt-1.5 text-[11px] text-[#37474F]">Displayed as "Shared from [Store Name]"</p>
                            </div>
                        </div>

                        {{-- SEO --}}
                        <div class="bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5" style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <div class="flex items-start justify-between mb-4 pb-3 border-b border-[#E0E0E0]">
                                <div>
                                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">
                                        <i class="fa-solid fa-magnifying-glass text-[#D4AF37] mr-2"></i>Search Engine Optimisation
                                    </h2>
                                    <p class="text-[11px] text-[#37474F] mt-0.5">Optimise how your product appears in search engines</p>
                                </div>
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Pro</span>
                            </div>

                            {{-- Why SEO matters --}}
                            <div class="mb-5 p-3 border-l-[3px] border-[#D4AF37] bg-[#FAFAF8] rounded-r-[4px]">
                                <p class="text-[12px] font-semibold text-[#1A1A1A] mb-1">Why does SEO matter for your listing?</p>
                                <p class="text-[12px] text-[#37474F] leading-relaxed">The <strong>Meta Title</strong> appears as the clickable headline on Google, and the <strong>Meta Description</strong> appears as the short text below it. Well-written, keyword-rich fields increase visibility and click-through rate — bringing buyers directly to your page without paid advertising.</p>
                            </div>

                            {{-- Search result preview --}}
                            <div class="mb-5 border border-[#E0E0E0] rounded-[6px] overflow-hidden">
                                <div class="px-3 py-2 bg-[#F5F5F5] text-[11px] text-[#37474F] font-semibold uppercase tracking-wider">
                                    Search result preview
                                </div>
                                <div class="p-4 bg-white">
                                    <p class="text-blue-600 text-[14px] font-medium">Your Meta Title appears here as the clickable headline</p>
                                    <p class="text-green-700 text-[11px] mt-0.5">https://{{ parse_url(config('app.url'), PHP_URL_HOST) }}/listing/your-product-slug</p>
                                    <p class="text-[#37474F] text-[13px] mt-1">Your Meta Description appears here — a short summary that convinces the user to click through.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div x-data="{ charCount: {{ strlen(old('meta_title', '')) }} }">
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">
                                        Meta Title
                                        <span class="ml-1 normal-case font-normal" :class="charCount > 60 ? 'text-red-500' : 'text-[#37474F]'">
                                            (<span x-text="charCount"></span>/60)
                                        </span>
                                    </label>
                                    <p class="text-[11px] text-[#37474F] mb-1.5">Aim for 50–60 characters. E.g. <em>"Premium Cotton T-Shirts Wholesale | YourBrand"</em></p>
                                    <input type="text" name="meta_title" maxlength="60"
                                           value="{{ old('meta_title') }}"
                                           x-on:input="charCount = $event.target.value.length"
                                           placeholder="e.g., Premium Cotton T-Shirts Wholesale | Your Brand"
                                           class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                                    @error('meta_title')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div x-data="{ charCount: {{ strlen(old('meta_description', '')) }} }">
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">
                                        Meta Description
                                        <span class="ml-1 normal-case font-normal" :class="charCount > 160 ? 'text-red-500' : (charCount > 120 ? 'text-yellow-500' : 'text-[#37474F]')">
                                            (<span x-text="charCount"></span>/160)
                                        </span>
                                    </label>
                                    <p class="text-[11px] text-[#37474F] mb-1.5">120–160 characters. E.g. <em>"Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping."</em></p>
                                    <textarea name="meta_description" rows="3" maxlength="160"
                                              x-on:input="charCount = $event.target.value.length"
                                              placeholder="e.g., Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping. Free sample on orders over $200."
                                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 resize-none placeholder-[#37474F]">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ── Right Column: Variant Attributes ── --}}
                    <div class="space-y-5">
                        <div id="nonMainShownVariantsContainer"
                             class="hidden bg-[#FFFFFF] border border-[#E0E0E0] rounded-[8px] p-5 space-y-4"
                             style="box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                        </div>
                    </div>
                </div>

                {{-- Product Variations --}}
                <div class="mt-5">
                    @include('user.listings.partials.variation-manager', ['mode' => 'create'])
                </div>

                {{-- Footer actions --}}
                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('business.listings.index') }}"
                       class="inline-flex items-center justify-center h-[40px] px-6 border border-[#E0E0E0] text-[#37474F] text-[13px] font-medium rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 h-[40px] px-6 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        Submit Listing
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const categoryContainer = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');

    let categoryLevelsData = {};

    fetch('/api/locations/countries')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                data.countries.forEach(country => {
                    const opt = document.createElement('option');
                    opt.value = country.id;
                    opt.textContent = country.name;
                    countrySelect.appendChild(opt);
                });
            }
        });

    countrySelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
        if (this.value) {
            fetch(`/api/locations/${this.value}/cities`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.cities.length > 0) {
                        citySelect.disabled = false;
                        data.cities.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.textContent = city.name;
                            citySelect.appendChild(opt);
                        });
                    }
                });
        }
    });

    const pendingCategoryFetches = {};

    loadCategoriesForLevel(0, null);

    categoryContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('category-select')) {
            const level = parseInt(e.target.dataset.level);
            const categoryId = e.target.value;
            const categoryName = e.target.options[e.target.selectedIndex].text;

            removeSelectsAfterLevel(level);

            Object.keys(categoryLevelsData).forEach(l => {
                if (parseInt(l) > level) { delete categoryLevelsData[l]; }
            });

            const container = document.getElementById('nonMainShownVariantsContainer');
            if (container) {
                container.innerHTML = '';
                container.classList.add('hidden');
            }

            if (categoryId) {
                categoryHiddenInput.value = categoryId;
                categoryLevelsData[level] = { id: categoryId, name: categoryName };
                loadCategoriesForLevel(level + 1, categoryId);

                const chainIds = [];
                for (let l = 0; l <= level; l++) {
                    if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); }
                }
                loadVariantsForChain(chainIds);

                window.dispatchEvent(new CustomEvent('category-changed', {
                    detail: { categoryId, categoryName, level }
                }));
            } else {
                categoryHiddenInput.value = '';
                delete categoryLevelsData[level];

                const chainIds = [];
                for (let l = 0; l < level; l++) {
                    if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); }
                }
                if (chainIds.length > 0) { loadVariantsForChain(chainIds); }
            }
        }
    });

    const selectClass = 'w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 category-select';

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

    function loadVariantsForChain(categoryIds) {
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
                    data.variants.forEach(v => {
                        if (!seen.has(v.id)) { seen.add(v.id); merged.push(v); }
                    });
                }
            });
            renderNonMainShownVariants(merged, {});
        });
    }

    function renderNonMainShownVariants(variants, savedValues) {
        const container = document.getElementById('nonMainShownVariantsContainer');
        if (!container) { return; }
        const nonMain = variants.filter(v => !v.is_main_shown);
        if (nonMain.length === 0) { container.classList.add('hidden'); return; }
        container.classList.remove('hidden');
        container.innerHTML = '<h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-1">Product Attributes</h3><p class="text-[11px] text-[#37474F] mb-3">Additional characteristics specific to this product.</p>';
        const inputClass = 'w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3';
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
                html += `<input type="text" name="variant_attributes[${variant.id}]" placeholder="${variant.name}" class="${inputClass}">`;
            }
            div.innerHTML = html;
            container.appendChild(div);
        });
    }

    const hasDeliveryCheckbox = document.getElementById('has_delivery');
    const deliveryFields = document.getElementById('deliveryFields');
    if (hasDeliveryCheckbox) {
        hasDeliveryCheckbox.addEventListener('change', function() {
            deliveryFields.classList.toggle('hidden', !this.checked);
        });
    }
    </script>
    @endpush
</x-app-layout>
