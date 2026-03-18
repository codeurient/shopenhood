@extends('admin.layouts.app')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Create New Category</h2>
            <p class="text-[#37474F] text-sm mt-1">Add a new category to organize your products</p>
        </div>
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Categories
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
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf

            <div class="p-6 space-y-6">

                <!-- Hierarchy Section -->
                <div class="pb-6 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-folder text-[#D4AF37]"></i>
                        Category Hierarchy
                    </h3>
                    <div>
                        <label class="block text-[13px] font-medium text-[#1A1A1A] mb-2">
                            Parent Category
                        </label>
                        <div id="category-selects-container" class="space-y-3">
                            <select name="parent_id" id="category_level_0"
                                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 category-select"
                                    data-level="0">
                                <option value="">Root Category</option>
                            </select>
                        </div>
                        <input type="hidden" name="parent_id" id="parent_id_hidden" value="">
                        <p class="mt-2 text-[12px] text-[#37474F] flex items-center gap-1">
                            <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                            Select parent categories in order. Leave at root to create a top-level category.
                        </p>
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="pb-6 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Category Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('name') border-red-500 @else border-[#E0E0E0] @enderror"
                                   placeholder="e.g., Electronics, Clothing, Home & Garden"
                                   required autofocus>
                            @error('name')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <label for="slug" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                URL Slug
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-link text-[#37474F] text-xs"></i>
                                </div>
                                <input type="text" id="slug" name="slug"
                                       value="{{ old('slug') }}"
                                       class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full pl-8 pr-3 @error('slug') border-red-500 @else border-[#E0E0E0] @enderror"
                                       placeholder="auto-generated-if-empty">
                            </div>
                            <p class="mt-1 text-[12px] text-[#37474F] flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[#D4AF37]"></i>
                                Auto-generated from name if left empty
                            </p>
                            @error('slug')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border focus:ring-0 focus:border-[#D4AF37] @error('description') border-red-500 @else border-[#E0E0E0] @enderror"
                                      placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Visual Identity Section -->
                <div class="pb-6 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-image text-[#D4AF37]"></i>
                        Visual Identity
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Icon -->
                        <div>
                            <label for="icon" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Icon Code
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-icons text-[#37474F] text-xs"></i>
                                </div>
                                <input type="text" id="icon" name="icon"
                                       value="{{ old('icon') }}"
                                       class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full pl-8 pr-3 @error('icon') border-red-500 @else border-[#E0E0E0] @enderror"
                                       placeholder="e.g., fa-laptop, fa-home">
                            </div>
                            <p class="mt-1 text-[12px] text-[#37474F]">Use a Font Awesome icon class (e.g., fa-laptop)</p>
                            @error('icon')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]" for="image">
                                Category Image
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-white focus:outline-none focus:border-[#D4AF37] @error('image') border-red-500 @enderror p-0">
                            <p class="mt-1 text-[12px] text-[#37474F]">PNG, JPG, WEBP (MAX. 2MB)</p>
                            @error('image')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Sort Order
                            </label>
                            <input type="number" id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', 0) }}" min="0"
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('sort_order') border-red-500 @else border-[#E0E0E0] @enderror"
                                   placeholder="0">
                            <p class="mt-1 text-[12px] text-[#37474F]">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="pb-6 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-[#D4AF37]"></i>
                        SEO Settings
                    </h3>
                    <div class="grid grid-cols-1 gap-5">

                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Meta Title
                            </label>
                            <input type="text" id="meta_title" name="meta_title" maxlength="60"
                                   value="{{ old('meta_title') }}"
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('meta_title') border-red-500 @else border-[#E0E0E0] @enderror"
                                   placeholder="Optimized title for search engines">
                            <p class="mt-1 text-[12px] text-[#37474F]">
                                <span id="meta_title_count">0</span>/60 characters
                            </p>
                            @error('meta_title')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Meta Description
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"
                                      class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border focus:ring-0 focus:border-[#D4AF37] @error('meta_description') border-red-500 @else border-[#E0E0E0] @enderror"
                                      placeholder="Brief description for search engine results...">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-[12px] text-[#37474F]">
                                <span id="meta_desc_count">0</span>/160 characters
                            </p>
                            @error('meta_description')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div>
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-toggle-on text-[#D4AF37]"></i>
                        Status
                    </h3>
                    <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">Active (visible to users)</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-[#E0E0E0] flex justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('category-selects-container');
    const parentIdHidden = document.getElementById('parent_id_hidden');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescInput = document.getElementById('meta_description');

    // Load root categories on page load
    loadCategoriesForLevel(0, null);

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
            slugInput.value = generateSlug(this.value);
            slugInput.dataset.autoGenerated = 'true';
        }
    });

    slugInput.addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });

    // Character counters for SEO fields
    metaTitleInput.addEventListener('input', function() {
        document.getElementById('meta_title_count').textContent = this.value.length;
    });

    metaDescInput.addEventListener('input', function() {
        document.getElementById('meta_desc_count').textContent = this.value.length;
    });

    function loadCategoriesForLevel(level, parentId) {
        const url = parentId
            ? '{{ route("admin.categories.ajax.children", ":id") }}'.replace(':id', parentId)
            : '{{ route("admin.categories.ajax.children") }}';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.categories.length > 0) {
                const currentSelect = document.getElementById('category_level_' + level);

                currentSelect.innerHTML = '<option value="">Select Category</option>';

                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    option.dataset.hasChildren = category.children && category.children.length > 0;
                    currentSelect.appendChild(option);
                });

                const oldValue = '{{ old("parent_id") }}';
                if (oldValue && level === 0) {
                    restoreOldSelection(oldValue);
                }
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
    }

    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('category-select')) {
            const currentLevel = parseInt(e.target.dataset.level);
            const selectedCategoryId = e.target.value;
            const selectedOption = e.target.options[e.target.selectedIndex];

            removeSelectsAfterLevel(currentLevel);

            if (selectedCategoryId) {
                parentIdHidden.value = selectedCategoryId;
                const hasChildren = selectedOption.dataset.hasChildren === 'true';
                if (hasChildren) {
                    createNextLevelSelect(currentLevel + 1, selectedCategoryId);
                }
            } else {
                parentIdHidden.value = '';
            }
        }
    });

    function createNextLevelSelect(level, parentId) {
        const newSelect = document.createElement('select');
        newSelect.id = 'category_level_' + level;
        newSelect.className = 'h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 category-select';
        newSelect.dataset.level = level;
        newSelect.innerHTML = '<option value="">Loading...</option>';
        container.appendChild(newSelect);
        loadCategoriesForLevel(level, parentId);
    }

    function removeSelectsAfterLevel(level) {
        const allSelects = container.querySelectorAll('.category-select');
        allSelects.forEach(select => {
            if (parseInt(select.dataset.level) > level) {
                select.remove();
            }
        });
    }

    function restoreOldSelection(categoryId) {
        parentIdHidden.value = categoryId;
    }

    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});
</script>
@endpush
@endsection
