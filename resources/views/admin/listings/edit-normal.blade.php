@extends('admin.layouts.app')

@section('title', 'Edit Listing: ' . $listing->title)
@section('page-title', 'Edit Listing')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Edit Listing: {{ $listing->title }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.listings.show', $listing) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                ← Back to Details
            </a>
            <a href="{{ route('admin.listings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                All Listings
            </a>
        </div>
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

    <form action="{{ route('admin.listings.update', $listing) }}" method="POST" enctype="multipart/form-data" id="listingForm">
        @csrf
        @method('PUT')

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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Type</option>
                                @foreach($listingTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('listing_type_id', $listing->listing_type_id) == $type->id ? 'selected' : '' }}>
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
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select"
                                        data-level="0">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <input type="hidden" name="category_id" id="category_id_hidden" value="{{ old('category_id', $listing->category_id) }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required maxlength="255"
                                   value="{{ old('title', $listing->title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $listing->slug) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <p class="text-sm text-gray-500 mt-1">Auto-generated if left empty</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                            <textarea name="short_description" rows="2" maxlength="500"
                                      placeholder="Brief summary for preview (optional)"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('short_description', $listing->short_description) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Short summary shown in listings (max 500 characters)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description', $listing->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Availability -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Availability</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base Price <span class="text-red-500">*</span></label>
                                <input type="number" name="base_price" step="0.01" min="0.01" required
                                       value="{{ old('base_price', $listing->base_price) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select name="currency"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    <option value="USD" {{ old('currency', $listing->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency', $listing->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency', $listing->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_negotiable" id="is_negotiable" value="1"
                                   {{ old('is_negotiable', $listing->is_negotiable) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_negotiable" class="text-sm text-gray-700">Price is negotiable</label>
                        </div>

                        <!-- Discount -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="has_discount" value="1"
                                       {{ old('discount_price', $listing->discount_price) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                                <label for="has_discount" class="ml-2 text-sm font-medium text-gray-700">Apply Discount</label>
                            </div>

                            <div id="discountFields" class="{{ old('discount_price', $listing->discount_price) ? '' : 'hidden' }} space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Price</label>
                                        <input type="number" name="discount_price" id="discount_price" step="0.01" min="0"
                                               value="{{ old('discount_price', $listing->discount_price) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                        <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                               value="{{ old('discount_start_date', $listing->discount_start_date?->format('Y-m-d\TH:i')) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                        <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                               value="{{ old('discount_end_date', $listing->discount_end_date?->format('Y-m-d\TH:i')) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">The discount price will be shown with a strikethrough on the original price during the specified period.</p>
                            </div>
                        </div>

                        <!-- Product Condition -->
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Product Condition <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center">
                                    <input type="radio" name="condition" value="new"
                                           {{ old('condition', $listing->condition ?? 'new') === 'new' ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">New</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="condition" value="used"
                                           {{ old('condition', $listing->condition) === 'used' ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">Second-hand</span>
                                </label>
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
                                           {{ old('availability_type', $listing->availability_type ?? 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">In Stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="availability_type" value="available_by_order"
                                           {{ old('availability_type', $listing->availability_type) === 'available_by_order' ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">Available by Order</span>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="has_delivery" name="has_delivery" value="1"
                                       {{ old('has_delivery', $listing->has_delivery) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                                <label for="has_delivery" class="ml-2 text-sm font-medium text-gray-700">Delivery Available</label>
                            </div>

                            <div id="deliveryFields" class="{{ old('has_delivery', $listing->has_delivery) ? '' : 'hidden' }} space-y-3 ml-6">
                                <div class="flex items-start gap-4">
                                    <label class="flex items-center mt-2">
                                        <input type="checkbox" name="has_domestic_delivery" value="1"
                                               {{ old('has_domestic_delivery', $listing->has_domestic_delivery) ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Domestic Delivery</span>
                                    </label>
                                    <div>
                                        <input type="number" name="domestic_delivery_price" step="0.01" min="0"
                                               value="{{ old('domestic_delivery_price', $listing->domestic_delivery_price) }}" placeholder="Price"
                                               class="w-40 px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <label class="flex items-center mt-2">
                                        <input type="checkbox" name="has_international_delivery" value="1"
                                               {{ old('has_international_delivery', $listing->has_international_delivery) ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">International Delivery</span>
                                    </label>
                                    <div>
                                        <input type="number" name="international_delivery_price" step="0.01" min="0"
                                               value="{{ old('international_delivery_price', $listing->international_delivery_price) }}" placeholder="Price"
                                               class="w-40 px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 border-t pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_visible" value="1"
                                       {{ old('is_visible', $listing->is_visible) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700">Visible to public</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1"
                                       {{ old('is_featured', $listing->is_featured) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700">Featured</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select name="country" id="country"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Country</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <select name="city" id="city" disabled
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select country first</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" maxlength="60"
                                   value="{{ old('meta_title', $listing->meta_title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" rows="3" maxlength="160"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $listing->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status, Variants & Images -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ old('status', $listing->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ old('status', $listing->status) === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="active" {{ old('status', $listing->status) === 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>

                <!-- Dynamic Variants Container -->
                <div id="variantsContainer" class="space-y-4"></div>

                <!-- Container for deletion inputs -->
                <div id="deleteImagesInputs"></div>

                <!-- Current Images -->
                @php $allImages = $listing->images; @endphp
                @if($allImages->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Images</h3>
                    <p class="text-xs text-gray-500 mb-4">{{ $allImages->count() }} image(s). The first (main) image has a "Main" badge.</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($allImages->sortBy('sort_order') as $image)
                            <div class="relative" id="img-{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image"
                                     class="w-full h-24 object-cover rounded-lg">
                                @if($image->is_primary)
                                    <span class="absolute bottom-1 left-1 text-xs bg-black/60 text-white px-1 rounded">Main</span>
                                @endif
                                <button type="button"
                                        onclick="markImageForDeletion({{ $image->id }}, 'img-{{ $image->id }}')"
                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 transition shadow"
                                        title="Remove image">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add Product Images -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Product Images</h3>
                    <p class="text-xs text-gray-500 mb-4">Upload new images to add to existing ones. (max 10 total)</p>

                    <input type="file" name="product_images[]" id="productImages" multiple
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                    <div id="productImagesPreview" class="mt-4 grid grid-cols-2 gap-2"></div>

                    <div id="productImagesControls" class="mt-3 hidden">
                        <button type="button" id="clearAllProductImages"
                                class="text-xs text-red-600 hover:text-red-800">
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.listings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                💾 Update Listing
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
window.EDIT_VARIANT_VALUES = @json($listing->listingVariants->mapWithKeys(function($lv) {
    return [$lv->variant_id => [
        'variant_item_id' => $lv->variant_item_id,
        'custom_value' => $lv->custom_value,
    ]];
})->toArray());

const categoryContainer = document.getElementById('categorySelectsContainer');
const categoryHiddenInput = document.getElementById('category_id_hidden');
const variantsContainer = document.getElementById('variantsContainer');
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');
const countrySelect = document.getElementById('country');
const citySelect = document.getElementById('city');

let categoryLevelsData = {};
let loadedVariantsByCategory = new Set();
let countriesData = {};

// Load countries and cities
fetch('/api/locations/countries-cities')
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

// Image deletion with undo
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
    undo.className = 'text-xs text-primary-600 mt-1 cursor-pointer hover:underline';
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

// New product images preview
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
                    const badge = index === 0 ? '<span class="absolute bottom-1 left-1 text-xs bg-black/60 text-white px-1 rounded">New Main</span>' : '';
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
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) { dt.items.add(file); }
    });
    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

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

function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Category selection
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

function loadCategoriesForLevel(level, parentId) {
    const url = parentId
        ? `/admin/categories/children/${parentId}`
        : '/admin/categories/children';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.categories.length > 0) {
            const existing = document.getElementById('category_level_' + level);
            if (existing) { existing.remove(); }

            const sel = document.createElement('select');
            sel.id = 'category_level_' + level;
            sel.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select mt-3';
            sel.dataset.level = level;
            sel.innerHTML = '<option value="">Select ' + (level === 0 ? 'Category' : 'Subcategory') + '</option>';

            data.categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                sel.appendChild(option);
            });

            categoryContainer.appendChild(sel);
        }
    });
}

function removeSelectsAfterLevel(level) {
    categoryContainer.querySelectorAll('.category-select').forEach(sel => {
        if (parseInt(sel.dataset.level) > level) { sel.remove(); }
    });
}

function loadVariantsForCategory(categoryId, categoryName, level) {
    if (loadedVariantsByCategory.has(categoryId)) { return; }

    fetch(`/admin/listings/category/${categoryId}/variants?show_all=true`, {
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
    card.className = 'bg-white rounded-lg shadow p-6 variant-card';
    card.dataset.level = level;
    card.dataset.categoryId = categoryId;

    const savedValues = window.EDIT_VARIANT_VALUES || {};
    let levelLabel = level === 0 ? 'Category' : (level === 1 ? 'Subcategory' : 'Level ' + (level + 1));

    let html = `
        <div class="mb-4 pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">${levelLabel} Attributes</h3>
                    <p class="text-sm text-gray-600 mt-1">${categoryName}</p>
                </div>
                <span class="px-2 py-1 text-xs bg-primary-100 text-primary-700 rounded">
                    ${variants.length} attribute${variants.length !== 1 ? 's' : ''}
                </span>
            </div>
        </div>
        <div class="space-y-4">
    `;

    variants.forEach(variant => {
        const saved = savedValues[variant.id];
        const savedItemId = saved ? saved.variant_item_id : null;
        const savedCustom = saved ? saved.custom_value : null;

        html += `<div>`;
        html += `<label class="block text-sm font-medium text-gray-700 mb-2">${variant.name}${variant.is_required ? ' <span class="text-red-500">*</span>' : ''}</label>`;

        if (variant.type === 'select') {
            html += `<select name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"><option value="">Select ${variant.name}</option>`;
            variant.items.forEach(item => {
                const selected = savedItemId && savedItemId == item.id ? 'selected' : '';
                html += `<option value="${item.id}" ${selected}>${item.display_value}</option>`;
            });
            html += `</select>`;
        } else if (variant.type === 'radio') {
            html += `<div class="space-y-2">`;
            variant.items.forEach((item, index) => {
                const checked = savedItemId && savedItemId == item.id ? 'checked' : '';
                html += `<label class="flex items-center"><input type="radio" name="variants[${variant.id}]" value="${item.id}" ${variant.is_required && index === 0 ? 'required' : ''} ${checked} class="w-4 h-4 text-primary-600 focus:ring-primary-500"><span class="ml-2 text-sm text-gray-700">${item.display_value}</span></label>`;
            });
            html += `</div>`;
        } else if (variant.type === 'checkbox') {
            html += `<div class="space-y-2">`;
            variant.items.forEach(item => {
                const checked = savedItemId && savedItemId == item.id ? 'checked' : '';
                html += `<label class="flex items-center"><input type="checkbox" name="variants[${variant.id}][]" value="${item.id}" ${checked} class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"><span class="ml-2 text-sm text-gray-700">${item.display_value}</span></label>`;
            });
            html += `</div>`;
        } else if (variant.type === 'text') {
            const val = savedCustom ? String(savedCustom).replace(/"/g, '&quot;') : '';
            html += `<input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} value="${val}" placeholder="${variant.placeholder || ''}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">`;
        } else if (variant.type === 'number') {
            const val = savedCustom || '';
            html += `<input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''} value="${val}" placeholder="${variant.placeholder || ''}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">`;
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

// Pre-populate on load
loadCategoriesForLevel(0, null);

document.addEventListener('DOMContentLoaded', function() {
    @if($listing->category_id)
        categoryHiddenInput.value = '{{ $listing->category_id }}';
        loadCategoryHierarchy({{ $listing->category_id }});
    @endif

    @if($listing->country)
        countrySelect.value = '{{ $listing->country }}';
        countrySelect.dispatchEvent(new Event('change'));
        setTimeout(() => {
            @if($listing->city)
                citySelect.value = '{{ $listing->city }}';
            @endif
        }, 100);
    @endif
});

function loadCategoryHierarchy(categoryId) {
    fetch(`/admin/categories/${categoryId}/hierarchy`, {
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

    const url = parentId
        ? `/admin/categories/children/${parentId}`
        : '/admin/categories/children';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.categories.length > 0) {
            const existing = document.getElementById('category_level_' + level);
            if (existing) { existing.remove(); }

            const sel = document.createElement('select');
            sel.id = 'category_level_' + level;
            sel.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 category-select mt-3';
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

            window.dispatchEvent(new CustomEvent('category-changed', {
                detail: { categoryId: item.id, categoryName: item.name, level }
            }));

            loadCategoryLevel(hierarchy, index + 1);
        }
    });
}
</script>
@endpush
@endsection
