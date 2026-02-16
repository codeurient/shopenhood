@extends('admin.layouts.app')

@section('title', 'Categories Management')
@section('page-title', 'Categories')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Categories</h2>
            <p class="text-gray-600 mt-1">Manage product categories hierarchy</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
            ➕ Create Category
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">📁</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Category::count() }}</p>
                    <p class="text-gray-600 text-sm">Total Categories</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 text-2xl">🌳</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Category::whereNull('parent_id')->count() }}</p>
                    <p class="text-gray-600 text-sm">Root Categories</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 text-2xl">✓</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Category::where('is_active', true)->count() }}</p>
                    <p class="text-gray-600 text-sm">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 text-2xl">🔧</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ DB::table('category_variants')->distinct('category_id')->count() }}</p>
                    <p class="text-gray-600 text-sm">With Variants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Level-Based Accordions Container -->
    <div id="accordionsContainer" class="space-y-6">
        <!-- Root level accordion will be inserted here by JavaScript -->
    </div>
</div>

<!-- Variant Assignment Modal -->
<div id="variantModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Assign Variants</h3>
                <p id="modalCategoryName" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button onclick="closeVariantModal()" class="text-gray-400 hover:text-gray-600 text-2xl">✕</button>
        </div>
        
        <div id="variantModalBody" class="flex-1 overflow-y-auto p-6">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
                <p class="text-gray-600 mt-4">Loading variants...</p>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeVariantModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Cancel
            </button>
            <button onclick="saveVariants()" id="saveVariantsBtn" class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">
                💾 Save Variants
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Level-based accordion system
const accordionsContainer = document.getElementById('accordionsContainer');
let currentCategoryId = null;

// Load root categories on page load
document.addEventListener('DOMContentLoaded', function() {
    loadLevel(null, 'Root Categories', 0);
});

/**
 * Load categories for a specific level
 * @param {number|null} parentId - Parent category ID (null for root)
 * @param {string} title - Accordion title
 * @param {number} level - Current level (0 = root)
 */
function loadLevel(parentId, title, level) {
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
            renderAccordion(data.categories, title, level, parentId);
        }
    })
    .catch(err => console.error('Error loading categories:', err));
}

/**
 * Render an accordion section
 */
function renderAccordion(categories, title, level, parentId) {
    const accordionId = `accordion-level-${level}`;
    const existingAccordion = document.getElementById(accordionId);
    
    // Remove this level and all levels below it
    if (existingAccordion) {
        let nextSibling = existingAccordion.nextElementSibling;
        while (nextSibling) {
            const toRemove = nextSibling;
            nextSibling = nextSibling.nextElementSibling;
            toRemove.remove();
        }
        existingAccordion.remove();
    }
    
    // Create accordion HTML
    const accordion = document.createElement('div');
    accordion.id = accordionId;
    accordion.className = 'bg-white rounded-lg shadow-lg overflow-hidden border-l-4 ' + getBorderColor(level);
    
    let html = `
        <div class="px-6 py-4 bg-gradient-to-r ${getGradientColor(level)} border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">${getLevelIcon(level)}</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                        <p class="text-sm text-gray-600">Level ${level + 1} • ${categories.length} categories</p>
                    </div>
                </div>
                ${level > 0 ? `<button onclick="removeLevel(${level})" class="text-gray-500 hover:text-red-600">✕ Close Level</button>` : ''}
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    `;
    
    categories.forEach(cat => {
        html += `
            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-400 hover:shadow-md transition ${cat.has_children ? 'cursor-pointer' : ''}"
                 ${cat.has_children ? `onclick="onCategoryClick(${cat.id}, '${escapeHtml(cat.name)}', ${level})"` : ''}>
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        ${cat.icon ? `<span class="text-2xl">${cat.icon}</span>` : ''}
                        <div>
                            <h4 class="font-semibold text-gray-900">${cat.name}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">${cat.slug}</p>
                        </div>
                    </div>
                    ${cat.has_children ? '<span class="text-primary-600 text-xl">→</span>' : ''}
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                    ${cat.is_active 
                        ? '<span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Active</span>'
                        : '<span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Inactive</span>'}
                    ${cat.variants_count > 0 
                        ? `<span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">${cat.variants_count} variants</span>`
                        : ''}
                    ${cat.listings_count > 0 
                        ? `<span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">${cat.listings_count} listings</span>`
                        : ''}
                </div>
                <div class="flex gap-2" onclick="event.stopPropagation()">
                    <button onclick="openVariantModal(${cat.id}, '${escapeHtml(cat.name)}')"
                            class="flex-1 px-3 py-1.5 text-sm bg-primary-500 text-white rounded hover:bg-primary-600">
                        🔧 Variants
                    </button>
                    <a href="/admin/categories/${cat.id}/edit" class="px-3 py-1.5 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 no-underline">
                        ✏️
                    </a>
                    <button onclick="confirmDeleteCategory(${cat.id}, '${escapeHtml(cat.name)}')" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                        🗑️
                    </button>
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    
    accordion.innerHTML = html;
    accordionsContainer.appendChild(accordion);
}

/**
 * Handle category click - load next level
 */
function onCategoryClick(categoryId, categoryName, currentLevel) {
    loadLevel(categoryId, `Sub-Categories of "${categoryName}"`, currentLevel + 1);
}

/**
 * Remove a specific level and all levels below it
 */
function removeLevel(level) {
    const accordion = document.getElementById(`accordion-level-${level}`);
    if (accordion) {
        let nextSibling = accordion.nextElementSibling;
        while (nextSibling) {
            const toRemove = nextSibling;
            nextSibling = nextSibling.nextElementSibling;
            toRemove.remove();
        }
        accordion.remove();
    }
}

/**
 * Get border color based on level
 */
function getBorderColor(level) {
    const colors = [
        'border-primary-500',
        'border-purple-500',
        'border-pink-500',
        'border-blue-500',
        'border-green-500'
    ];
    return colors[level % colors.length];
}

/**
 * Get gradient background based on level
 */
function getGradientColor(level) {
    const gradients = [
        'from-primary-50 to-white',
        'from-purple-50 to-white',
        'from-pink-50 to-white',
        'from-blue-50 to-white',
        'from-green-50 to-white'
    ];
    return gradients[level % gradients.length];
}

/**
 * Get icon based on level
 */
function getLevelIcon(level) {
    const icons = ['🏠', '📁', '📂', '🗂️', '📋'];
    return icons[Math.min(level, icons.length - 1)];
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================================================
// VARIANT MODAL FUNCTIONS (from previous implementation)
// ============================================================================

function openVariantModal(categoryId, categoryName) {
    currentCategoryId = categoryId;

    // Reset modal body to loading state BEFORE showing to prevent stale content flash
    document.getElementById('variantModalBody').innerHTML = `
        <div class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
            <p class="text-gray-600 mt-4">Loading variants...</p>
        </div>
    `;

    document.getElementById('modalCategoryName').textContent = `Category: ${categoryName}`;

    // Remove both hidden class and inline style (for initial page load protection)
    const modal = document.getElementById('variantModal');
    modal.classList.remove('hidden');
    modal.style.display = '';

    document.body.style.overflow = 'hidden';

    console.log('🔄 Loading variants for category:', categoryId, categoryName);

    fetch(`/admin/categories/${categoryId}/variants`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        console.log('📦 Received variants data:', data);
        if (data.success) {
            // Log each variant's settings
            data.variants.forEach(v => {
                if (v.is_assigned && v.settings) {
                    console.log(`  Variant "${v.name}" (ID: ${v.id}):`, v.settings);
                }
            });
            renderVariants(data.variants);
        }
    })
    .catch(err => console.error('❌ Error loading variants:', err));
}

function renderVariants(variants) {
    const body = document.getElementById('variantModalBody');

    console.log('🎨 Rendering variants:', variants.length);

    if (!variants.length) {
        body.innerHTML = `
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📦</div>
                <p class="text-gray-600 text-lg mb-2">No variants available</p>
                <a href="/admin/variants/create" class="text-primary-600 hover:underline">Create a variant first</a>
            </div>
        `;
        return;
    }

    let html = '<div class="space-y-3">';
    variants.forEach(v => {
        const settings = v.settings || {};

        // Log checkbox states being rendered
        console.log(`  Rendering variant "${v.name}" (ID: ${v.id}):`, {
            is_assigned: v.is_assigned,
            is_required: settings.is_required,
            is_searchable: settings.is_searchable,
            is_filterable: settings.is_filterable,
            is_main_shown: settings.is_main_shown,
            will_check_main_shown: settings.is_main_shown ? 'YES' : 'NO'
        });

        html += `
            <div class="border rounded-lg p-4 ${v.is_assigned ? 'bg-primary-50 border-primary-300' : 'bg-gray-50 border-gray-200'}">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="var_${v.id}" value="${v.id}" ${v.is_assigned ? 'checked' : ''}
                           onchange="toggleVariantSettings(this)"
                           class="mt-1 w-5 h-5 text-primary-600 rounded focus:ring-primary-500">
                    <div class="flex-1">
                        <label for="var_${v.id}" class="cursor-pointer">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900">${v.name}</span>
                                <span class="px-2 py-1 text-xs bg-primary-100 text-primary-700 rounded">${v.type}</span>
                            </div>
                            <p class="text-sm text-gray-600">${v.items_count} items available</p>
                        </label>
                        <div id="settings_${v.id}" class="mt-3 pl-4 border-l-2 border-primary-300 space-y-2 ${v.is_assigned ? '' : 'hidden'}">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="req_${v.id}" ${settings.is_required ? 'checked' : ''} class="w-4 h-4 text-primary-600 rounded">
                                <span>Required field</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="sea_${v.id}" ${settings.is_searchable !== false ? 'checked' : ''} class="w-4 h-4 text-primary-600 rounded">
                                <span>Searchable</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="fil_${v.id}" ${settings.is_filterable !== false ? 'checked' : ''} class="w-4 h-4 text-primary-600 rounded">
                                <span>Filterable</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="main_${v.id}" ${settings.is_main_shown ? 'checked' : ''} class="w-4 h-4 text-primary-600 rounded">
                                <span class="font-medium text-primary-700">Main shown variant</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    body.innerHTML = html;

    console.log('✓ Variants rendered');
}

function toggleVariantSettings(checkbox) {
    const settingsDiv = document.getElementById('settings_' + checkbox.value);
    const container = checkbox.closest('div[class*="border"]');
    if (checkbox.checked) {
        settingsDiv.classList.remove('hidden');
        container.classList.add('bg-primary-50', 'border-primary-300');
        container.classList.remove('bg-gray-50', 'border-gray-200');
    } else {
        settingsDiv.classList.add('hidden');
        container.classList.remove('bg-primary-50', 'border-primary-300');
        container.classList.add('bg-gray-50', 'border-gray-200');
    }
}

function closeVariantModal() {
    const modal = document.getElementById('variantModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function saveVariants() {
    console.log('🔄 Saving variants for category:', currentCategoryId);

    const checked = document.querySelectorAll('input[id^="var_"]:checked');
    console.log('📋 Selected variants:', checked.length);

    const variants = Array.from(checked).map(cb => {
        const variantId = cb.value;
        const mainShownCheckbox = document.querySelector(`input[name="main_${variantId}"]`);
        const isMainShown = mainShownCheckbox?.checked || false;

        console.log(`  Variant ${variantId}:`, {
            is_required: document.querySelector(`input[name="req_${variantId}"]`)?.checked || false,
            is_searchable: document.querySelector(`input[name="sea_${variantId}"]`)?.checked || true,
            is_filterable: document.querySelector(`input[name="fil_${variantId}"]`)?.checked || true,
            is_main_shown: isMainShown
        });

        return {
            id: parseInt(variantId),
            is_required: document.querySelector(`input[name="req_${variantId}"]`)?.checked || false,
            is_searchable: document.querySelector(`input[name="sea_${variantId}"]`)?.checked || true,
            is_filterable: document.querySelector(`input[name="fil_${variantId}"]`)?.checked || true,
            is_main_shown: isMainShown,
            sort_order: 0
        };
    });

    console.log('📦 Sending data:', { variants });

    const btn = document.getElementById('saveVariantsBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Saving...';

    fetch(`/admin/categories/${currentCategoryId}/variants/sync`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ variants })
    })
    .then(r => {
        console.log('📡 Response status:', r.status);
        return r.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);
        if (data.success) {
            alert('✓ ' + data.message);
            location.reload();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(err => {
        console.error('❌ Error:', err);
        alert('Network error occurred: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '💾 Save Variants';
    });
}

function confirmDeleteCategory(categoryId, categoryName) {
    if (confirm(`Are you sure you want to delete "${categoryName}"? This will also delete all child categories and remove variant assignments.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/categories/${categoryId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection