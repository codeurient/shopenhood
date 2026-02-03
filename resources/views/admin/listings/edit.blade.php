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
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                        <label class="flex items-center gap-1.5 px-3 py-1 rounded-full cursor-pointer transition"
                               :class="document.getElementById('default_basic')?.checked ? 'bg-indigo-50 ring-1 ring-indigo-300' : 'bg-gray-50'"
                               id="defaultBasicLabel">
                            <input type="radio"
                                   name="default_variation"
                                   value="basic"
                                   id="default_basic"
                                   @php $noVariationDefault = !$listing->variations->contains('is_default', true); @endphp
                                   {{ $noVariationDefault ? 'checked' : '' }}
                                   onchange="window.dispatchEvent(new CustomEvent('basic-default-selected'))"
                                   class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-medium text-gray-600">Default listing</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Listing Type <span class="text-red-500">*</span>
                            </label>
                            <select name="listing_type_id" id="listing_type_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
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
                                   value="{{ old('title', $listing->title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $listing->slug) }}"
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('short_description', $listing->short_description) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Short summary shown in listings (max 500 characters)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="6" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $listing->description) }}</textarea>
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
                                       value="{{ old('base_price', $listing->base_price) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select name="currency"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="USD" {{ old('currency', $listing->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency', $listing->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency', $listing->currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
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
                                               value="{{ old('discount_price', $listing->discount_price) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                        <input type="datetime-local" name="discount_start_date" id="discount_start_date"
                                               value="{{ old('discount_start_date', $listing->discount_start_date?->format('Y-m-d\TH:i')) }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                        <input type="datetime-local" name="discount_end_date" id="discount_end_date"
                                               value="{{ old('discount_end_date', $listing->discount_end_date?->format('Y-m-d\TH:i')) }}"
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
                                           {{ old('availability_type', $listing->availability_type ?? 'in_stock') === 'in_stock' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">In Stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="availability_type" value="available_by_order"
                                           {{ old('availability_type', $listing->availability_type) === 'available_by_order' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Available by Order</span>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="has_delivery" name="has_delivery" value="1" {{ old('has_delivery', $listing->has_delivery) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <label for="has_delivery" class="ml-2 text-sm font-medium text-gray-700">Delivery Available</label>
                            </div>

                            <div id="deliveryFields" class="{{ old('has_delivery', $listing->has_delivery) ? '' : 'hidden' }} space-y-3 ml-6">
                                <div class="flex items-start gap-4">
                                    <label class="flex items-center mt-2">
                                        <input type="checkbox" name="has_domestic_delivery" value="1" {{ old('has_domestic_delivery', $listing->has_domestic_delivery) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Domestic Delivery</span>
                                    </label>
                                    <div>
                                        <input type="number" name="domestic_delivery_price" step="0.01" min="0"
                                               value="{{ old('domestic_delivery_price', $listing->domestic_delivery_price) }}" placeholder="Price"
                                               class="w-40 px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <label class="flex items-center mt-2">
                                        <input type="checkbox" name="has_international_delivery" value="1" {{ old('has_international_delivery', $listing->has_international_delivery) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">International Delivery</span>
                                    </label>
                                    <div>
                                        <input type="number" name="international_delivery_price" step="0.01" min="0"
                                               value="{{ old('international_delivery_price', $listing->international_delivery_price) }}" placeholder="Price"
                                               class="w-40 px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 border-t pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_negotiable" value="1" {{ old('is_negotiable', $listing->is_negotiable) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Price is negotiable</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $listing->is_visible) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Visible to public</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $listing->is_featured) ? 'checked' : '' }}
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
                                <option value="admin" {{ old('created_as_role', $listing->created_as_role ?? 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="normal_user" {{ old('created_as_role', $listing->created_as_role) === 'normal_user' ? 'selected' : '' }}>Normal User</option>
                                <option value="business_user" {{ old('created_as_role', $listing->created_as_role) === 'business_user' ? 'selected' : '' }}>Business User</option>
                            </select>
                        </div>

                        <div id="storeNameField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Store Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="store_name" id="store_name"
                                   value="{{ old('store_name', $listing->store_name) }}"
                                   placeholder="Enter store name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">This will be displayed as "Shared from [Store Name]"</p>
                        </div>
                    </div>
                </div>

                <!-- Main Image -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Main Image</h3>
                    <p class="text-sm text-gray-600 mb-4">This image will be shown in the listing grid/list view</p>

                    @php $mainImage = $listing->images->where('is_primary', true)->first(); @endphp
                    @if($mainImage)
                        <div class="mb-4" id="main-image-wrapper-{{ $mainImage->id }}">
                            <p class="text-sm text-gray-600 mb-2">Current main image:</p>
                            <div class="relative inline-block w-full">
                                <img src="{{ Storage::url($mainImage->image_path) }}"
                                     alt="Current main image"
                                     class="w-full h-48 object-cover rounded-lg">
                                <button type="button"
                                        onclick="markImageForDeletion({{ $mainImage->id }}, 'main-image-wrapper-{{ $mainImage->id }}')"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-700 transition shadow"
                                        title="Remove main image">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <input type="file" name="main_image" id="mainImage" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                    <div id="mainImagePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-600 mb-2">New image preview:</p>
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
                                   value="{{ old('meta_title', $listing->meta_title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" rows="3" maxlength="160"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('meta_description', $listing->meta_description) }}</textarea>
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
                        <option value="draft" {{ old('status', $listing->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ old('status', $listing->status) === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="active" {{ old('status', $listing->status) === 'active' ? 'selected' : '' }}>Active</option>
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

                    @php
                        $detailImages = $mainImage
                            ? $listing->images->where('id', '!=', $mainImage->id)
                            : $listing->images;
                    @endphp
                    @if($detailImages->count() > 0)
                        <div class="mb-4" id="detailImagesExisting">
                            <p class="text-sm text-gray-600 mb-2">Current detail images ({{ $detailImages->count() }} images):</p>
                            <div class="grid grid-cols-2 gap-2" id="detailImagesGrid">
                                @foreach($detailImages as $image)
                                    <div class="relative" id="detail-image-{{ $image->id }}">
                                        <img src="{{ Storage::url($image->image_path) }}"
                                             alt="Detail image"
                                             class="w-full h-24 object-cover rounded-lg">
                                        <button type="button"
                                                onclick="markImageForDeletion({{ $image->id }}, 'detail-image-{{ $image->id }}')"
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
                    <!-- Always render the container for deletion inputs -->
                    <div id="deleteImagesInputs"></div>

                    <input type="file" name="detail_images[]" id="detailImages" multiple accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Upload new images to add to existing ones</p>

                    <div id="detailImagesPreview" class="mt-4 grid grid-cols-2 gap-2">
                        <!-- Preview images will appear here -->
                    </div>

                    <div id="detailImagesContainer" class="mt-4 space-y-2 hidden">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">New Images:</span>
                            <button type="button" id="clearAllDetailImages"
                                    class="text-xs text-red-600 hover:text-red-800">
                                🗑️ Clear New Images
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Variations Section -->
        <div class="mt-6">
            @include('admin.listings.partials.variation-manager', ['mode' => 'edit', 'listing' => $listing])
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.listings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                💾 Update Listing
            </button>
        </div>
    </form>
</div>

@php
    $variationsData = $listing->variations->map(function($v) {
        return [
            'id' => $v->id,
            'sku' => $v->sku,
            'attributes' => $v->attributes->mapWithKeys(function($attr) {
                return [$attr->variant_id => $attr->variant_item_id];
            })->toArray(),
            'price' => (float) $v->price,
            'discount_price' => $v->discount_price ? (float) $v->discount_price : null,
            'stock_quantity' => $v->stock_quantity,
            'low_stock_threshold' => $v->low_stock_threshold,
            'manage_stock' => $v->manage_stock,
            'allow_backorder' => $v->allow_backorder,
            'is_default' => $v->is_default,
            'is_active' => $v->is_active,
            'images' => [],
        ];
    })->toArray();

    // Listing variant values (sidebar category variant selections)
    $listingVariantValues = $listing->listingVariants->mapWithKeys(function($lv) {
        return [$lv->variant_id => [
            'variant_item_id' => $lv->variant_item_id,
            'custom_value' => $lv->custom_value,
        ]];
    })->toArray();
@endphp

@push('scripts')
<script>
// VANILLA JS APPROACH: Set variations data globally BEFORE Alpine initializes
// This bypasses all Alpine timing/initialization issues
window.EDIT_VARIATIONS = @json($variationsData);
console.log('✓ Global variations data set:', window.EDIT_VARIATIONS.length, 'variations');

// Listing variant values for pre-selecting sidebar variant dropdowns
// Format: { variant_id: { variant_item_id: X, custom_value: Y } }
window.EDIT_VARIANT_VALUES = @json($listingVariantValues);

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

// Delivery fields toggle
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

// Unified image deletion function for both main and detail images
function markImageForDeletion(imageId, wrapperId) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;

    // Add hidden input for deletion
    const inputsContainer = document.getElementById('deleteImagesInputs');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_images[]';
    input.value = imageId;
    input.id = 'delete-input-' + imageId;
    inputsContainer.appendChild(input);

    // Fade out and hide the remove button
    wrapper.style.opacity = '0.3';
    const btn = wrapper.querySelector('button');
    if (btn) btn.style.display = 'none';

    // Add undo link
    const undo = document.createElement('p');
    undo.className = 'text-xs text-indigo-600 mt-1 cursor-pointer hover:underline';
    undo.textContent = 'Undo removal';
    undo.onclick = function() {
        const delInput = document.getElementById('delete-input-' + imageId);
        if (delInput) delInput.remove();
        wrapper.style.opacity = '1';
        if (btn) btn.style.display = '';
        undo.remove();
    };
    wrapper.appendChild(undo);
}

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
        return;
    }

    const loadingCard = createLoadingCard(level);
    variantsContainer.appendChild(loadingCard);

    // Show all variants in sidebar (not just main shown variants)
    fetch(`/admin/listings/category/${categoryId}/variants?show_all=true`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        loadingCard.remove();

        if (data.success && data.variants.length > 0) {
            loadedVariantsByCategory.add(categoryId);
            renderVariantsCard(data.variants, categoryName, level, categoryId);
        }
    })
    .catch(err => {
        console.error('Error loading variants:', err);
        loadingCard.remove();
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

    // Get saved variant values (for edit mode pre-selection)
    const savedValues = window.EDIT_VARIANT_VALUES || {};

    variants.forEach(variant => {
        const saved = savedValues[variant.id];
        const savedItemId = saved ? saved.variant_item_id : null;
        const savedCustom = saved ? saved.custom_value : null;

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
                const selected = savedItemId && savedItemId == item.id ? 'selected' : '';
                html += `<option value="${item.id}" ${selected}>${item.display_value}</option>`;
            });
            html += `</select>`;

        } else if (variant.type === 'radio') {
            html += `<div class="space-y-2">`;
            variant.items.forEach((item, index) => {
                const checked = savedItemId && savedItemId == item.id ? 'checked' : '';
                html += `
                    <label class="flex items-center">
                        <input type="radio" name="variants[${variant.id}]" value="${item.id}"
                               ${variant.is_required && index === 0 ? 'required' : ''} ${checked}
                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${item.display_value}</span>
                    </label>
                `;
            });
            html += `</div>`;

        } else if (variant.type === 'checkbox') {
            html += `<div class="space-y-2">`;
            variant.items.forEach(item => {
                const checked = savedItemId && savedItemId == item.id ? 'checked' : '';
                html += `
                    <label class="flex items-center">
                        <input type="checkbox" name="variants[${variant.id}][]" value="${item.id}" ${checked}
                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${item.display_value}</span>
                    </label>
                `;
            });
            html += `</div>`;

        } else if (variant.type === 'text') {
            const val = savedCustom ? savedCustom.replace(/"/g, '&quot;') : '';
            html += `
                <input type="text" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       value="${val}"
                       placeholder="${variant.placeholder || ''}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            `;

        } else if (variant.type === 'number') {
            const val = savedCustom || '';
            html += `
                <input type="number" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       value="${val}"
                       placeholder="${variant.placeholder || ''}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            `;

        } else if (variant.type === 'range') {
            const val = savedCustom || '';
            html += `
                <input type="range" name="variants[${variant.id}]" ${variant.is_required ? 'required' : ''}
                       value="${val}"
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

// ==================== Pre-populate existing data on page load ====================

document.addEventListener('DOMContentLoaded', function() {
    // Pre-populate category selection
    @if($listing->category_id)
        categoryHiddenInput.value = '{{ $listing->category_id }}';
        // Load category hierarchy
        loadCategoryHierarchy({{ $listing->category_id }});
    @endif

    // Pre-populate country and city
    @if($listing->country)
        countrySelect.value = '{{ $listing->country }}';
        countrySelect.dispatchEvent(new Event('change'));
        setTimeout(() => {
            @if($listing->city)
                citySelect.value = '{{ $listing->city }}';
            @endif
        }, 100);
    @endif

    // Show discount fields if listing has discount
    @if($listing->discount_price)
        hasDiscountCheckbox.checked = true;
        discountFields.classList.remove('hidden');
        document.getElementById('discount_price').required = true;
        document.getElementById('discount_start_date').required = true;
        document.getElementById('discount_end_date').required = true;
    @endif

    // Show store name field if business user
    @if($listing->created_as_role === 'business_user')
        storeNameField.classList.remove('hidden');
        document.getElementById('store_name').required = true;
    @endif
});

// Load category hierarchy for editing
function loadCategoryHierarchy(categoryId) {
    fetch(`/admin/categories/${categoryId}/hierarchy`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.hierarchy) {
            // Load each level sequentially
            loadCategoryLevel(data.hierarchy, 0);
        }
    })
    .catch(err => console.error('Error loading category hierarchy:', err));
}

function loadCategoryLevel(hierarchy, index) {
    if (index >= hierarchy.length) {
        // After loading all category levels, set up event listener for preloading variations
        // The actual loading will happen when category-variants-loaded event fires
        preloadExistingVariations();
        return;
    }

    const item = hierarchy[index];
    const level = index;
    const parentId = index > 0 ? hierarchy[index - 1].id : null;
    const isLastLevel = (index === hierarchy.length - 1);

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
                if (cat.id === item.id) {
                    option.selected = true;
                }
                newSelect.appendChild(option);
            });

            categoryContainer.appendChild(newSelect);

            // Store in categoryLevelsData
            categoryLevelsData[level] = {
                id: item.id,
                name: item.name
            };

            // Load variants for sidebar (all levels)
            loadVariantsForCategory(item.id, item.name, level);

            // Emit category-changed for EVERY level so the variation manager
            // accumulates variants from the entire category hierarchy
            console.log('Emitting category-changed for level:', level, item.name);
            window.dispatchEvent(new CustomEvent('category-changed', {
                detail: { categoryId: item.id, categoryName: item.name, level }
            }));

            // Load next level
            loadCategoryLevel(hierarchy, index + 1);
        }
    });
}

// Pre-load existing variations is now handled via global variable (window.EDIT_VARIATIONS)
// The variation manager's init() function will automatically load it
function preloadExistingVariations() {
    // No longer needed - data loads automatically from window.EDIT_VARIATIONS
    console.log('✓ Variations will be loaded automatically by Alpine from window.EDIT_VARIATIONS');
}
</script>
@endpush
@endsection
