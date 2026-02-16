@extends('admin.layouts.app')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Create New Category</h2>
            <p class="text-gray-600 mt-1">Add a new category to organize your products</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf

            <div class="p-8 space-y-8">
                
                <!-- Hierarchy Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        Category Hierarchy
                    </h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Parent Category
                        </label>
                        <div id="category-selects-container" class="space-y-3">
                            <select name="parent_id" id="category_level_0" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 category-select" 
                                    data-level="0">
                                <option value="">🏠 Root Category</option>
                            </select>
                        </div>
                        <input type="hidden" name="parent_id" id="parent_id_hidden" value="">
                        <p class="mt-2 text-sm text-gray-500">
                            💡 Select parent categories in order. Leave at root to create a top-level category.
                        </p>
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name') }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 @error('name') border-red-500 @enderror" 
                                   placeholder="e.g., Electronics, Clothing, Home & Garden" 
                                   required autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <label for="slug" class="block mb-2 text-sm font-medium text-gray-900">
                                URL Slug
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <input type="text" id="slug" name="slug" 
                                       value="{{ old('slug') }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 @error('slug') border-red-500 @enderror" 
                                       placeholder="auto-generated-if-empty">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                🔗 Auto-generated from name if left empty
                            </p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4" 
                                      class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror" 
                                      placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Visual Identity Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Visual Identity
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Icon -->
                        <div>
                            <label for="icon" class="block mb-2 text-sm font-medium text-gray-900">
                                Icon Code
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input type="text" id="icon" name="icon" 
                                       value="{{ old('icon') }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 @error('icon') border-red-500 @enderror" 
                                       placeholder="e.g., 📱 🏠 🚗">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Use emoji or icon class (e.g., fa-laptop)
                            </p>
                            @error('icon')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="image">
                                Category Image
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('image') border-red-500 @enderror">
                            <p class="mt-2 text-sm text-gray-500">
                                PNG, JPG, WEBP (MAX. 2MB)
                            </p>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block mb-2 text-sm font-medium text-gray-900">
                                Sort Order
                            </label>
                            <input type="number" id="sort_order" name="sort_order" 
                                   value="{{ old('sort_order', 0) }}" min="0"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 @error('sort_order') border-red-500 @enderror" 
                                   placeholder="0">
                            <p class="mt-2 text-sm text-gray-500">
                                Lower numbers appear first
                            </p>
                            @error('sort_order')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        SEO Settings
                    </h3>
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block mb-2 text-sm font-medium text-gray-900">
                                Meta Title
                            </label>
                            <input type="text" id="meta_title" name="meta_title" maxlength="60"
                                   value="{{ old('meta_title') }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 @error('meta_title') border-red-500 @enderror" 
                                   placeholder="Optimized title for search engines">
                            <p class="mt-2 text-sm text-gray-500">
                                <span id="meta_title_count">0</span>/60 characters
                            </p>
                            @error('meta_title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block mb-2 text-sm font-medium text-gray-900">
                                Meta Description
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"
                                      class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 @error('meta_description') border-red-500 @enderror" 
                                      placeholder="Brief description for search engine results...">{{ old('meta_description') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                <span id="meta_desc_count">0</span>/160 characters
                            </p>
                            @error('meta_description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status
                    </h3>
                    <div class="flex items-center">
                        <label for="is_active" class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Active (visible to users)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-end gap-4">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600 focus:ring-4 focus:outline-none focus:ring-primary-300 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- <style>
    .required { color: #dc3545; }
    .form-group { margin-bottom: 1.5rem; }
    .form-actions { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #dee2e6; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
    .alert ul { margin: 0; padding-left: 1.5rem; }
    #category-selects-container {display: flex;     display: flex;      flex-direction: column;       gap: 10px;  }
    #category-selects-container .category-select { width: 100%; }
    </style> --}}

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
                
                // Update to match new Tailwind classes
                currentSelect.innerHTML = '<option value="">📂 Select Category</option>';
                
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    option.dataset.hasChildren = category.children && category.children.length > 0;
                    currentSelect.appendChild(option);
                });

                // Restore old value if validation failed
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

            // Remove all selects after current level
            removeSelectsAfterLevel(currentLevel);

            if (selectedCategoryId) {
                // Update hidden input with selected category ID
                parentIdHidden.value = selectedCategoryId;

                // Check if selected category has children
                const hasChildren = selectedOption.dataset.hasChildren === 'true';

                if (hasChildren) {
                    // Create next level select
                    createNextLevelSelect(currentLevel + 1, selectedCategoryId);
                }
            } else {
                // Root level selected, clear hidden input
                parentIdHidden.value = '';
            }
        }
    });

    function createNextLevelSelect(level, parentId) {
        const newSelect = document.createElement('select');
        newSelect.id = 'category_level_' + level;
        // Updated Tailwind classes
        newSelect.className = 'bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 category-select';
        newSelect.dataset.level = level;
        newSelect.style.marginTop = '0.75rem';
        newSelect.innerHTML = '<option value="">Loading...</option>';

        container.appendChild(newSelect);

        // Load categories for this level
        loadCategoriesForLevel(level, parentId);
    }

    function removeSelectsAfterLevel(level) {
        const allSelects = container.querySelectorAll('.category-select');
        allSelects.forEach(select => {
            const selectLevel = parseInt(select.dataset.level);
            if (selectLevel > level) {
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