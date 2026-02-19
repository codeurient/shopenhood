<x-app-layout>
    {{-- <x-slot name="header">

    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Listing</h2>
                <a href="{{ route('user.listings.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-sm">
                    &larr; Back to My Listings
                </a>
            </div>


            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif



            <form action="{{ route('user.listings.store') }}" method="POST" enctype="multipart/form-data" id="listingForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column: Main Content --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Basic Information --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>

                            <div class="space-y-4">
                                {{-- Listing Type --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Listing Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="listing_type_id" id="listing_type_id" required
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Type</option>
                                        @foreach($listingTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('listing_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->icon }} {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Category (Hierarchical) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <div id="categorySelectsContainer">
                                        <select id="category_level_0" required
                                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select"
                                                data-level="0">
                                            <option value="">Select Category</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="category_id" id="category_id_hidden" required>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Title --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" required maxlength="255"
                                           value="{{ old('title') }}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Short Description --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Short Description
                                    </label>
                                    <textarea name="short_description" rows="2" maxlength="500"
                                              placeholder="Brief summary for preview (optional)"
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('short_description') }}</textarea>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Short summary shown in listings (max 500 characters)</p>
                                </div>

                                {{-- Full Description --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description" rows="6" required
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pricing</h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Base Price</label>
                                        <input type="number" name="base_price" step="0.01" min="0"
                                               value="{{ old('base_price') }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        @error('base_price')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                                        <select name="currency"
                                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                            <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="is_negotiable" id="is_negotiable" value="1"
                                           {{ old('is_negotiable') ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    <label for="is_negotiable" class="text-sm text-gray-700 dark:text-gray-300">Price is negotiable</label>
                                </div>

                                {{-- Discount Pricing --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <div class="flex items-center mb-3">
                                        <input type="checkbox" id="has_discount" class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                        <label for="has_discount" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Apply Discount</label>
                                    </div>

                                    <div id="discountFields" class="hidden space-y-4">
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discount Price</label>
                                                <input type="number" name="discount_price" id="discount_price" step="0.01" min="0"
                                                       value="{{ old('discount_price') }}"
                                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                                                <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                                       value="{{ old('discount_start_date') }}"
                                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                                                <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                                       value="{{ old('discount_end_date') }}"
                                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">The discount price will be shown with a strikethrough on the original price during the specified period.</p>
                                    </div>
                                </div>

                                {{-- Product Condition --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Product Condition <span class="text-red-500">*</span></label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="new"
                                                   {{ old('condition', 'new') === 'new' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">New</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="used"
                                                   {{ old('condition') === 'used' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Second-hand</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Product Availability (Business Users Only) --}}
                                @if(auth()->user()->isBusinessUser())
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Product Availability</label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="availability_type" value="in_stock"
                                                   {{ old('availability_type', 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Stock</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="availability_type" value="available_by_order"
                                                   {{ old('availability_type') === 'available_by_order' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Available by Order</span>
                                        </label>
                                    </div>
                                </div>
                                @endif

                                {{-- Delivery Options --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <div class="flex items-center mb-3">
                                        <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                               {{ old('has_delivery') ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                        <label for="has_delivery" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Available</label>
                                    </div>

                                    <div id="deliveryFields" class="{{ old('has_delivery') ? '' : 'hidden' }} space-y-3 ml-6">
                                        <div class="flex items-start gap-4">
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="has_domestic_delivery" value="1"
                                                       {{ old('has_domestic_delivery') ? 'checked' : '' }}
                                                       class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Domestic Delivery</span>
                                            </label>
                                            <div>
                                                <input type="number" name="domestic_delivery_price" step="0.01" min="0"
                                                       value="{{ old('domestic_delivery_price') }}" placeholder="Price"
                                                       class="w-40 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-4">
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="has_international_delivery" value="1"
                                                       {{ old('has_international_delivery') ? 'checked' : '' }}
                                                       class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">International Delivery</span>
                                            </label>
                                            <div>
                                                <input type="number" name="international_delivery_price" step="0.01" min="0"
                                                       value="{{ old('international_delivery_price') }}" placeholder="Price"
                                                       class="w-40 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Location</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                                    <select id="country_select"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                                    <select name="location_id" id="city_select" disabled
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select country first</option>
                                    </select>
                                    @error('location_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Store Name (Business Users Only) --}}
                        @if(auth()->user()->isBusinessUser())
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Store Information</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Store Name</label>
                                <input type="text" name="store_name" value="{{ old('store_name') }}" maxlength="255"
                                       placeholder="Enter your store name"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This will be displayed as "Shared from [Store Name]"</p>
                            </div>
                        </div>
                        @endif

                        {{-- Wholesale Settings (Business Users Only) --}}
                        @if(auth()->user()->isBusinessUser())
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="{ isWholesale: {{ old('is_wholesale') ? 'true' : 'false' }}, sampleAvailable: {{ old('wholesale_sample_available') ? 'true' : 'false' }} }">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Wholesale Settings</h3>
                                <label class="flex items-center">
                                    <input type="hidden" name="is_wholesale" :value="isWholesale ? '1' : '0'">
                                    <input type="checkbox" x-model="isWholesale"
                                           class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Wholesale</span>
                                </label>
                            </div>

                            <div x-show="isWholesale" x-collapse class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Minimum Order Quantity (MOQ) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="wholesale_min_order_qty" min="1"
                                               value="{{ old('wholesale_min_order_qty') }}"
                                               placeholder="e.g., 10"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Quantity Increment
                                        </label>
                                        <input type="number" name="wholesale_qty_increment" min="1"
                                               value="{{ old('wholesale_qty_increment') }}"
                                               placeholder="e.g., 5 (order in multiples)"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Lead Time (days)
                                    </label>
                                    <input type="number" name="wholesale_lead_time_days" min="0" max="365"
                                           value="{{ old('wholesale_lead_time_days') }}"
                                           placeholder="Production/fulfillment time"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="flex items-center mb-2">
                                        <input type="hidden" name="wholesale_sample_available" :value="sampleAvailable ? '1' : '0'">
                                        <input type="checkbox" x-model="sampleAvailable"
                                               class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Samples Available</span>
                                    </label>
                                    <div x-show="sampleAvailable" class="ml-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sample Price (per unit)</label>
                                        <input type="number" name="wholesale_sample_price" step="0.01" min="0"
                                               value="{{ old('wholesale_sample_price') }}"
                                               placeholder="0.00"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Wholesale Terms & Conditions
                                    </label>
                                    <textarea name="wholesale_terms" rows="3" maxlength="2000"
                                              placeholder="Payment terms, shipping policy, bulk order requirements..."
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('wholesale_terms') }}</textarea>
                                </div>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Volume pricing tiers can be set for each product variation in the variations section below.
                                </p>
                            </div>
                        </div>
                        @endif

                        {{-- SEO Settings (Business Users Only) --}}
                        @if(auth()->user()->isBusinessUser())
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Search Engine Optimization (SEO)</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Optimize how your product appears in Google and other search engines</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded">Pro Feature</span>
                            </div>

                            <div class="space-y-5">
                                {{-- Meta Title --}}
                                <div x-data="{ charCount: {{ strlen(old('meta_title', '')) }} }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Meta Title
                                        <span class="ml-2 text-xs font-normal" :class="charCount > 60 ? 'text-red-500' : 'text-gray-400'">
                                            (<span x-text="charCount"></span>/60)
                                        </span>
                                    </label>
                                    <input type="text" name="meta_title" maxlength="60"
                                           value="{{ old('meta_title') }}"
                                           x-on:input="charCount = $event.target.value.length"
                                           placeholder="e.g., Premium Cotton T-Shirts Wholesale | Your Brand"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-xs text-gray-600 dark:text-gray-400">
                                        <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Tips for a great meta title:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Keep it under 60 characters to avoid truncation in search results</li>
                                            <li>Include your main keyword near the beginning</li>
                                            <li>Use a pipe <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">|</code> or hyphen <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">-</code> to separate brand name</li>
                                            <li>Make it compelling and unique - this is what users click on!</li>
                                        </ul>
                                        <p class="mt-2 text-green-600 dark:text-green-400">Example: "Organic Cotton T-Shirts Bulk | Free Shipping Over $500"</p>
                                    </div>
                                </div>

                                {{-- Meta Description --}}
                                <div x-data="{ charCount: {{ strlen(old('meta_description', '')) }} }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Meta Description
                                        <span class="ml-2 text-xs font-normal" :class="charCount > 160 ? 'text-red-500' : (charCount > 120 ? 'text-yellow-500' : 'text-gray-400')">
                                            (<span x-text="charCount"></span>/160)
                                        </span>
                                    </label>
                                    <textarea name="meta_description" rows="3" maxlength="160"
                                              x-on:input="charCount = $event.target.value.length"
                                              placeholder="Write a compelling description that encourages users to click..."
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description') }}</textarea>
                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-xs text-gray-600 dark:text-gray-400">
                                        <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Tips for a great meta description:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Aim for 120-155 characters (mobile-friendly length)</li>
                                            <li>Include a call-to-action: "Shop now", "Order today", "Free shipping"</li>
                                            <li>Mention key benefits: price, quality, delivery speed</li>
                                            <li>Use numbers for impact: "50+ colors", "Save 20%"</li>
                                        </ul>
                                        <p class="mt-2 text-green-600 dark:text-green-400">Example: "Shop premium cotton t-shirts in bulk. MOQ 10 pieces. Free shipping on orders over $500. 50+ colors available. Order now!"</p>
                                    </div>
                                </div>

                                {{-- Important Notice --}}
                                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-amber-800 dark:text-amber-300">
                                            <p class="font-medium mb-1">Important: Write Original Content</p>
                                            <p>Please write all text in your own words with correct grammar and spelling. Do not copy content from other websites or use AI-generated text. Original, human-written content performs significantly better in search engines and builds more trust with customers.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Main Image --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Main Image</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">This image will be shown as the listing thumbnail</p>

                            <input type="file" name="main_image" id="mainImage" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="w-full px-4 py-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg">
                            <div id="mainImagePreview" class="mt-4 hidden">
                                <img src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                            </div>
                            @error('main_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Right Column: Variants & Detail Images --}}
                    <div class="space-y-6">
                        {{-- Dynamic Variants Container --}}
                        <div id="variantsContainer" class="space-y-4">
                            {{-- Variant cards will be dynamically inserted here --}}
                        </div>

                        {{-- Product Detail Images --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" style="margin-top: 0px">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Detail Images</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Additional images shown on the product details page (max 10)</p>

                            <input type="file" name="detail_images[]" id="detailImages" multiple
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="w-full px-4 py-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg">

                            <div id="detailImagesPreview" class="mt-4 grid grid-cols-2 gap-2"></div>

                            <div id="detailImagesContainer" class="mt-4 space-y-2 hidden">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Images:</span>
                                    <button type="button" id="clearAllDetailImages"
                                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Variations Section (Business Users Only) --}}
                @if(auth()->user()->isBusinessUser())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Default Product</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Choose which product is shown by default on the listing page.
                    </p>
                    <div class="flex items-center gap-3 flex-wrap">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio"
                                   id="default_basic"
                                   name="default_variation"
                                   value="basic"
                                   checked
                                   onchange="window.dispatchEvent(new CustomEvent('basic-default-selected'))"
                                   class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Basic Information</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">(base price from Pricing section)</span>
                        </label>
                        <span class="text-xs text-gray-400 dark:text-gray-500">— or select a variation as default in the table below</span>
                    </div>
                </div>
                <div class="mt-6">
                    @include('user.listings.partials.variation-manager', ['mode' => 'create'])
                </div>
                @endif

                {{-- Footer --}}
                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('user.listings.index') }}"
                       class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                        Submit Listing
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    // Global variables
    const categoryContainer = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const variantsContainer = document.getElementById('variantsContainer');
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');

    let categoryLevelsData = {};
    let loadedVariantsByCategory = new Set();

    // Load countries from database
    fetch('/api/locations/countries')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.countries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.id;
                    option.textContent = country.name;
                    countrySelect.appendChild(option);
                });
            }
        });

    // Country change handler - load cities
    countrySelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;

        if (this.value) {
            fetch(`/api/locations/${this.value}/cities`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.cities.length > 0) {
                        citySelect.disabled = false;
                        data.cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    }
                });
        }
    });

    // Main image preview
    document.getElementById('mainImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('mainImagePreview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Detail images preview
    document.getElementById('detailImages').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const previewContainer = document.getElementById('detailImagesPreview');
        const imagesContainer = document.getElementById('detailImagesContainer');

        previewContainer.innerHTML = '';

        if (files.length > 0) {
            imagesContainer.classList.remove('hidden');

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                        <button type="button"
                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition"
                                onclick="removeDetailImage(${index})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            imagesContainer.classList.add('hidden');
        }
    });

    // Clear all detail images
    document.getElementById('clearAllDetailImages').addEventListener('click', function() {
        document.getElementById('detailImages').value = '';
        document.getElementById('detailImagesPreview').innerHTML = '';
        document.getElementById('detailImagesContainer').classList.add('hidden');
    });

    // Remove individual detail image
    function removeDetailImage(index) {
        const input = document.getElementById('detailImages');
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    // Load root categories on page load
    loadCategoriesForLevel(0, null);

    // Category selection handler
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

                // Emit event for variation manager
                window.dispatchEvent(new CustomEvent('category-changed', {
                    detail: { categoryId, categoryName, level }
                }));
            } else {
                categoryHiddenInput.value = '';
                delete categoryLevelsData[level];
            }
        }
    });

    const selectClass = 'w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select';

    function loadCategoriesForLevel(level, parentId) {
        const url = parentId
            ? `/api/categories/children/${parentId}`
            : '/api/categories/children';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.categories.length > 0) {
                const existing = document.getElementById('category_level_' + level);
                if (existing) existing.remove();

                const sel = document.createElement('select');
                sel.id = 'category_level_' + level;
                sel.className = selectClass + (level > 0 ? ' mt-3' : '');
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
        });
    }

    function removeSelectsAfterLevel(level) {
        categoryContainer.querySelectorAll('.category-select').forEach(select => {
            if (parseInt(select.dataset.level) > level) select.remove();
        });
    }

    function loadVariantsForCategory(categoryId, categoryName, level) {
        if (loadedVariantsByCategory.has(categoryId)) return;

        // Delay showing the loading spinner to prevent flash when no variants exist
        let loadingCard = null;
        let fetchComplete = false;
        const loadingTimeout = setTimeout(() => {
            if (!fetchComplete) {
                loadingCard = createLoadingCard(level);
                variantsContainer.appendChild(loadingCard);
            }
        }, 150);

        fetch(`/api/categories/${categoryId}/variants?show_all=true`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            fetchComplete = true;
            clearTimeout(loadingTimeout);
            if (loadingCard) loadingCard.remove();
            if (data.success && data.variants.length > 0) {
                loadedVariantsByCategory.add(categoryId);
                renderVariantsCard(data.variants, categoryName, level, categoryId);
            }
        })
        .catch(err => {
            fetchComplete = true;
            clearTimeout(loadingTimeout);
            if (loadingCard) loadingCard.remove();
        });
    }

    function createLoadingCard(level) {
        const card = document.createElement('div');
        card.className = 'bg-white dark:bg-gray-800 rounded-lg shadow p-6';
        card.dataset.level = level;
        card.innerHTML = `
            <div class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">Loading variants...</p>
            </div>
        `;
        return card;
    }

    function renderVariantsCard(variants, categoryName, level, categoryId) {
        const card = document.createElement('div');
        card.className = 'bg-white dark:bg-gray-800 rounded-lg shadow p-6 variant-card';
        card.dataset.level = level;
        card.dataset.categoryId = categoryId;

        let levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));
        const inputClass = 'w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500';

        let html = `
            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">${levelLabel} Attributes</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${categoryName}</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded">
                        ${variants.length} attribute${variants.length !== 1 ? 's' : ''}
                    </span>
                </div>
            </div>
            <div class="space-y-4">
        `;

        variants.forEach(variant => {
            html += `<div>`;
            html += `<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${variant.name} ${variant.is_required ? '<span class="text-red-500">*</span>' : ''}</label>`;

            if (variant.type === 'select') {
                html += `<select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="${inputClass}"><option value="">Select ${variant.name}</option>`;
                variant.items.forEach(item => {
                    html += `<option value="${item.id}">${item.display_value}</option>`;
                });
                html += `</select>`;
            } else if (variant.type === 'radio') {
                html += `<div class="space-y-2">`;
                variant.items.forEach((item, index) => {
                    html += `<label class="flex items-center"><input type="radio" name="variants[${variant.id}]" value="${item.id}" ${variant.is_required && index === 0 ? 'required' : ''} class="w-4 h-4 text-primary-600 focus:ring-primary-500"><span class="ml-2 text-sm text-gray-700 dark:text-gray-300">${item.display_value}</span></label>`;
                });
                html += `</div>`;
            } else if (variant.type === 'checkbox') {
                html += `<div class="space-y-2">`;
                variant.items.forEach(item => {
                    html += `<label class="flex items-center"><input type="checkbox" name="variants[${variant.id}][]" value="${item.id}" class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"><span class="ml-2 text-sm text-gray-700 dark:text-gray-300">${item.display_value}</span></label>`;
                });
                html += `</div>`;
            } else if (variant.type === 'text') {
                html += `<input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            } else if (variant.type === 'number') {
                html += `<input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} placeholder="${variant.placeholder || ''}" class="${inputClass}">`;
            } else if (variant.type === 'range') {
                html += `<input type="range" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer">`;
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
                const categoryId = card.dataset.categoryId;
                if (categoryId) loadedVariantsByCategory.delete(categoryId);
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
            if (this.checked) {
                deliveryFields.classList.remove('hidden');
            } else {
                deliveryFields.classList.add('hidden');
            }
        });
    }
    </script>
    @endpush
</x-app-layout>
