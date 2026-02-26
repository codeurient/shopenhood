<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Business Listing</h2>
                <a href="{{ route('business.listings.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-sm">
                    &larr; Back to Business Listings
                </a>
            </div>

            {{-- Status Banner --}}
            <div class="mb-6 p-4 rounded-lg text-sm
                {{ $listing->status === 'active' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700' : '' }}
                {{ $listing->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700' : '' }}
                {{ $listing->status === 'rejected' ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700' : '' }}
                {{ !in_array($listing->status, ['active', 'pending', 'rejected']) ? 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600' : '' }}">
                <strong>Status:</strong> {{ ucfirst($listing->status) }}
                @if($listing->status === 'rejected' && $listing->rejection_reason)
                    <br><strong>Reason:</strong> {{ $listing->rejection_reason }}
                @endif
                @if($listing->status !== 'rejected')
                    <span class="text-xs opacity-75"> — Updating will resubmit for review.</span>
                @endif
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

            <form action="{{ route('business.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" id="listingForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column --}}
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
                                        @foreach($listingTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('listing_type_id', $listing->listing_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->icon }} {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('listing_type_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
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
                                    <input type="hidden" name="category_id" id="category_id_hidden" value="{{ old('category_id', $listing->category_id) }}" required>
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
                                           value="{{ old('title', $listing->title) }}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Short Description --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Short Description</label>
                                    <textarea name="short_description" rows="2" maxlength="500"
                                              placeholder="Brief summary for preview (optional)"
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('short_description', $listing->short_description) }}</textarea>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Short summary shown in listings (max 500 characters)</p>
                                </div>

                                {{-- Full Description --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="description" rows="6" required
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description', $listing->description) }}</textarea>
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
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                                        <select name="currency"
                                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                            <option value="USD" {{ old('currency', $listing->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency', $listing->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency', $listing->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Prices are set per variation in the product variations section below.</p>

                                {{-- Product Condition --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Product Condition <span class="text-red-500">*</span></label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="new"
                                                   {{ old('condition', $listing->condition ?? 'new') === 'new' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">New</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="used"
                                                   {{ old('condition', $listing->condition) === 'used' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Second-hand</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Product Availability --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Product Availability <span class="text-red-500">*</span></label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="availability_type" value="in_stock"
                                                   {{ old('availability_type', $listing->availability_type ?? 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Stock</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="availability_type" value="available_by_order"
                                                   {{ old('availability_type', $listing->availability_type) === 'available_by_order' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Available by Order</span>
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Select "Available by Order" if the item is not currently in stock but can be supplied upon request.</p>
                                </div>

                                {{-- Store Type --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Store Type</label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="has_store" value="0"
                                                   {{ old('has_store', $listing->has_store ? '1' : '0') === '0' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No Store</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="has_store" value="1"
                                                   {{ old('has_store', $listing->has_store ? '1' : '0') === '1' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Store</span>
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Select "Store" if this product is sold from a physical store location.</p>
                                </div>

                                {{-- Delivery Options --}}
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <div class="flex items-center mb-3">
                                        <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                               {{ old('has_delivery', $listing->has_delivery) ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                        <label for="has_delivery" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Available</label>
                                    </div>
                                    <div id="deliveryFields" class="{{ old('has_delivery', $listing->has_delivery) ? '' : 'hidden' }} space-y-3 ml-6">
                                        <div class="flex items-start gap-4">
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="has_domestic_delivery" value="1"
                                                       {{ old('has_domestic_delivery', $listing->has_domestic_delivery) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Domestic Delivery</span>
                                            </label>
                                            <div>
                                                <input type="number" name="domestic_delivery_price" step="0.01" min="0"
                                                       value="{{ old('domestic_delivery_price', $listing->domestic_delivery_price) }}" placeholder="Price"
                                                       class="w-40 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-4">
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="has_international_delivery" value="1"
                                                       {{ old('has_international_delivery', $listing->has_international_delivery) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-primary-600 rounded border-gray-300 dark:border-gray-600 focus:ring-primary-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">International Delivery</span>
                                            </label>
                                            <div>
                                                <input type="number" name="international_delivery_price" step="0.01" min="0"
                                                       value="{{ old('international_delivery_price', $listing->international_delivery_price) }}" placeholder="Price"
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

                        {{-- Store Information --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Store Information</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Store Name</label>
                                <input type="text" name="store_name" value="{{ old('store_name', $listing->store_name) }}" maxlength="255"
                                       placeholder="Enter your store name"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Displayed as "Shared from [Store Name]"</p>
                            </div>
                        </div>

                        {{-- SEO Settings --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Search Engine Optimization (SEO)</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Optimize how your product appears in search engines</p>
                                </div>
                                <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded">Pro Feature</span>
                            </div>

                            {{-- Why SEO matters --}}
                            <div class="mb-5 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Why does SEO matter for your listing?</p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">When someone searches Google for your product, the <strong>Meta Title</strong> appears as the clickable blue headline and the <strong>Meta Description</strong> appears as the short text snippet below it. Well-written, keyword-rich fields increase your listing's visibility and click-through rate — bringing more potential buyers directly to your page without paid advertising.</p>
                            </div>

                            {{-- Search result preview mock --}}
                            <div class="mb-5 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <div class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">How your listing looks in search results</div>
                                <div class="p-4 bg-white dark:bg-gray-900">
                                    <p class="text-blue-600 dark:text-blue-400 text-base font-medium">Your Meta Title appears here as the clickable headline</p>
                                    <p class="text-green-700 dark:text-green-500 text-xs mt-0.5">https://{{ parse_url(config('app.url'), PHP_URL_HOST) }}/listing/your-product-slug</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Your Meta Description appears here — a short summary that convinces the user to click through to your listing.</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div x-data="{ charCount: {{ strlen(old('meta_title', $listing->meta_title ?? '')) }} }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Meta Title
                                        <span class="ml-2 text-xs font-normal" :class="charCount > 60 ? 'text-red-500' : 'text-gray-400'">
                                            (<span x-text="charCount"></span>/60)
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Aim for 50–60 characters. Place your most important keyword near the start. Example: <em>"Premium Cotton T-Shirts Wholesale | YourBrand"</em></p>
                                    <input type="text" name="meta_title" maxlength="60"
                                           value="{{ old('meta_title', $listing->meta_title) }}"
                                           x-on:input="charCount = $event.target.value.length"
                                           placeholder="e.g., Premium Cotton T-Shirts Wholesale | Your Brand"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    @error('meta_title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div x-data="{ charCount: {{ strlen(old('meta_description', $listing->meta_description ?? '')) }} }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Meta Description
                                        <span class="ml-2 text-xs font-normal" :class="charCount > 160 ? 'text-red-500' : (charCount > 120 ? 'text-yellow-500' : 'text-gray-400')">
                                            (<span x-text="charCount"></span>/160)
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Use 120–160 characters. Highlight a key benefit or call-to-action. Example: <em>"Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping. Free sample on orders over $200."</em></p>
                                    <textarea name="meta_description" rows="3" maxlength="160"
                                              x-on:input="charCount = $event.target.value.length"
                                              placeholder="e.g., Shop bulk cotton t-shirts. MOQ 10 pcs. Fast shipping. Free sample on orders over $200."
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $listing->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column: Non-main-shown variant attributes --}}
                    <div class="space-y-6">
                        <div id="nonMainShownVariantsContainer" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4"></div>
                    </div>
                </div>

                {{-- Product Variations --}}
                <div class="mt-6">
                    @include('user.listings.partials.variation-manager', ['mode' => 'edit', 'listing' => $listing])
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('business.listings.index') }}"
                       class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $variationsData = $listing->variations->map(function ($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'attributes' => $v->attributes->mapWithKeys(function ($attr) {
                    return [$attr->variant_id => $attr->variant_item_id];
                })->toArray(),
                'price' => (float) $v->price,
                'discount_price' => $v->discount_price ? (float) $v->discount_price : null,
                'discount_start_date' => $v->discount_start_date ? $v->discount_start_date->format('Y-m-d') : '',
                'discount_end_date' => $v->discount_end_date ? $v->discount_end_date->format('Y-m-d') : '',
                'stock_quantity' => $v->stock_quantity,
                'low_stock_threshold' => $v->low_stock_threshold,
                'manage_stock' => $v->manage_stock,
                'allow_backorder' => $v->allow_backorder,
                'is_default' => $v->is_default,
                'is_active' => $v->is_active,
                'is_wholesale' => (bool) $v->is_wholesale,
                'wholesale_min_order_qty' => $v->wholesale_min_order_qty,
                'wholesale_qty_increment' => $v->wholesale_qty_increment ?? 1,
                'wholesale_lead_time_days' => $v->wholesale_lead_time_days,
                'wholesale_sample_available' => (bool) $v->wholesale_sample_available,
                'wholesale_sample_price' => $v->wholesale_sample_price ? (float) $v->wholesale_sample_price : null,
                'wholesale_terms' => $v->wholesale_terms ?? '',
                'images' => [],
                'existing_images' => $v->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => asset('storage/'.$img->image_path),
                ])->values()->toArray(),
                'deleted_image_ids' => [],
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
                    if (preselectedCountryId && country.id === preselectedCountryId) {
                        opt.selected = true;
                    }
                    countrySelect.appendChild(opt);
                });

                if (preselectedCountryId) {
                    loadCitiesForCountry(preselectedCountryId, preselectedLocationId);
                }
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
                        if (selectCityId && city.id === selectCityId) {
                            opt.selected = true;
                        }
                        citySelect.appendChild(opt);
                    });
                }
            });
    }

    countrySelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
        if (this.value) {
            loadCitiesForCountry(this.value);
        }
    });

    const selectClass = 'w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select';

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
                    data.variants.forEach(v => {
                        if (!seen.has(v.id)) { seen.add(v.id); merged.push(v); }
                    });
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
        container.innerHTML = '<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Product Attributes</h3><p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Additional characteristics specific to this product.</p>';
        const inputClass = 'w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500';
        nonMain.forEach(variant => {
            const savedItemId = savedValues ? savedValues[variant.id] : null;
            const div = document.createElement('div');
            let html = `<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">${variant.name}</label>`;
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
            container.appendChild(div);
        });
    }

    categoryContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('category-select')) {
            const level = parseInt(e.target.dataset.level);
            const categoryId = e.target.value;
            const categoryName = e.target.options[e.target.selectedIndex].text;

            removeSelectsAfterLevel(level);

            // Clear stale deeper-level data
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

                // Load variants for the full chain from root to current level
                const chainIds = [];
                for (let l = 0; l <= level; l++) {
                    if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); }
                }
                loadVariantsForChain(chainIds, {});

                window.dispatchEvent(new CustomEvent('category-changed', {
                    detail: { categoryId, categoryName, level }
                }));
            } else {
                categoryHiddenInput.value = '';
                delete categoryLevelsData[level];

                // Reload variants for remaining parent categories
                const chainIds = [];
                for (let l = 0; l < level; l++) {
                    if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); }
                }
                if (chainIds.length > 0) { loadVariantsForChain(chainIds, {}); }
            }
        }
    });

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

    // Initialize category dropdowns — exactly one fetch path runs, never both at once
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
            if (data.success && data.hierarchy) {
                loadCategoryLevel(data.hierarchy, 0);
            }
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
                sel.className = selectClass + (level > 0 ? ' mt-3' : '');
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

                window.dispatchEvent(new CustomEvent('category-changed', {
                    detail: { categoryId: item.id, categoryName: item.name, level }
                }));

                // Load variants once the full chain is built (at the leaf level)
                if (index + 1 >= hierarchy.length) {
                    const chainIds = [];
                    for (let l = 0; l <= level; l++) {
                        if (categoryLevelsData[l]) { chainIds.push(categoryLevelsData[l].id); }
                    }
                    loadVariantsForChain(chainIds, window.EDIT_VARIANT_ATTRIBUTES || {});
                }

                loadCategoryLevel(hierarchy, index + 1);
            }
        });
    }
    </script>
    @endpush
</x-app-layout>
