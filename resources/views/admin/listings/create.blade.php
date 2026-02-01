@extends('admin.layouts.app')

@section('title', 'Create Listing')
@section('page-title', 'Create Listing')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Create New Listing</h2>
        <a href="{{ route('admin.listings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Back to Listings
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.listings.store') }}" method="POST" enctype="multipart/form-data" id="listingForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Listing Type <span class="text-red-500">*</span>
                            </label>
                            <select name="listing_type_id" id="listing_type_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Type</option>
                                @foreach($listingTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('listing_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div id="categorySelectsContainer">
                                <select id="category_level_0" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 category-select"
                                        data-level="0">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <input type="hidden" name="category_id" id="category_id_hidden" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   value="{{ old('title') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">Auto-generated if left empty</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="2"
                                      placeholder="Brief summary for preview (optional)"
                                      maxlength="500"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('short_description') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Short summary shown in listings (max 500 characters)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="6" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Availability</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base Price</label>
                                <input type="number" name="base_price" id="base_price" step="0.01" min="0"
                                       value="{{ old('base_price') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select name="currency"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                            </div>
                        </div>

                        <!-- Discount Pricing -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="has_discount" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <label for="has_discount" class="ml-2 text-sm font-medium text-gray-700">Apply Discount</label>
                            </div>

                            <div id="discountFields" class="hidden space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Price</label>
                                        <input type="number" name="discount_price" id="discount_price" step="0.01" min="0"
                                               value="{{ old('discount_price') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                        <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                               value="{{ old('discount_start_date') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                        <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                               value="{{ old('discount_end_date') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">The discount price will be shown with a strikethrough on the original price during the specified period.</p>
                            </div>
                        </div>

                        <!-- Product Availability -->
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Product Availability <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center">
                                    <input type="radio" name="availability_type" value="in_stock"
                                           {{ old('availability_type', 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">In Stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="availability_type" value="available_by_order"
                                           {{ old('availability_type') === 'available_by_order' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Available by Order</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 border-t pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_negotiable" value="1" {{ old('is_negotiable') ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Price is negotiable</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Visible to public</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Featured</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select name="country" id="country"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Country</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <select name="city" id="city"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                        disabled>
                                    <option value="">Select city first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posted As</label>
                            <select name="created_as_role" id="created_as_role"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="admin" {{ old('created_as_role', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="normal_user" {{ old('created_as_role') === 'normal_user' ? 'selected' : '' }}>Normal User</option>
                                <option value="business_user" {{ old('created_as_role') === 'business_user' ? 'selected' : '' }}>Business User</option>
                            </select>
                        </div>

                        <div id="storeNameField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Store Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="store_name" id="store_name"
                                   value="{{ old('store_name') }}"
                                   placeholder="Enter store name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">This will be displayed as "Shared from [Store Name]"</p>
                        </div>
                    </div>
                </div>

                <!-- Main Image -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Main Image</h3>
                    <p class="text-sm text-gray-600 mb-4">This image will be displayed on the main listing page</p>
                    <input type="file" name="main_image" id="mainImage" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <div id="mainImagePreview" class="mt-4 hidden">
                        <img src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                    </div>
                </div>

                <!-- SEO -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" maxlength="60"
                                   value="{{ old('meta_title') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" rows="3" maxlength="160"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Variants, Status & Product Detail Images -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>

                <!-- Dynamic Variants Container -->
                <div id="variantsContainer" class="space-y-4">
                    <!-- Variant cards will be dynamically inserted here -->
                </div>

                <!-- Product Detail Images -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Detail Images</h3>
                    <p class="text-xs text-gray-500 mb-4">These images will be shown on the product details page when users view this listing</p>

                    <input type="file" name="detail_images[]" id="detailImages" multiple accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                    <div id="detailImagesPreview" class="mt-4 grid grid-cols-2 gap-2">
                        <!-- Preview images will appear here -->
                    </div>

                    <div id="detailImagesContainer" class="mt-4 space-y-2 hidden">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Selected Images:</span>
                            <button type="button" id="clearAllDetailImages"
                                    class="text-xs text-red-600 hover:text-red-800">
                                🗑️ Delete All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Variations Section -->
        <div class="mt-6">
            @include('admin.listings.partials.variation-manager', ['mode' => 'create'])
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.listings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                💾 Create Listing
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Global variables
const categoryContainer = document.getElementById('categorySelectsContainer');
const categoryHiddenInput = document.getElementById('category_id_hidden');
const variantsContainer = document.getElementById('variantsContainer');
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');
const countrySelect = document.getElementById('country');
const citySelect = document.getElementById('city');
const createdAsRoleSelect = document.getElementById('created_as_role');
const storeNameField = document.getElementById('storeNameField');
const hasDiscountCheckbox = document.getElementById('has_discount');
const discountFields = document.getElementById('discountFields');

let categoryLevelsData = {};
let loadedVariantsByCategory = new Set();
let countriesData = {};
let detailImagesFiles = [];

// Load countries and cities data
fetch('/json/countriesAndCities.json')
    .then(response => response.json())
    .then(data => {
        countriesData = data;
        populateCountries();
    });

function populateCountries() {
    Object.keys(countriesData).forEach(country => {
        const option = document.createElement('option');
        option.value = country;
        option.textContent = country;
        countrySelect.appendChild(option);
    });
}

// Country change handler
countrySelect.addEventListener('change', function() {
    citySelect.innerHTML = '<option value="">Select City</option>';
    citySelect.disabled = true;

    if (this.value && countriesData[this.value]) {
        citySelect.disabled = false;
        countriesData[this.value].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});

// Store name visibility based on role
createdAsRoleSelect.addEventListener('change', function() {
    if (this.value === 'business_user') {
        storeNameField.classList.remove('hidden');
        document.getElementById('store_name').required = true;
    } else {
        storeNameField.classList.add('hidden');
        document.getElementById('store_name').required = false;
    }
});

// Discount fields toggle
hasDiscountCheckbox.addEventListener('change', function() {
    if (this.checked) {
        discountFields.classList.remove('hidden');
        document.getElementById('discount_price').required = true;
        document.getElementById('discount_start_date').required = true;
        document.getElementById('discount_end_date').required = true;
    } else {
        discountFields.classList.add('hidden');
        document.getElementById('discount_price').required = false;
        document.getElementById('discount_start_date').required = false;
        document.getElementById('discount_end_date').required = false;
        document.getElementById('discount_price').value = '';
        document.getElementById('discount_start_date').value = '';
        document.getElementById('discount_end_date').value = '';
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
    detailImagesFiles = files;

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
    detailImagesFiles = [];
});

// Remove individual detail image
function removeDetailImage(index) {
    const input = document.getElementById('detailImages');
    const dt = new DataTransfer();
    const files = Array.from(input.files);

    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });

    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

// Load root categories on page load
loadCategoriesForLevel(0, null);

// Auto-generate slug
titleInput.addEventListener('input', function() {
    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
        slugInput.value = generateSlug(this.value);
        slugInput.dataset.autoGenerated = 'true';
    }
});

slugInput.addEventListener('input', function() {
    delete this.dataset.autoGenerated;
});

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
            categoryLevelsData[level] = {
                id: categoryId,
                name: categoryName
            };

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

function loadCategoriesForLevel(level, parentId) {
    const url = parentId
        ? `/admin/categories/children/${parentId}`
        : '/admin/categories/children';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.categories.length > 0) {
            const existingSelect = document.getElementById('category_level_' + level);
            if (existingSelect) {
                existingSelect.remove();
            }

            const newSelect = document.createElement('select');
            newSelect.id = 'category_level_' + level;
            newSelect.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 category-select mt-3';
            newSelect.dataset.level = level;

            newSelect.innerHTML = '<option value="">Select ' + (level === 0 ? 'Category' : 'Subcategory') + '</option>';

            data.categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                newSelect.appendChild(option);
            });

            categoryContainer.appendChild(newSelect);
        }
    });
}

function removeSelectsAfterLevel(level) {
    categoryContainer.querySelectorAll('.category-select').forEach(select => {
        if (parseInt(select.dataset.level) > level) {
            select.remove();
        }
    });
}

function loadVariantsForCategory(categoryId, categoryName, level) {
    if (loadedVariantsByCategory.has(categoryId)) {
        console.log('✓ Variants already loaded for category:', categoryName);
        return;
    }

    console.log('🔄 Loading sidebar variants for:', categoryName, '(ID:', categoryId, ')');

    const loadingCard = createLoadingCard(level);
    variantsContainer.appendChild(loadingCard);

    // Show all variants in sidebar (not just main shown variants)
    fetch(`/admin/listings/category/${categoryId}/variants?show_all=true`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => {
        if (!r.ok) {
            throw new Error(`HTTP error! status: ${r.status}`);
        }
        return r.json();
    })
    .then(data => {
        loadingCard.remove();
        console.log('📦 Sidebar variants API response:', data);

        if (data.success && data.variants.length > 0) {
            loadedVariantsByCategory.add(categoryId);
            console.log(`✓ Rendering ${data.variants.length} variant(s) in sidebar`);
            renderVariantsCard(data.variants, categoryName, level, categoryId);
        } else if (data.success && data.variants.length === 0) {
            console.warn('⚠️ This category has no variants assigned to it');
            // const noVariantsCard = document.createElement('div');
            // noVariantsCard.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4';
            // noVariantsCard.dataset.level = level;
            // noVariantsCard.innerHTML = `
            //     <p class="text-sm text-yellow-800">
            //         <strong>No variants found for "${categoryName}"</strong><br>
            //         <span class="text-xs">This category has no variant attributes assigned to it. You can add variants in the Categories section.</span>
            //     </p>
            // `;
            // variantsContainer.appendChild(noVariantsCard);
        } else {
            console.error('❌ API returned success: false or invalid data');
        }
    })
    .catch(err => {
        console.error('❌ Error loading sidebar variants:', err);
        loadingCard.remove();

        const errorCard = document.createElement('div');
        errorCard.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
        errorCard.dataset.level = level;
        errorCard.innerHTML = `
            <p class="text-sm text-red-800">
                <strong>Failed to load variants</strong><br>
                <span class="text-xs">${err.message}</span>
            </p>
        `;
        variantsContainer.appendChild(errorCard);
    });
}

function createLoadingCard(level) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-lg shadow p-6';
    card.dataset.level = level;
    card.innerHTML = `
        <div class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="text-gray-600 mt-2 text-sm">Loading variants...</p>
        </div>
    `;
    return card;
}

function renderVariantsCard(variants, categoryName, level, categoryId) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-lg shadow p-6 variant-card';
    card.dataset.level = level;
    card.dataset.categoryId = categoryId;

    let levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));

    let html = `
        <div class="mb-4 pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">${levelLabel} Attributes</h3>
                    <p class="text-sm text-gray-600 mt-1">${categoryName}</p>
                </div>
                <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded">
                    ${variants.length} attribute${variants.length !== 1 ? 's' : ''}
                </span>
            </div>
        </div>
        <div class="space-y-4">
    `;

    variants.forEach(variant => {
        html += `<div>`;
        html += `
            <label class="block text-sm font-medium text-gray-700 mb-2">
                ${variant.name}
                ${variant.is_required ? '<span class="text-red-500">*</span>' : ''}
            </label>
        `;

        if (variant.type === 'select') {
            html += `
                <select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select ${variant.name}</option>
            `;
            variant.items.forEach(item => {
                html += `<option value="${item.id}">${item.display_value}</option>`;
            });
            html += `</select>`;

        } else if (variant.type === 'radio') {
            html += `<div class="space-y-2">`;
            variant.items.forEach((item, index) => {
                html += `
                    <label class="flex items-center">
                        <input type="radio" name="variants[${variant.id}]" value="${item.id}"
                               ${variant.is_required && index === 0 ? 'required' : ''}
                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${item.display_value}</span>
                    </label>
                `;
            });
            html += `</div>`;

        } else if (variant.type === 'checkbox') {
            html += `<div class="space-y-2">`;
            variant.items.forEach(item => {
                html += `
                    <label class="flex items-center">
                        <input type="checkbox" name="variants[${variant.id}][]" value="${item.id}"
                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${item.display_value}</span>
                    </label>
                `;
            });
            html += `</div>`;

        } else if (variant.type === 'text') {
            html += `
                <input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       placeholder="${variant.placeholder || ''}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            `;

        } else if (variant.type === 'number') {
            html += `
                <input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       placeholder="${variant.placeholder || ''}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            `;

        } else if (variant.type === 'range') {
            html += `
                <input type="range" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
            `;
        }

        html += `</div>`;
    });

    html += `</div>`;
    card.innerHTML = html;

    variantsContainer.appendChild(card);
}

function removeVariantCardsAfterLevel(level) {
    const cards = variantsContainer.querySelectorAll('.variant-card');
    cards.forEach(card => {
        const cardLevel = parseInt(card.dataset.level);
        if (cardLevel >= level) {
            const categoryId = card.dataset.categoryId;
            if (categoryId) {
                loadedVariantsByCategory.delete(categoryId);
            }
            card.remove();
        }
    });
}

function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
</script>
@endpush
@endsection
