@php
    $isEditMode = isset($mode) && $mode === 'edit';
    $hasExistingVariations = $isEditMode && isset($listing) && $listing->variations->count() > 0;
@endphp

<!-- Product Variations Manager -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="variationManager()" x-init="init()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Product Variations</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage different variants (colors, sizes, etc.) with individual pricing and stock</p>
            @if($hasExistingVariations)
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                    <strong>Edit Mode:</strong> You are editing existing variations. Use "Regenerate" carefully as it will replace all current variations.
                </p>
            @endif
        </div>
        <div class="flex gap-2">
            @if($isEditMode)
                <button type="button" @click="confirmRegenerateVariations()"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition"
                        :disabled="categoryVariants.length === 0">
                    Regenerate All Combinations
                </button>
            @else
                <button type="button" @click="generateAllVariations()"
                        class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition"
                        :disabled="categoryVariants.length === 0">
                    Generate All Combinations
                </button>
            @endif
            <button type="button" @click="addManualVariation()"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                Add Manual Variation
            </button>
        </div>
    </div>

    <!-- Variant Selection Info -->
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 rounded" x-show="categoryVariants.length > 0" x-cloak>
        <p class="text-sm text-blue-700 dark:text-blue-300">
            <strong>Available Variants for this category:</strong>
            <span x-text="categoryVariants.map(v => v.name).join(', ')"></span>
        </p>
        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1" x-show="categoryVariants.length > 0">
            Select variants below to generate all possible combinations automatically.
        </p>
    </div>

    <!-- Variations Table -->
    <div class="overflow-x-auto" x-show="variations.length > 0" x-cloak>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU</th>
                    <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" x-text="variant.name"></th>
                    </template>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Images</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Default</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="(variation, index) in variations" :key="index">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <!-- SKU -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="text"
                                   :name="`variations[${index}][sku]`"
                                   x-model="variation.sku"
                                   @input="updateSKU(index)"
                                   placeholder="Auto-generate"
                                   class="w-32 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                        </td>

                        <!-- Variant Values -->
                        <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">

                            <td class="px-4 py-3 whitespace-nowrap">
                                <select :name="`variations[${index}][attributes][${variant.id}]`"
                                        x-model="variation.attributes[variant.id]"
                                        @change="updateVariationDisplay(index)"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select</option>
                                    <template x-for="item in variant.items" :key="item.id">
                                        <option :value="item.id" x-text="item.display_value || item.value"></option>
                                    </template>
                                </select>
                            </td>
                        </template>

                        <!-- Price -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="number"
                                   :name="`variations[${index}][price]`"
                                   x-model="variation.price"
                                   step="0.01"
                                   placeholder="0.00"
                                   required
                                   class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                        </td>

                        <!-- Discount Price -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="number"
                                   :name="`variations[${index}][discount_price]`"
                                   x-model="variation.discount_price"
                                   step="0.01"
                                   placeholder="Optional"
                                   class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                        </td>

                        <!-- Stock -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="number"
                                   :name="`variations[${index}][stock_quantity]`"
                                   x-model="variation.stock_quantity"
                                   placeholder="0"
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500">
                        </td>

                        <!-- Images -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="openImageModal(index)"
                                        class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="imageCount(index) > 0 ? imageCount(index) + ' image(s)' : 'Upload'"></span>
                                </button>
                                <!-- Hidden delete inputs for existing images marked for removal -->
                                <template x-for="imgId in variation.deleted_image_ids" :key="imgId">
                                    <input type="hidden" name="delete_variation_image_ids[]" :value="imgId">
                                </template>
                                <input type="file"
                                       :id="`variation_images_${index}`"
                                       :name="`variations[${index}][images][]`"
                                       @change="handleImageUpload($event, index)"
                                       multiple
                                       accept="image/*"
                                       class="hidden">
                            </div>
                        </td>

                        <!-- Default -->
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <input type="radio"
                                   name="default_variation"
                                   :value="index"
                                   x-model="defaultVariationIndex"
                                   @change="setDefaultVariation(index)"
                                   class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                            <input type="hidden"
                                   :name="`variations[${index}][is_default]`"
                                   :value="defaultVariationIndex === index ? 1 : 0">
                        </td>

                        <!-- Active -->
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <input type="checkbox"
                                   :name="`variations[${index}][is_active]`"
                                   x-model="variation.is_active"
                                   value="1"
                                   class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button type="button"
                                    @click="removeVariation(index)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">
                                Delete
                            </button>

                            <!-- Hidden fields for existing variations and stock management -->
                            <input type="hidden" :name="`variations[${index}][id]`" x-model="variation.id">
                            <input type="hidden" :name="`variations[${index}][manage_stock]`" :value="variation.manage_stock ? 1 : 0">
                            <input type="hidden" :name="`variations[${index}][allow_backorder]`" :value="variation.allow_backorder ? 1 : 0">
                            <input type="hidden" :name="`variations[${index}][low_stock_threshold]`" :value="variation.low_stock_threshold || 10">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg" x-show="variations.length === 0" x-cloak>
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No variations created yet</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">
            Select a category with variants to automatically generate combinations,<br>
            or manually add individual variations
        </p>
    </div>

    <!-- Bulk Actions -->
    <div class="mt-6 flex gap-4" x-show="variations.length > 0" x-cloak>
        <button type="button"
                @click="bulkSetPrice()"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition">
            Set Price for All
        </button>
        <button type="button"
                @click="bulkSetStock()"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition">
            Set Stock for All
        </button>
        <button type="button"
                @click="clearAllVariations()"
                class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-900/50 transition">
            Clear All
        </button>
        <div class="flex-1"></div>
        <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
            Total Variations: <strong class="ml-2" x-text="variations.length"></strong>
        </div>
    </div>

    <!-- Hidden field for variation count -->
    <input type="hidden" name="variation_count" :value="variations.length">

    <!-- Image Manager Modal -->
    <div x-show="showImageModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeImageModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Variation Images</h3>
                    <button type="button" @click="closeImageModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="imageModalIndex !== null">
                    <div>
                        <!-- Existing server images -->
                        <template x-if="variations[imageModalIndex].existing_images && variations[imageModalIndex].existing_images.length > 0">
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Saved Images</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="img in variations[imageModalIndex].existing_images" :key="img.id">
                                        <div class="relative w-20 h-20 rounded overflow-hidden border dark:border-gray-600"
                                             :class="variations[imageModalIndex].deleted_image_ids.includes(img.id) ? 'opacity-30' : ''">
                                            <img :src="img.url" class="w-full h-full object-cover">
                                            <button type="button"
                                                    @click="toggleExistingImageDeletion(imageModalIndex, img.id)"
                                                    class="absolute top-0.5 right-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold"
                                                    :class="variations[imageModalIndex].deleted_image_ids.includes(img.id)
                                                        ? 'bg-green-500 text-white'
                                                        : 'bg-red-500 text-white'">
                                                <span x-text="variations[imageModalIndex].deleted_image_ids.includes(img.id) ? '↩' : '×'"></span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Newly selected files -->
                        <template x-if="variations[imageModalIndex].images && variations[imageModalIndex].images.length > 0">
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">New Uploads</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(file, fi) in variations[imageModalIndex].images" :key="fi">
                                        <div class="relative w-20 h-20 rounded overflow-hidden border dark:border-gray-600">
                                            <img :src="previewUrl(file)" class="w-full h-full object-cover">
                                            <button type="button"
                                                    @click="removeNewFile(imageModalIndex, fi)"
                                                    class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template x-if="imageCount(imageModalIndex) === 0">
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">No images added yet.</p>
                        </template>

                        <!-- Add images button -->
                        <label :for="`variation_images_${imageModalIndex}`"
                               class="mt-2 flex items-center justify-center gap-2 w-full py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-400 transition text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add images
                        </label>
                    </div>
                </template>

                <div class="mt-4 flex justify-end">
                    <button type="button" @click="closeImageModal()"
                            class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             @click="showModal = false"></div>

        <!-- Modal content -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                 @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4" x-text="modalTitle"></h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" x-text="modalLabel"></label>
                    <input type="number"
                           x-model="modalValue"
                           x-ref="modalInput"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-primary-500 focus:border-primary-500"
                           @keydown.enter="confirmModal()">
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button"
                            @click="showModal = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmModal()"
                            class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
[x-cloak] { display: none !important; }
</style>
<script>
function variationManager() {
    return {
        variations: [],
        categoryVariants: [],
        variantsByLevel: {},
        selectedVariants: [],
        defaultVariationIndex: null,
        showModal: false,
        modalTitle: '',
        modalLabel: '',
        modalValue: '',
        modalAction: null,
        showImageModal: false,
        imageModalIndex: null,

        init() {
            // Load existing variations from global variable (edit mode)
            if (window.EDIT_VARIATIONS && window.EDIT_VARIATIONS.length > 0) {
                this.variations = window.EDIT_VARIATIONS;

                // Set default variation index
                const defaultIndex = this.variations.findIndex(v => v.is_default);
                if (defaultIndex !== -1) {
                    this.defaultVariationIndex = defaultIndex;
                }

                console.log('Loaded variations from global variable:', this.variations.length, 'variations');
            }

            // Listen for category changes
            window.addEventListener('category-changed', (event) => {
                console.log('Category changed event received:', event.detail);
                this.loadCategoryVariants(event.detail.categoryId, event.detail.level);
            });

            // Listen for "Basic Information" being selected as default
            window.addEventListener('basic-default-selected', () => {
                this.defaultVariationIndex = null;
            });
        },

        // Get only variants marked as "Main shown"
        mainShownVariants() {
            return this.categoryVariants.filter(v => v.is_main_shown === true);
        },

        async loadCategoryVariants(categoryId, level) {
            if (!categoryId) {
                this.categoryVariants = [];
                this.variantsByLevel = {};
                this.selectedVariants = [];
                return;
            }

            // Default level to 0 if not provided
            if (level === undefined || level === null) {
                level = 0;
            }

            // Clear variants for this level and any deeper levels
            const levelsToRemove = Object.keys(this.variantsByLevel)
                .map(Number)
                .filter(l => l >= level);
            levelsToRemove.forEach(l => delete this.variantsByLevel[l]);

            try {
                const response = await fetch(`/api/categories/${categoryId}/variants?show_all=true`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Store variants for this specific level
                    this.variantsByLevel[level] = data.variants;

                    // Rebuild categoryVariants by merging all levels (deduplicating by variant ID)
                    this.rebuildCategoryVariants();

                    // Dispatch event to signal variants are ready (for edit page preloading)
                    window.dispatchEvent(new CustomEvent('category-variants-loaded', {
                        detail: { categoryId, variants: this.categoryVariants }
                    }));
                }
            } catch (error) {
                console.error('Error loading category variants:', error);
                // Remove this level's entry on error
                delete this.variantsByLevel[level];
                this.rebuildCategoryVariants();
            }
        },

        rebuildCategoryVariants() {
            const seen = new Set();
            const merged = [];

            // Iterate levels in order (0, 1, 2, ...) so parent variants come first
            const sortedLevels = Object.keys(this.variantsByLevel).map(Number).sort((a, b) => a - b);
            for (const lvl of sortedLevels) {
                for (const variant of this.variantsByLevel[lvl]) {
                    if (!seen.has(variant.id)) {
                        seen.add(variant.id);
                        merged.push(variant);
                    }
                }
            }

            this.categoryVariants = merged;
            this.selectedVariants = merged.filter(v => v.is_required);
        },

        confirmRegenerateVariations() {
            if (this.variations.length > 0) {
                if (!confirm('WARNING: This will REPLACE all existing variations!\n\nAre you sure you want to regenerate all combinations? This action cannot be undone.\n\nExisting variations: ' + this.variations.length)) {
                    return;
                }
            }
            this.generateAllVariations();
        },

        generateAllVariations() {
            if (this.categoryVariants.length === 0) {
                alert('No variants loaded! Please select a category first.');
                return;
            }

            // Filter to only use "Main shown" variants for generating combinations
            const mainShownVariants = this.categoryVariants.filter(v => v.is_main_shown);

            if (mainShownVariants.length === 0) {
                const availableVariants = this.categoryVariants.map(v => v.name).join(', ');
                alert(`No "Main shown" variants found!\n\nThis category has ${this.categoryVariants.length} variant(s): ${availableVariants}\n\nBut none are marked as "Main shown variant". Please contact the administrator.`);
                return;
            }

            const combinations = this.generateCombinations(mainShownVariants);

            // First create variations with empty attributes
            this.variations = combinations.map(() => ({
                id: null,
                sku: '',
                attributes: {},
                price: '',
                discount_price: '',
                stock_quantity: 0,
                low_stock_threshold: 10,
                manage_stock: true,
                allow_backorder: false,
                images: [],
                existing_images: [],
                deleted_image_ids: [],
                is_default: false,
                is_active: true,
            }));

            this.defaultVariationIndex = null;

            // Wait for DOM to render then populate actual data
            this.$nextTick(() => {
                this.variations = combinations.map((combo) => ({
                    id: null,
                    sku: this.generateSKU(combo),
                    attributes: {...combo},
                    price: '',
                    discount_price: '',
                    stock_quantity: 0,
                    low_stock_threshold: 10,
                    manage_stock: true,
                    allow_backorder: false,
                    images: [],
                    existing_images: [],
                    deleted_image_ids: [],
                    is_default: false,
                    is_active: true,
                }));
            });
        },

        generateCombinations(variantsToUse = null) {
            const variants = variantsToUse || this.categoryVariants;
            if (variants.length === 0) return [];

            const combinations = [];

            function combine(index, current) {
                if (index === variants.length) {
                    combinations.push({...current});
                    return;
                }

                const variant = variants[index];
                variant.items.forEach(item => {
                    current[variant.id] = item.id;
                    combine(index + 1, current);
                });
            }

            combine(0, {});
            return combinations;
        },

        generateSKU(attributes) {
            const parts = [];

            // Add listing title prefix if available
            const titleInput = document.getElementById('title');
            if (titleInput && titleInput.value) {
                const titlePart = titleInput.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 4);
                parts.push(titlePart);
            }

            // Add variant values
            this.categoryVariants.forEach(variant => {
                const itemId = attributes[variant.id];
                if (itemId) {
                    const item = variant.items.find(i => i.id == itemId);
                    if (item) {
                        const value = item.value
                            .toUpperCase()
                            .replace(/[^A-Z0-9]/g, '')
                            .substring(0, 3);
                        parts.push(value);
                    }
                }
            });

            return parts.join('-');
        },

        updateSKU(index) {
            // Allow manual SKU editing
        },

        updateVariationDisplay(index) {
            const variation = this.variations[index];
            variation.sku = this.generateSKU(variation.attributes);
        },

        addManualVariation() {
            const newVariation = {
                id: null,
                sku: '',
                attributes: {},
                price: '',
                discount_price: '',
                stock_quantity: 0,
                low_stock_threshold: 10,
                manage_stock: true,
                allow_backorder: false,
                images: [],
                existing_images: [],
                deleted_image_ids: [],
                is_default: false,
                is_active: true,
            };

            this.variations.push(newVariation);
        },

        removeVariation(index) {
            if (!confirm('Are you sure you want to remove this variation?')) {
                return;
            }

            this.variations.splice(index, 1);

            // Update default if needed
            if (this.defaultVariationIndex >= this.variations.length) {
                this.defaultVariationIndex = Math.max(0, this.variations.length - 1);
            }
        },

        setDefaultVariation(index) {
            this.defaultVariationIndex = index;
            // Uncheck the "Basic Information" default radio if it exists
            const basicRadio = document.getElementById('default_basic');
            if (basicRadio) {
                basicRadio.checked = false;
            }
        },

        handleImageUpload(event, index) {
            const newFiles = Array.from(event.target.files);
            this.variations[index].images = [...(this.variations[index].images || []), ...newFiles];

            // Sync all accumulated files back into the input so they submit with the form
            const dt = new DataTransfer();
            this.variations[index].images.forEach(f => dt.items.add(f));
            event.target.files = dt.files;
        },

        openImageModal(index) {
            this.imageModalIndex = index;
            this.showImageModal = true;
        },

        closeImageModal() {
            this.showImageModal = false;
            this.imageModalIndex = null;
        },

        imageCount(index) {
            const v = this.variations[index];
            if (!v) return 0;
            const existingCount = (v.existing_images || []).filter(img =>
                !(v.deleted_image_ids || []).includes(img.id)
            ).length;
            return existingCount + (v.images || []).length;
        },

        toggleExistingImageDeletion(index, imgId) {
            const v = this.variations[index];
            if (!v.deleted_image_ids) { v.deleted_image_ids = []; }
            const pos = v.deleted_image_ids.indexOf(imgId);
            if (pos === -1) {
                v.deleted_image_ids.push(imgId);
            } else {
                v.deleted_image_ids.splice(pos, 1);
            }
        },

        removeNewFile(index, fileIndex) {
            this.variations[index].images.splice(fileIndex, 1);

            // Sync removal back to the file input
            const input = document.getElementById(`variation_images_${index}`);
            if (input) {
                const dt = new DataTransfer();
                (this.variations[index].images || []).forEach(f => dt.items.add(f));
                input.files = dt.files;
            }
        },

        previewUrl(file) {
            return URL.createObjectURL(file);
        },

        bulkSetPrice() {
            this.modalTitle = 'Set Price for All Variations';
            this.modalLabel = 'Enter price:';
            this.modalValue = '';
            this.modalAction = 'price';
            this.showModal = true;
            setTimeout(() => this.$refs.modalInput?.focus(), 100);
        },

        bulkSetStock() {
            this.modalTitle = 'Set Stock for All Variations';
            this.modalLabel = 'Enter stock quantity:';
            this.modalValue = '';
            this.modalAction = 'stock';
            this.showModal = true;
            setTimeout(() => this.$refs.modalInput?.focus(), 100);
        },

        confirmModal() {
            const value = parseFloat(this.modalValue);

            if (isNaN(value) || value < 0) {
                alert('Please enter a valid positive number');
                return;
            }

            if (this.modalAction === 'price') {
                this.variations.forEach(v => v.price = value);
            } else if (this.modalAction === 'stock') {
                this.variations.forEach(v => v.stock_quantity = parseInt(value));
            }

            this.showModal = false;
            this.modalValue = '';
        },

        clearAllVariations() {
            if (!confirm('Are you sure you want to clear all variations?')) {
                return;
            }

            this.variations = [];
            this.defaultVariationIndex = null;
        }
    }
}
</script>
@endpush
