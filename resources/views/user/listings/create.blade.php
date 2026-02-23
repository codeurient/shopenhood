<x-app-layout>
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
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Base Price <span class="text-red-500">*</span></label>
                                        <input type="number" name="base_price" step="0.01" min="0.01" required
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

                    </div>

                    {{-- Right Column: Variant Selectors + Product Images --}}
                    <div class="space-y-6">
                        <div id="variantsContainer" class="space-y-4"></div>

                        {{-- Product Images --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Product Images</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">The first image will be used as the main image in listings. (max 10)</p>

                            <input type="file" name="product_images[]" id="productImages" multiple
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="w-full px-4 py-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg">

                            <div id="productImagesPreview" class="mt-4 grid grid-cols-2 gap-2"></div>

                            <div id="productImagesContainer" class="mt-4 space-y-2 hidden">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Images:</span>
                                    <button type="button" id="clearAllProductImages"
                                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
    const categoryContainer = document.getElementById('categorySelectsContainer');
    const categoryHiddenInput = document.getElementById('category_id_hidden');
    const variantsContainer = document.getElementById('variantsContainer');
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');

    let categoryLevelsData = {};
    let loadedVariantsByCategory = new Set();

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

    // Product images preview
    const productImagesEl = document.getElementById('productImages');
    if (productImagesEl) {
        productImagesEl.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const previewContainer = document.getElementById('productImagesPreview');
            const imagesContainer = document.getElementById('productImagesContainer');

            previewContainer.innerHTML = '';

            if (files.length > 0) {
                imagesContainer.classList.remove('hidden');
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        const badge = index === 0 ? '<span class="absolute bottom-1 left-1 text-xs bg-black/60 text-white px-1 rounded">Main</span>' : '';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                            ${badge}
                            <button type="button"
                                    class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition"
                                    onclick="removeProductImage(${index})">
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
    }

    const clearAllProductImagesEl = document.getElementById('clearAllProductImages');
    if (clearAllProductImagesEl) {
        clearAllProductImagesEl.addEventListener('click', function() {
            document.getElementById('productImages').value = '';
            document.getElementById('productImagesPreview').innerHTML = '';
            document.getElementById('productImagesContainer').classList.add('hidden');
        });
    }

    function removeProductImage(index) {
        const input = document.getElementById('productImages');
        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
            if (i !== index) { dt.items.add(file); }
        });
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    const pendingCategoryFetches = {};

    loadCategoriesForLevel(0, null);

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
        card.className = 'bg-white dark:bg-gray-800 rounded-lg shadow p-6 variant-card';
        card.dataset.level = level;
        card.dataset.categoryId = categoryId;

        const inputClass = 'w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500';
        let levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));

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
            html += `<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${variant.name}${variant.is_required ? ' <span class="text-red-500">*</span>' : ''}</label>`;

            if (variant.type === 'select' || (variant.items && variant.items.length > 0)) {
                html += `<select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="${inputClass}"><option value="">Select ${variant.name}</option>`;
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
    const discountFieldsEl = document.getElementById('discountFields');
    if (hasDiscountCheckbox && discountFieldsEl) {
        hasDiscountCheckbox.addEventListener('change', function() {
            if (this.checked) {
                discountFieldsEl.classList.remove('hidden');
            } else {
                discountFieldsEl.classList.add('hidden');
                const dp = document.getElementById('discount_price');
                const ds = document.getElementById('discount_start_date');
                const de = document.getElementById('discount_end_date');
                if (dp) { dp.value = ''; }
                if (ds) { ds.value = ''; }
                if (de) { de.value = ''; }
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
