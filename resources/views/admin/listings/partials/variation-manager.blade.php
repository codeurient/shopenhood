@php
    $isEditMode = isset($mode) && $mode === 'edit';
    $hasExistingVariations = $isEditMode && isset($listing) && $listing->variations->count() > 0;
@endphp

<!-- Product Variations Manager -->
<div class="bg-white rounded-lg shadow p-6" x-data="variationManager()" x-init="init()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Product Variations</h3>
            <p class="text-sm text-gray-600 mt-1">Manage different variants (colors, sizes, etc.) with individual pricing and stock</p>
            @if($hasExistingVariations)
                <p class="text-xs text-amber-600 mt-2">
                    ⚠️ <strong>Edit Mode:</strong> You are editing existing variations. Use "Regenerate" carefully as it will replace all current variations.
                </p>
            @endif
        </div>
        <div class="flex gap-2">
            @if($isEditMode)
                <button type="button" @click="confirmRegenerateVariations()"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition"
                        :disabled="categoryVariants.length === 0">
                    ⚠️ Regenerate All Combinations
                </button>
            @else
                <button type="button" @click="generateAllVariations()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                        :disabled="categoryVariants.length === 0">
                    ⚡ Generate All Combinations
                </button>
            @endif
            <button type="button" @click="addManualVariation()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                ➕ Add Manual Variation
            </button>
        </div>
    </div>

    <!-- Variant Selection Info -->
    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded" x-show="categoryVariants.length > 0" x-cloak>
        <p class="text-sm text-blue-700">
            <strong>Available Variants for this category:</strong>
            <span x-text="categoryVariants.map(v => v.name).join(', ')"></span>
        </p>
        <p class="text-xs text-blue-600 mt-1" x-show="categoryVariants.length > 0">
            Select variants below to generate all possible combinations automatically.
        </p>
    </div>

    <!-- Variations Table -->
    <div class="overflow-x-auto" x-show="variations.length > 0" x-cloak>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" x-text="variant.name"></th>
                    </template>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Images</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(variation, index) in variations" :key="index">
                    <tr class="hover:bg-gray-50">
                        <!-- SKU -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="text"
                                   :name="`variations[${index}][sku]`"
                                   x-model="variation.sku"
                                   @input="updateSKU(index)"
                                   placeholder="Auto-generate"
                                   class="w-32 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        </td>

                        <!-- Variant Values -->
                        <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">

                            <td class="px-4 py-3 whitespace-nowrap">
                                <select :name="`variations[${index}][attributes][${variant.id}]`"
                                        x-model="variation.attributes[variant.id]"
                                        @change="updateVariationDisplay(index)"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
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
                                   class="w-24 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        </td>

                        <!-- Discount Price -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="number"
                                   :name="`variations[${index}][discount_price]`"
                                   x-model="variation.discount_price"
                                   step="0.01"
                                   placeholder="Optional"
                                   class="w-24 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        </td>

                        <!-- Stock -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="number"
                                   :name="`variations[${index}][stock_quantity]`"
                                   x-model="variation.stock_quantity"
                                   placeholder="0"
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                        </td>

                        <!-- Images -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <label :for="`variation_images_${index}`" class="cursor-pointer">
                                    <span class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition">
                                        <span x-show="!variation.images || variation.images.length === 0">📁 Upload</span>
                                        <span x-show="variation.images && variation.images.length > 0"
                                              x-text="`${variation.images.length} 📷`"></span>
                                    </span>
                                </label>
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
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
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
                                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button type="button"
                                    @click="removeVariation(index)"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                🗑️ Delete
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
    <div class="text-center py-12 bg-gray-50 rounded-lg" x-show="variations.length === 0" x-cloak>
        <div class="text-6xl mb-4">📦</div>
        <p class="text-gray-500 text-lg mb-4">No variations created yet</p>
        <p class="text-sm text-gray-400 mb-6">
            Select a category with variants to automatically generate combinations,<br>
            or manually add individual variations
        </p>
    </div>

    <!-- Bulk Actions -->
    <div class="mt-6 flex gap-4" x-show="variations.length > 0" x-cloak>
        <button type="button"
                @click="bulkSetPrice()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
            💰 Set Price for All
        </button>
        <button type="button"
                @click="bulkSetStock()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
            📦 Set Stock for All
        </button>
        <button type="button"
                @click="clearAllVariations()"
                class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
            🗑️ Clear All
        </button>
        <div class="flex-1"></div>
        <div class="text-sm text-gray-600 flex items-center">
            Total Variations: <strong class="ml-2" x-text="variations.length"></strong>
        </div>
    </div>

    <!-- Hidden field for variation count -->
    <input type="hidden" name="variation_count" :value="variations.length">

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
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                 @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="modalTitle"></h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="modalLabel"></label>
                    <input type="number"
                           x-model="modalValue"
                           x-ref="modalInput"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           @keydown.enter="confirmModal()">
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button"
                            @click="showModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmModal()"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
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

        init() {
            // VANILLA JS APPROACH: Load existing variations from global variable
            // This bypasses all Alpine timing/initialization issues
            if (window.EDIT_VARIATIONS && window.EDIT_VARIATIONS.length > 0) {
                this.variations = window.EDIT_VARIATIONS;

                // Set default variation index
                const defaultIndex = this.variations.findIndex(v => v.is_default);
                if (defaultIndex !== -1) {
                    this.defaultVariationIndex = defaultIndex;
                }

                console.log('✓ Loaded variations from global variable:', this.variations.length, 'variations');
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

        // Get only variants marked as "Main shown" - ensures consistent filtering
        mainShownVariants() {
            return this.categoryVariants.filter(v => v.is_main_shown === true);
        },

        async loadCategoryVariants(categoryId, level) {
            if (!categoryId) {
                this.categoryVariants = [];
                this.variantsByLevel = {};
                this.selectedVariants = [];
                console.log('⚠️ No category ID provided');
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

            console.log('🔄 Loading variants for category ID:', categoryId, 'at level:', level);

            try {
                // Load ALL variants (not just main shown) so existing variations can display properly
                const response = await fetch(`/admin/listings/category/${categoryId}/variants?show_all=true`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('📦 API Response:', data);

                if (data.success) {
                    // Store variants for this specific level
                    this.variantsByLevel[level] = data.variants;

                    // Rebuild categoryVariants by merging all levels (deduplicating by variant ID)
                    this.rebuildCategoryVariants();

                    const mainShownCount = this.categoryVariants.filter(v => v.is_main_shown).length;
                    console.log(`✓ Loaded ${data.variants.length} variants for level ${level} (total across all levels: ${this.categoryVariants.length}, ${mainShownCount} marked as "Main shown")`);

                    // Log which variants are main shown
                    const mainShownNames = this.categoryVariants
                        .filter(v => v.is_main_shown)
                        .map(v => v.name)
                        .join(', ');

                    if (mainShownNames) {
                        console.log('  Main shown variants:', mainShownNames);
                    } else {
                        console.warn('⚠️ No variants are marked as "Main shown" - Generate combinations will not work');
                    }

                    // Dispatch event to signal variants are ready (for edit page preloading)
                    window.dispatchEvent(new CustomEvent('category-variants-loaded', {
                        detail: { categoryId, variants: this.categoryVariants }
                    }));
                } else {
                    console.error('❌ API returned success: false');
                }
            } catch (error) {
                console.error('❌ Error loading category variants:', error);
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
                if (!confirm('⚠️ WARNING: This will REPLACE all existing variations!\n\nAre you sure you want to regenerate all combinations? This action cannot be undone.\n\nExisting variations: ' + this.variations.length)) {
                    return;
                }
            }
            this.generateAllVariations();
        },

        generateAllVariations() {
            console.log('🎯 Generate all variations clicked');
            console.log('📋 Total category variants:', this.categoryVariants.length);

            if (this.categoryVariants.length === 0) {
                alert('⚠️ No variants loaded!\n\nPlease select a category first. The category must have variants assigned to it.');
                console.error('❌ categoryVariants is empty - user needs to select a category');
                return;
            }

            // Filter to only use "Main shown" variants for generating combinations
            const mainShownVariants = this.categoryVariants.filter(v => v.is_main_shown);
            console.log('✨ Main shown variants:', mainShownVariants.length, mainShownVariants.map(v => v.name));

            if (mainShownVariants.length === 0) {
                const availableVariants = this.categoryVariants.map(v => v.name).join(', ');
                alert(`⚠️ No "Main shown" variants found!\n\nThis category has ${this.categoryVariants.length} variant(s): ${availableVariants}\n\nBut none are marked as "Main shown variant".\n\nPlease go to Categories → Edit Category → Manage Variants and check the "Main shown" checkbox for the variants you want to use for product combinations.`);
                console.error('❌ No main shown variants. Available variants:', this.categoryVariants);
                return;
            }

            const combinations = this.generateCombinations(mainShownVariants);
            console.log('Generated combinations:', combinations);

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
                is_default: false,
                is_active: true,
            }));

            this.defaultVariationIndex = null;

            // Wait for DOM to render (rows, selects, and options)
            this.$nextTick(() => {
                // Now populate the actual data
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
                is_default: false,
                is_active: true,
            };

            this.variations.push(newVariation);

            // Don't automatically set first as default - let user choose
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
            const files = Array.from(event.target.files);
            this.variations[index].images = files;
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
