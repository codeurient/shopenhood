@extends('admin.layouts.app')

@section('title', 'Edit Item: ' . $variantItem->value)
@section('page-title', 'Edit Variant Item')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Item in "{{ $variant->name }}"</h2>
            <p class="text-[#37474F] text-sm mt-1">Type: <span class="font-medium">{{ ucfirst($variant->type) }}</span></p>
        </div>
        <a href="{{ route('admin.variants.items.index', $variant) }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Items
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
            <div>
                <h3 class="text-[13px] font-semibold text-red-800">There were errors with your submission</h3>
                <ul class="mt-1 text-[13px] text-red-700 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <form action="{{ route('admin.variants.items.update', [$variant, $variantItem]) }}" method="POST" enctype="multipart/form-data" id="itemForm">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">

                <!-- Item Value -->
                <div>
                    <label for="value" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Item Value <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="value" id="value"
                           value="{{ old('value', $variantItem->value) }}"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('value') border-red-500 @else border-[#E0E0E0] @enderror"
                           placeholder="e.g., Small, Red, 64GB"
                           required autofocus>
                    <p class="mt-1 text-[12px] text-[#37474F]">The actual value stored in the database</p>
                    @error('value')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Display Value -->
                <div>
                    <label for="display_value" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Display Value
                    </label>
                    <input type="text" name="display_value" id="display_value"
                           value="{{ old('display_value', $variantItem->display_value) }}"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('display_value') border-red-500 @else border-[#E0E0E0] @enderror"
                           placeholder="Optional formatted display text">
                    <p class="mt-1 text-[12px] text-[#37474F]">Human-friendly label (e.g., "Extra Large" instead of "XL")</p>
                    @error('display_value')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Visual Options (select, radio, checkbox only) -->
                @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                    <div class="bg-gray-50 border border-[#E0E0E0] rounded-[6px] p-4">
                        <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-palette text-[#D4AF37]"></i>
                            Visual Options <span class="text-[12px] font-normal text-[#37474F]">(Optional)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Color Code -->
                            <div>
                                <label for="color_code" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Color Code
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="color_code" id="color_code"
                                           value="{{ old('color_code', $variantItem->color_code ?? '#000000') }}"
                                           class="w-[34px] h-[34px] border border-[#E0E0E0] rounded cursor-pointer p-0.5">
                                    <input type="text" id="color_code_text"
                                           value="{{ old('color_code', $variantItem->color_code) }}"
                                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] flex-1 px-3 @error('color_code') border-red-500 @enderror"
                                           placeholder="#000000" readonly>
                                </div>
                                <p class="mt-1 text-[12px] text-[#37474F]">Useful for color variants (e.g., Red, Blue)</p>
                                @error('color_code')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label for="image" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Item Image
                                </label>
                                @if($variantItem->image)
                                    <div class="mb-2 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $variantItem->image) }}" alt="{{ $variantItem->value }}"
                                             class="w-12 h-12 object-cover rounded border border-[#E0E0E0]">
                                        <span class="text-[12px] text-[#37474F]">Current image</span>
                                    </div>
                                @endif
                                <input type="file" name="image" id="image" accept="image/*"
                                       class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-white focus:outline-none focus:border-[#D4AF37] @error('image') border-red-500 @enderror p-0">
                                <p class="mt-1 text-[12px] text-[#37474F]">Max 1MB, JPEG/PNG/WebP. Leave empty to keep current.</p>
                                @error('image')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Sort Order
                    </label>
                    <input type="number" name="sort_order" id="sort_order"
                           value="{{ old('sort_order', $variantItem->sort_order) }}" min="0"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('sort_order') border-red-500 @else border-[#E0E0E0] @enderror">
                    <p class="mt-1 text-[12px] text-[#37474F]">Display order (lower numbers appear first)</p>
                    @error('sort_order')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $variantItem->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">Active</span>
                    </label>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-[#E0E0E0] flex justify-end gap-3">
                <a href="{{ route('admin.variants.items.index', $variant) }}"
                   class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Update Item
                </button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('color_code');
    const colorText = document.getElementById('color_code_text');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });
    }

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 1024 * 1024) {
                    alert('Image must be less than 1MB');
                    this.value = '';
                    imagePreview.innerHTML = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" class="w-16 h-16 object-cover rounded border border-[#E0E0E0]">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '';
            }
        });
    }
});
</script>
@endsection
