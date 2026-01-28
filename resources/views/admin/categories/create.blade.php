@extends('admin.layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1>Create Category</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn-back">Back to Categories</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                @csrf

                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <div id="category-selects-container">
                        <select name="parent_id" id="category_level_0" class="form-control category-select" data-level="0">
                            <option value="">-- Root Category --</option>
                        </select>
                    </div>
                    <input type="hidden" name="parent_id" id="parent_id_hidden" value="">
                    <small class="form-text">Select parent categories in order. Leave at root level to create a top-level category.</small>
                </div>

                <div class="form-group">
                    <label for="name">Category Name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name') }}" 
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input 
                        type="text" 
                        name="slug" 
                        id="slug" 
                        class="form-control @error('slug') is-invalid @enderror" 
                        value="{{ old('slug') }}"
                    >
                    <small class="form-text">Auto-generated from name if left empty</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4" 
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="icon">Icon</label>
                        <input 
                            type="text" 
                            name="icon" 
                            id="icon" 
                            class="form-control @error('icon') is-invalid @enderror" 
                            value="{{ old('icon') }}"
                            placeholder="e.g., fa-laptop, bi-house"
                        >
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="image">Category Image</label>
                        <input 
                            type="file" 
                            name="image" 
                            id="image" 
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*"
                        >
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input 
                        type="number" 
                        name="sort_order" 
                        id="sort_order" 
                        class="form-control @error('sort_order') is-invalid @enderror" 
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                    >
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_title">Meta Title (SEO)</label>
                    <input 
                        type="text" 
                        name="meta_title" 
                        id="meta_title" 
                        class="form-control @error('meta_title') is-invalid @enderror" 
                        value="{{ old('meta_title') }}"
                        maxlength="60"
                    >
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description (SEO)</label>
                    <textarea 
                        name="meta_description" 
                        id="meta_description" 
                        rows="3" 
                        class="form-control @error('meta_description') is-invalid @enderror"
                        maxlength="160"
                    >{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active" 
                            class="form-check-input" 
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.required { color: #dc3545; }
.form-group { margin-bottom: 1.5rem; }
.form-actions { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #dee2e6; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
.alert ul { margin: 0; padding-left: 1.5rem; }
#category-selects-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

#category-selects-container .category-select {
    width: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('category-selects-container');
    const parentIdHidden = document.getElementById('parent_id_hidden');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

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

    /**
     * Load categories for a specific level
     * @param {number} level - The level to load categories for
     * @param {number|null} parentId - The parent category ID
     */
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
                
                // Clear existing options except the first one
                currentSelect.innerHTML = '<option value="">-- Select Category --</option>';
                
                // Populate with categories
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

    /**
     * Handle category selection change
     */
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

    /**
     * Create a new select element for the next level
     * @param {number} level - The level number
     * @param {number} parentId - The parent category ID
     */
    function createNextLevelSelect(level, parentId) {
        const newSelect = document.createElement('select');
        newSelect.id = 'category_level_' + level;
        newSelect.className = 'form-control category-select';
        newSelect.dataset.level = level;
        newSelect.style.marginTop = '10px';
        newSelect.innerHTML = '<option value="">Loading...</option>';

        container.appendChild(newSelect);

        // Load categories for this level
        loadCategoriesForLevel(level, parentId);
    }

    /**
     * Remove all select elements after a given level
     * @param {number} level - The level to keep
     */
    function removeSelectsAfterLevel(level) {
        const allSelects = container.querySelectorAll('.category-select');
        allSelects.forEach(select => {
            const selectLevel = parseInt(select.dataset.level);
            if (selectLevel > level) {
                select.remove();
            }
        });
    }

    /**
     * Restore old selection after validation failure
     * @param {string} categoryId - The category ID to restore
     */
    function restoreOldSelection(categoryId) {
        // This would require loading the full path to the category
        // For now, just set the hidden input
        parentIdHidden.value = categoryId;
        
        // You can enhance this by making an AJAX call to get the category path
        // and recreating all the select elements with the correct values
    }

    /**
     * Generate slug from text
     * @param {string} text - The text to convert to slug
     * @returns {string} The generated slug
     */
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
@endsection