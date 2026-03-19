@php
    $isEditMode = isset($mode) && $mode === 'edit';
    $hasExistingVariations = $isEditMode && isset($listing) && $listing->variations->count() > 0;
@endphp

<!-- Product Variations Manager -->
<div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden" x-data="variationManager()" x-init="init()">

    {{-- Card Header --}}
    <div class="flex items-center justify-between px-5 py-3 bg-[#37474F]">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-[#D4AF37] text-sm"></i>
            <h3 class="text-[13px] font-semibold text-white">Product Variations</h3>
            <p class="text-[11px] text-white/60 hidden sm:block">— manage variants with individual pricing and stock</p>
        </div>
        <div class="flex items-center gap-2">
            @if($isEditMode)
                <button type="button" @click="confirmRegenerateVariations()"
                        class="inline-flex items-center gap-1.5 h-[30px] px-3 bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition disabled:opacity-40"
                        :disabled="categoryVariants.length === 0">
                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                    Regenerate All
                </button>
            @else
                <button type="button" @click="generateAllVariations()"
                        class="inline-flex items-center gap-1.5 h-[30px] px-3 bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition disabled:opacity-40"
                        :disabled="categoryVariants.length === 0">
                    <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
                    Generate All
                </button>
            @endif
            <button type="button" @click="addManualVariation()"
                    class="inline-flex items-center gap-1.5 h-[30px] px-3 border border-white/30 text-white text-[12px] font-semibold rounded hover:bg-white/10 transition">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Add Manual
            </button>
        </div>
    </div>

    <div class="p-5">

        {{-- Edit mode warning --}}
        @if($hasExistingVariations)
            <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-yellow-50 border border-yellow-200 rounded text-[12px] text-yellow-700">
                <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5"></i>
                <span><strong>Edit Mode:</strong> You are editing existing variations. Use "Regenerate" carefully as it will replace all current variations.</span>
            </div>
        @endif

        {{-- Variant info banner --}}
        <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-blue-50 border border-blue-200 rounded text-[12px] text-blue-700"
             x-show="categoryVariants.length > 0" x-cloak>
            <i class="fa-solid fa-circle-info flex-shrink-0 mt-0.5"></i>
            <div>
                <strong>Available Variants:</strong>
                <span x-text="categoryVariants.map(v => v.name).join(', ')"></span>
                <p class="mt-0.5">Select variants below to generate all possible combinations automatically.</p>
            </div>
        </div>

        {{-- Variations Table --}}
        <div class="overflow-x-auto border border-[#E0E0E0] rounded" x-show="variations.length > 0" x-cloak>
            <table class="min-w-full">
                <thead>
                    <tr class="bg-[#37474F]">
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">SKU</th>
                        <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">
                            <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap" x-text="variant.name"></th>
                        </template>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Price</th>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Discount</th>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Wholesale</th>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Stock</th>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Images</th>
                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Default</th>
                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Active</th>
                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    <template x-for="(variation, index) in variations" :key="index">
                        <tr class="hover:bg-[#F5F5F5] transition">
                            <!-- SKU -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <input type="text"
                                       :name="`variations[${index}][sku]`"
                                       x-model="variation.sku"
                                       @input="updateSKU(index)"
                                       placeholder="Auto"
                                       class="w-28 h-[30px] px-2 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] placeholder-[#37474F]/40 focus:border-[#D4AF37] focus:outline-none transition">
                            </td>

                            <!-- Variant Values -->
                            <template x-for="variant in categoryVariants.filter(v => v.is_main_shown)" :key="variant.id">
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <select :name="`variations[${index}][attributes][${variant.id}]`"
                                            x-model="variation.attributes[variant.id]"
                                            @change="updateVariationDisplay(index)"
                                            class="h-[30px] px-2 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                        <option value="">Select</option>
                                        <template x-for="item in variant.items" :key="item.id">
                                            <option :value="item.id" x-text="item.display_value || item.value"></option>
                                        </template>
                                    </select>
                                </td>
                            </template>

                            <!-- Price -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <input type="number"
                                       :name="`variations[${index}][price]`"
                                       x-model="variation.price"
                                       step="0.01"
                                       placeholder="0.00"
                                       required
                                       class="w-24 h-[30px] px-2 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] placeholder-[#37474F]/40 focus:border-[#D4AF37] focus:outline-none transition">
                            </td>

                            <!-- Discount -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <button type="button"
                                        @click="openDiscountModal(index)"
                                        :class="variation.discount_price
                                            ? 'bg-green-50 text-green-700 border-green-300'
                                            : 'bg-white text-[#37474F] border-[#E0E0E0] hover:border-[#D4AF37]'"
                                        class="inline-flex items-center gap-1 h-[26px] px-2 text-[11px] font-semibold border rounded transition whitespace-nowrap">
                                    <i class="fa-solid fa-tag text-[9px]"></i>
                                    <span x-text="variation.discount_price ? 'Set ✓' : 'Discount'"></span>
                                </button>
                                <input type="hidden" :name="`variations[${index}][discount_price]`" :value="variation.discount_price || ''">
                                <input type="hidden" :name="`variations[${index}][discount_start_date]`" :value="variation.discount_start_date || ''">
                                <input type="hidden" :name="`variations[${index}][discount_end_date]`" :value="variation.discount_end_date || ''">
                            </td>

                            <!-- Wholesale -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <button type="button"
                                        @click="openWholesaleModal(index)"
                                        :class="variation.is_wholesale
                                            ? 'bg-blue-50 text-blue-700 border-blue-300'
                                            : 'bg-white text-[#37474F] border-[#E0E0E0] hover:border-[#D4AF37]'"
                                        class="inline-flex items-center gap-1 h-[26px] px-2 text-[11px] font-semibold border rounded transition">
                                    <i class="fa-solid fa-boxes-stacked text-[9px]"></i>
                                    <span x-text="variation.is_wholesale ? 'Set ✓' : 'Wholesale'"></span>
                                </button>
                                <input type="hidden" :name="`variations[${index}][is_wholesale]`" :value="variation.is_wholesale ? 1 : 0">
                                <input type="hidden" :name="`variations[${index}][wholesale_min_order_qty]`" :value="variation.wholesale_min_order_qty || ''">
                                <input type="hidden" :name="`variations[${index}][wholesale_qty_increment]`" :value="variation.wholesale_qty_increment || 1">
                                <input type="hidden" :name="`variations[${index}][wholesale_sample_available]`" :value="variation.wholesale_sample_available ? 1 : 0">
                                <input type="hidden" :name="`variations[${index}][wholesale_sample_price]`" :value="variation.wholesale_sample_price || ''">
                                <input type="hidden" :name="`variations[${index}][wholesale_terms]`" :value="variation.wholesale_terms || ''">
                            </td>

                            <!-- Stock -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <input type="number"
                                       :name="`variations[${index}][stock_quantity]`"
                                       x-model="variation.stock_quantity"
                                       placeholder="0"
                                       class="w-20 h-[30px] px-2 bg-white border border-[#E0E0E0] rounded text-[12px] text-[#1A1A1A] placeholder-[#37474F]/40 focus:border-[#D4AF37] focus:outline-none transition">
                            </td>

                            <!-- Images -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            @click="openImageModal(index)"
                                            class="inline-flex items-center gap-1 h-[26px] px-2 text-[11px] font-semibold border border-[#E0E0E0] rounded hover:border-[#D4AF37] text-[#37474F] transition">
                                        <i class="fa-solid fa-image text-[9px]"></i>
                                        <span x-text="imageCount(index) > 0 ? imageCount(index) + ' img' : 'Upload'"></span>
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
                            <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                <input type="radio"
                                       name="default_variation"
                                       :value="index"
                                       x-model="defaultVariationIndex"
                                       @change="setDefaultVariation(index)"
                                       class="w-4 h-4 accent-[#D4AF37]">
                                <input type="hidden"
                                       :name="`variations[${index}][is_default]`"
                                       :value="defaultVariationIndex === index ? 1 : 0">
                            </td>

                            <!-- Active -->
                            <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                <input type="checkbox"
                                       :name="`variations[${index}][is_active]`"
                                       x-model="variation.is_active"
                                       value="1"
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                            </td>

                            <!-- Actions -->
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <button type="button"
                                        @click="removeVariation(index)"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded text-[#E0E0E0] hover:text-red-500 hover:bg-red-50 transition">
                                    <i class="fa-solid fa-trash text-xs"></i>
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

        {{-- Empty State --}}
        <div class="py-12 text-center" x-show="variations.length === 0" x-cloak>
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                <i class="fa-solid fa-layer-group text-[#37474F] text-lg"></i>
            </div>
            <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No variations yet</p>
            <p class="text-[13px] text-[#37474F]">Select a category with variants to generate combinations,<br>or add individual variations manually.</p>
        </div>

        {{-- Bulk Actions --}}
        <div class="mt-4 flex items-center gap-2 flex-wrap" x-show="variations.length > 0" x-cloak>
            <button type="button"
                    @click="bulkSetPrice()"
                    class="inline-flex items-center gap-1.5 h-[30px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                <i class="fa-solid fa-dollar-sign text-[10px]"></i>
                Set Price for All
            </button>
            <button type="button"
                    @click="bulkSetStock()"
                    class="inline-flex items-center gap-1.5 h-[30px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                <i class="fa-solid fa-cubes text-[10px]"></i>
                Set Stock for All
            </button>
            <button type="button"
                    @click="clearAllVariations()"
                    class="inline-flex items-center gap-1.5 h-[30px] px-3 border border-red-200 text-red-600 text-[12px] font-semibold rounded hover:bg-red-50 transition">
                <i class="fa-solid fa-trash text-[10px]"></i>
                Clear All
            </button>
            <div class="flex-1"></div>
            <span class="text-[12px] text-[#37474F]">
                Total: <strong class="text-[#1A1A1A]" x-text="variations.length"></strong> variation(s)
            </span>
        </div>

    </div>

    <!-- Hidden field for variation count -->
    <input type="hidden" name="variation_count" :value="variations.length">

    <!-- ── Image Manager Modal ──────────────────────────────────────────── -->
    <div x-show="showImageModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="closeImageModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden" @click.stop>
                <div class="flex items-center justify-between px-5 py-3 bg-[#37474F]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-images text-[#D4AF37] text-sm"></i>
                        <h3 class="text-[13px] font-semibold text-white">Variation Images</h3>
                    </div>
                    <button type="button" @click="closeImageModal()"
                            class="text-white/60 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="p-5">
                    <template x-if="imageModalIndex !== null">
                        <div>
                            <!-- Existing server images -->
                            <template x-if="variations[imageModalIndex].existing_images && variations[imageModalIndex].existing_images.length > 0">
                                <div class="mb-4">
                                    <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">Saved Images</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="img in variations[imageModalIndex].existing_images" :key="img.id">
                                            <div class="relative w-20 h-20 rounded overflow-hidden border border-[#E0E0E0]"
                                                 :class="variations[imageModalIndex].deleted_image_ids.includes(img.id) ? 'opacity-30' : ''">
                                                <img :src="img.url" class="w-full h-full object-cover">
                                                <button type="button"
                                                        @click="toggleExistingImageDeletion(imageModalIndex, img.id)"
                                                        class="absolute top-0.5 right-0.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                                        :class="variations[imageModalIndex].deleted_image_ids.includes(img.id) ? 'bg-green-500' : 'bg-red-500'">
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
                                    <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-2">New Uploads</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(file, fi) in variations[imageModalIndex].images" :key="fi">
                                            <div class="relative w-20 h-20 rounded overflow-hidden border border-[#E0E0E0]">
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
                                <p class="text-[12px] text-[#37474F] text-center py-4">No images added yet.</p>
                            </template>

                            <!-- Add images button -->
                            <label :for="`variation_images_${imageModalIndex}`"
                                   class="mt-2 flex items-center justify-center gap-2 w-full py-3 border-2 border-dashed border-[#E0E0E0] rounded cursor-pointer hover:border-[#D4AF37] transition text-[12px] text-[#37474F]">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                Add images
                            </label>
                        </div>
                    </template>
                </div>
                <div class="px-5 py-3 border-t border-[#E0E0E0] flex justify-end">
                    <button type="button" @click="closeImageModal()"
                            class="inline-flex items-center h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Discount Modal ──────────────────────────────────────────────── -->
    <div x-show="showDiscountModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="closeDiscountModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden" @click.stop>
                <div class="flex items-center justify-between px-5 py-3 bg-[#37474F]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-tag text-[#D4AF37] text-sm"></i>
                        <h3 class="text-[13px] font-semibold text-white">Set Discount</h3>
                    </div>
                    <button type="button" @click="closeDiscountModal()"
                            class="text-white/60 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="p-5">
                    <template x-if="discountModalIndex !== null">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Discount Price</label>
                                <input type="number"
                                       x-model="variations[discountModalIndex].discount_price"
                                       step="0.01" min="0"
                                       placeholder="Enter discounted price"
                                       class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Start Date</label>
                                    <input type="date"
                                           x-model="variations[discountModalIndex].discount_start_date"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">End Date</label>
                                    <input type="date"
                                           x-model="variations[discountModalIndex].discount_end_date"
                                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="px-5 py-3 border-t border-[#E0E0E0] flex items-center justify-between">
                    <button type="button"
                            @click="clearDiscount(discountModalIndex)"
                            class="inline-flex items-center gap-1.5 h-[34px] px-4 border border-red-200 text-red-600 text-[13px] font-semibold rounded hover:bg-red-50 transition">
                        <i class="fa-solid fa-trash text-xs"></i>
                        Clear
                    </button>
                    <button type="button"
                            @click="closeDiscountModal()"
                            class="inline-flex items-center h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Wholesale Modal ─────────────────────────────────────────────── -->
    <div x-show="showWholesaleModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="closeWholesaleModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden" @click.stop>
                <div class="flex items-center justify-between px-5 py-3 bg-[#37474F]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-[#D4AF37] text-sm"></i>
                        <h3 class="text-[13px] font-semibold text-white">Wholesale Settings</h3>
                    </div>
                    <button type="button" @click="closeWholesaleModal()"
                            class="text-white/60 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="p-5">
                    <template x-if="wholesaleModalIndex !== null">
                        <div class="space-y-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       x-model="variations[wholesaleModalIndex].is_wholesale"
                                       :id="`wholesale_toggle_${wholesaleModalIndex}`"
                                       class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[13px] font-semibold text-[#1A1A1A]">Enable wholesale for this variant</span>
                            </label>
                            <template x-if="variations[wholesaleModalIndex].is_wholesale">
                                <div class="space-y-4 pt-3 border-t border-[#E0E0E0]">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Min Order Qty</label>
                                            <input type="number"
                                                   id="wholesale_min_order_qty"
                                                   x-model="variations[wholesaleModalIndex].wholesale_min_order_qty"
                                                   min="1" placeholder="e.g. 10"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Qty Increment</label>
                                            <input type="number"
                                                   id="wholesale_qty_increment"
                                                   x-model="variations[wholesaleModalIndex].wholesale_qty_increment"
                                                   min="1" placeholder="e.g. 5"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox"
                                               x-model="variations[wholesaleModalIndex].wholesale_sample_available"
                                               :id="`sample_toggle_${wholesaleModalIndex}`"
                                               class="w-4 h-4 rounded accent-[#D4AF37]">
                                        <span class="text-[13px] text-[#1A1A1A]">Samples available</span>
                                    </label>
                                    <template x-if="variations[wholesaleModalIndex].wholesale_sample_available">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Sample Price</label>
                                            <input type="number"
                                                   id="wholesale_sample_price"
                                                   x-model="variations[wholesaleModalIndex].wholesale_sample_price"
                                                   step="0.01" min="0" placeholder="0.00"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                        </div>
                                    </template>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Wholesale Terms</label>
                                        <textarea id="wholesale_terms"
                                                  x-model="variations[wholesaleModalIndex].wholesale_terms"
                                                  rows="3"
                                                  placeholder="Describe wholesale terms, requirements, or conditions..."
                                                  class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="px-5 py-3 border-t border-[#E0E0E0] flex justify-end">
                    <button type="button"
                            @click="closeWholesaleModal()"
                            class="inline-flex items-center h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Bulk Action Modal ───────────────────────────────────────────── -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="fixed inset-0 bg-black/50 transition-opacity"
             @click="showModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden"
                 @click.stop>
                <div class="flex items-center justify-between px-5 py-3 bg-[#37474F]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-[#D4AF37] text-sm"></i>
                        <h3 class="text-[13px] font-semibold text-white" x-text="modalTitle"></h3>
                    </div>
                    <button type="button" @click="showModal = false"
                            class="text-white/60 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="p-5">
                    <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5" x-text="modalLabel"></label>
                    <input type="number"
                           x-model="modalValue"
                           x-ref="modalInput"
                           step="0.01"
                           min="0"
                           class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition"
                           @keydown.enter="confirmModal()">
                </div>
                <div class="px-5 py-3 border-t border-[#E0E0E0] flex gap-3 justify-end">
                    <button type="button"
                            @click="showModal = false"
                            class="inline-flex items-center h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmModal()"
                            class="inline-flex items-center h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
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
        showDiscountModal: false,
        discountModalIndex: null,
        showWholesaleModal: false,
        wholesaleModalIndex: null,

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
                discount_start_date: '',
                discount_end_date: '',
                stock_quantity: 0,
                low_stock_threshold: 10,
                manage_stock: true,
                allow_backorder: false,
                images: [],
                existing_images: [],
                deleted_image_ids: [],
                is_default: false,
                is_active: true,
                is_wholesale: false,
                wholesale_min_order_qty: '',
                wholesale_qty_increment: 1,
                wholesale_sample_available: false,
                wholesale_sample_price: '',
                wholesale_terms: '',
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
                    discount_start_date: '',
                    discount_end_date: '',
                    stock_quantity: 0,
                    low_stock_threshold: 10,
                    manage_stock: true,
                    allow_backorder: false,
                    images: [],
                    existing_images: [],
                    deleted_image_ids: [],
                    is_default: false,
                    is_active: true,
                    is_wholesale: false,
                    wholesale_min_order_qty: '',
                    wholesale_qty_increment: 1,
                    wholesale_lead_time_days: '',
                    wholesale_sample_available: false,
                    wholesale_sample_price: '',
                    wholesale_terms: '',
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
                discount_start_date: '',
                discount_end_date: '',
                stock_quantity: 0,
                low_stock_threshold: 10,
                manage_stock: true,
                allow_backorder: false,
                images: [],
                existing_images: [],
                deleted_image_ids: [],
                is_default: false,
                is_active: true,
                is_wholesale: false,
                wholesale_min_order_qty: '',
                wholesale_qty_increment: 1,
                wholesale_sample_available: false,
                wholesale_sample_price: '',
                wholesale_terms: '',
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

        openDiscountModal(index) {
            this.discountModalIndex = index;
            this.showDiscountModal = true;
        },

        closeDiscountModal() {
            this.showDiscountModal = false;
            this.discountModalIndex = null;
        },

        clearDiscount(index) {
            this.variations[index].discount_price = '';
            this.variations[index].discount_start_date = '';
            this.variations[index].discount_end_date = '';
        },

        openWholesaleModal(index) {
            this.wholesaleModalIndex = index;
            this.showWholesaleModal = true;
        },

        closeWholesaleModal() {
            this.showWholesaleModal = false;
            this.wholesaleModalIndex = null;
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
