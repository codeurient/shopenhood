@extends('layouts.public')

@section('title', $listing->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div x-data="productVariantSelector()" x-init="init()">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    <img :src="currentImage"
                         :alt="'{{ $listing->title }}'"
                         class="w-full h-full object-cover">
                </div>

                <!-- Image Thumbnails -->
                <div class="grid grid-cols-5 gap-2" x-show="currentVariation && currentVariation.images.length > 1">
                    <template x-for="(image, index) in (currentVariation?.images || [])" :key="index">
                        <button @click="currentImage = image.url"
                                :class="currentImage === image.url ? 'ring-2 ring-indigo-600' : 'ring-1 ring-gray-200'"
                                class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:opacity-75 transition">
                            <img :src="image.url"
                                 alt="Product thumbnail"
                                 class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Title & Category -->
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <a href="{{ route('categories.show', $listing->category) }}" class="hover:text-gray-700">
                            {{ $listing->category->name }}
                        </a>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $listing->title }}</h1>
                </div>

                <!-- Price -->
                <div>
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-bold text-gray-900" x-text="formatPrice(currentVariation?.current_price)"></span>
                        <template x-if="currentVariation?.has_discount">
                            <span class="text-xl text-gray-500 line-through" x-text="formatPrice(currentVariation?.price)"></span>
                        </template>
                        <template x-if="currentVariation?.has_discount">
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded"
                                  x-text="currentVariation?.discount_percentage + '% OFF'"></span>
                        </template>
                    </div>
                </div>

                <!-- SKU & Stock -->
                <div class="flex items-center gap-4 text-sm">
                    <div class="text-gray-600">
                        SKU: <span class="font-medium text-gray-900" x-text="currentVariation?.sku"></span>
                    </div>
                    <div>
                        <template x-if="currentVariation?.is_in_stock">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                In Stock
                            </span>
                        </template>
                        <template x-if="!currentVariation?.is_in_stock">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        </template>
                        <template x-if="currentVariation?.is_low_stock && currentVariation?.is_in_stock">
                            <span class="ml-2 text-xs text-orange-600">
                                Only <span x-text="currentVariation?.stock_quantity"></span> left!
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Variant Selectors -->
                <div class="space-y-4">
                    <template x-for="variant in variants" :key="variant.id">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-900" x-text="variant.name"></label>
                                <span class="text-sm text-gray-600" x-show="selectedAttributes[variant.id]">
                                    <span x-text="getSelectedItemValue(variant.id)"></span>
                                </span>
                            </div>

                            <!-- Color Swatches -->
                            <template x-if="variant.display_type === 'color_swatch'">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in variant.items" :key="item.id">
                                        <button @click="selectAttribute(variant.id, item.id)"
                                                :disabled="!isOptionAvailable(variant.id, item.id)"
                                                :class="{
                                                    'ring-2 ring-indigo-600': selectedAttributes[variant.id] == item.id,
                                                    'ring-1 ring-gray-300': selectedAttributes[variant.id] != item.id,
                                                    'opacity-40 cursor-not-allowed': !isOptionAvailable(variant.id, item.id)
                                                }"
                                                :title="item.value"
                                                class="w-10 h-10 rounded-full transition hover:scale-110"
                                                :style="`background-color: ${item.color_code}`">
                                            <span class="sr-only" x-text="item.value"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Image Swatches -->
                            <template x-if="variant.display_type === 'image_swatch'">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in variant.items" :key="item.id">
                                        <button @click="selectAttribute(variant.id, item.id)"
                                                :disabled="!isOptionAvailable(variant.id, item.id)"
                                                :class="{
                                                    'ring-2 ring-indigo-600': selectedAttributes[variant.id] == item.id,
                                                    'ring-1 ring-gray-300': selectedAttributes[variant.id] != item.id,
                                                    'opacity-40 cursor-not-allowed': !isOptionAvailable(variant.id, item.id)
                                                }"
                                                class="w-16 h-16 rounded-lg overflow-hidden transition hover:scale-105">
                                            <img :src="'/storage/' + item.image_path"
                                                 :alt="item.value"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Buttons -->
                            <template x-if="variant.display_type === 'button'">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in variant.items" :key="item.id">
                                        <button @click="selectAttribute(variant.id, item.id)"
                                                :disabled="!isOptionAvailable(variant.id, item.id)"
                                                :class="{
                                                    'bg-indigo-600 text-white border-indigo-600': selectedAttributes[variant.id] == item.id,
                                                    'bg-white text-gray-900 border-gray-300': selectedAttributes[variant.id] != item.id,
                                                    'opacity-40 cursor-not-allowed': !isOptionAvailable(variant.id, item.id)
                                                }"
                                                class="px-4 py-2 border rounded-lg font-medium text-sm transition hover:border-indigo-600"
                                                x-text="item.value">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Dropdown -->
                            <template x-if="variant.display_type === 'dropdown'">
                                <select @change="selectAttribute(variant.id, $event.target.value)"
                                        x-model="selectedAttributes[variant.id]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select {{ variant.name }}</option>
                                    <template x-for="item in variant.items" :key="item.id">
                                        <option :value="item.id"
                                                :disabled="!isOptionAvailable(variant.id, item.id)"
                                                x-text="item.value"></option>
                                    </template>
                                </select>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Description -->
                @if($listing->description)
                <div class="prose prose-sm max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    <p class="text-gray-600">{{ $listing->description }}</p>
                </div>
                @endif

                <!-- Add to Cart -->
                <div class="space-y-3">
                    <div class="flex gap-3">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button @click="quantity = Math.max(1, quantity - 1)"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-900">
                                -
                            </button>
                            <input type="number"
                                   x-model.number="quantity"
                                   min="1"
                                   :max="currentVariation?.stock_quantity || 999"
                                   class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none">
                            <button @click="quantity = Math.min(currentVariation?.stock_quantity || 999, quantity + 1)"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-900">
                                +
                            </button>
                        </div>
                        <button @click="addToCart"
                                :disabled="!currentVariation?.is_in_stock || !isVariationFullySelected()"
                                :class="currentVariation?.is_in_stock && isVariationFullySelected()
                                    ? 'bg-indigo-600 hover:bg-indigo-700'
                                    : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 px-6 py-3 text-white font-semibold rounded-lg transition">
                                <template x-if="currentVariation?.is_in_stock && isVariationFullySelected()">
                                    <span>Add to Cart</span>
                                </template>
                                <template x-if="!currentVariation?.is_in_stock">
                                    <span>Out of Stock</span>
                                </template>
                                <template x-if="currentVariation?.is_in_stock && !isVariationFullySelected()">
                                    <span>Select Options</span>
                                </template>
                            </button>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="border-t border-gray-200 pt-6 space-y-3 text-sm">
                    @if($listing->listingType)
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span>{{ $listing->listingType->name }}</span>
                    </div>
                    @endif

                    @if($listing->user->store_name)
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>{{ $listing->user->store_name }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function productVariantSelector() {
    return {
        variations: @json($variationsData),
        variants: @json($variantsData),
        selectedAttributes: {},
        availableOptions: {},
        currentVariation: null,
        currentImage: '',
        quantity: 1,

        init() {
            // Set default variation and attributes
            const defaultVariation = this.variations.find(v => v.is_default) || this.variations[0];
            if (defaultVariation) {
                this.currentVariation = defaultVariation;
                this.currentImage = defaultVariation.images[0]?.url || '';

                // Pre-select default variation attributes
                defaultVariation.attributes.forEach(attr => {
                    this.selectedAttributes[attr.variant_id] = attr.variant_item_id;
                });

                // Initialize available options
                this.updateAvailableOptions();
            }
        },

        async selectAttribute(variantId, itemId) {
            // Update selected attributes
            this.selectedAttributes[variantId] = itemId;

            // Update available options based on current selection
            await this.updateAvailableOptions();

            // Try to find matching variation
            this.findMatchingVariation();
        },

        async updateAvailableOptions() {
            try {
                const response = await fetch(`{{ route('listings.show', $listing) }}/available-options`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        selected: this.selectedAttributes
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.availableOptions = data.available_options;
                }
            } catch (error) {
                console.error('Error updating available options:', error);
            }
        },

        findMatchingVariation() {
            // Find variation that matches all selected attributes
            const selectedCount = Object.keys(this.selectedAttributes).length;

            // Only search if all variants are selected
            if (selectedCount !== this.variants.length) {
                return;
            }

            const matchingVariation = this.variations.find(variation => {
                return variation.attributes.every(attr => {
                    return this.selectedAttributes[attr.variant_id] == attr.variant_item_id;
                });
            });

            if (matchingVariation) {
                this.currentVariation = matchingVariation;
                this.currentImage = matchingVariation.images[0]?.url || '';

                // Adjust quantity if it exceeds new variation's stock
                if (this.quantity > matchingVariation.stock_quantity) {
                    this.quantity = Math.max(1, matchingVariation.stock_quantity);
                }
            }
        },

        isOptionAvailable(variantId, itemId) {
            // If nothing selected yet, all options are available
            if (Object.keys(this.availableOptions).length === 0) {
                return true;
            }

            // Check if this option is in available options
            return this.availableOptions[variantId]?.includes(itemId) || false;
        },

        isVariationFullySelected() {
            return Object.keys(this.selectedAttributes).length === this.variants.length &&
                   Object.values(this.selectedAttributes).every(v => v !== null && v !== '');
        },

        getSelectedItemValue(variantId) {
            const itemId = this.selectedAttributes[variantId];
            if (!itemId) return '';

            const variant = this.variants.find(v => v.id == variantId);
            const item = variant?.items.find(i => i.id == itemId);
            return item?.value || '';
        },

        formatPrice(price) {
            if (!price) return '$0.00';
            return '$' + parseFloat(price).toFixed(2);
        },

        async addToCart() {
            if (!this.currentVariation?.is_in_stock || !this.isVariationFullySelected()) {
                return;
            }

            // TODO: Implement add to cart functionality
            console.log('Adding to cart:', {
                variation_id: this.currentVariation.id,
                quantity: this.quantity
            });

            alert(`Added ${this.quantity} item(s) to cart!`);
        }
    }
}
</script>
@endpush
@endsection
