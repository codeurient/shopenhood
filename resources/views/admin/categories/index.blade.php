@extends('admin.layouts.app')

@section('title', 'Categories Management')
@section('page-title', 'Categories')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Categories</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage product categories hierarchy</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus"></i>
            Create Category
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-folder text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ \App\Models\Category::count() }}</p>
                    <p class="text-[#37474F] text-xs">Total Categories</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-tree text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ \App\Models\Category::whereNull('parent_id')->count() }}</p>
                    <p class="text-[#37474F] text-xs">Root Categories</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ \App\Models\Category::where('is_active', true)->count() }}</p>
                    <p class="text-[#37474F] text-xs">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-screwdriver-wrench text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ DB::table('category_variants')->distinct('category_id')->count() }}</p>
                    <p class="text-[#37474F] text-xs">With Variants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Level-Based Accordions Container -->
    <div id="accordionsContainer" class="space-y-4">
        <!-- Root level accordion will be inserted here by JavaScript -->
    </div>
</div>

<!-- Variant Assignment Modal -->
<div id="variantModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none">
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-[#E0E0E0] flex justify-between items-center">
            <div>
                <h3 class="text-[15px] font-semibold text-[#1A1A1A]">Assign Variants</h3>
                <p id="modalCategoryName" class="text-[13px] text-[#37474F] mt-0.5"></p>
            </div>
            <button onclick="closeVariantModal()"
                    class="w-8 h-8 flex items-center justify-center rounded text-[#37474F] hover:bg-[#E0E0E0]/50 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="variantModalBody" class="flex-1 overflow-y-auto p-6">
            <div class="text-center py-12">
                <i class="fa-solid fa-spinner fa-spin text-[#D4AF37] text-3xl mb-4"></i>
                <p class="text-[#37474F] text-[13px] mt-4">Loading variants...</p>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-[#E0E0E0] flex justify-end gap-3">
            <button onclick="closeVariantModal()"
                    class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </button>
            <button onclick="saveVariants()" id="saveVariantsBtn"
                    class="inline-flex items-center gap-1.5 justify-center h-[34px] px-4 text-[13px] font-semibold bg-[#D4AF37] text-[#000000] rounded hover:brightness-110 transition">
                <i class="fa-solid fa-floppy-disk"></i>
                Save Variants
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
 */
const childrenBaseUrl = '{{ route('admin.categories.children') }}';

function loadLevel(parentId, title, level) {
    const url = parentId
        ? `${childrenBaseUrl}/${parentId}`
        : childrenBaseUrl;

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
    accordion.className = 'bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden border-l-4 ' + getBorderColor(level);

    let html = `
        <div class="px-6 py-4 bg-gray-50 border-b border-[#E0E0E0]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#D4AF37]/10 flex items-center justify-center">
                        ${getLevelIcon(level)}
                    </div>
                    <div>
                        <h3 class="text-[14px] font-semibold text-[#1A1A1A]">${title}</h3>
                        <p class="text-xs text-[#37474F]">Level ${level + 1} &bull; ${categories.length} categories</p>
                    </div>
                </div>
                ${level > 0 ? `<button onclick="removeLevel(${level})" class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition"><i class="fa-solid fa-xmark text-xs"></i> Close Level</button>` : ''}
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    `;

    categories.forEach(cat => {
        html += `
            <div class="border border-[#E0E0E0] rounded-[6px] p-4 hover:border-[#D4AF37] hover:shadow-md transition ${cat.has_children ? 'cursor-pointer' : ''}"
                 ${cat.has_children ? `onclick="onCategoryClick(${cat.id}, '${escapeHtml(cat.name)}', ${level})"` : ''}>
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        ${cat.icon ? `<span class="text-xl">${cat.icon}</span>` : ''}
                        <div>
                            <h4 class="font-semibold text-[#1A1A1A] text-[13px]">${cat.name}</h4>
                            <p class="text-[11px] text-[#37474F] mt-0.5">${cat.slug}</p>
                        </div>
                    </div>
                    ${cat.has_children ? '<i class="fa-solid fa-chevron-right text-[#D4AF37] text-sm"></i>' : ''}
                </div>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    ${cat.is_active
                        ? '<span class="text-[11px] font-semibold px-2 py-0.5 bg-green-100 text-green-700 rounded-[10px]">Active</span>'
                        : '<span class="text-[11px] font-semibold px-2 py-0.5 bg-red-100 text-red-700 rounded-[10px]">Inactive</span>'}
                    ${cat.variants_count > 0
                        ? `<span class="text-[11px] font-semibold px-2 py-0.5 bg-[#D4AF37]/10 text-[#37474F] rounded-[10px]">${cat.variants_count} variants</span>`
                        : ''}
                    ${cat.listings_count > 0
                        ? `<span class="text-[11px] font-semibold px-2 py-0.5 bg-[#37474F]/10 text-[#37474F] rounded-[10px]">${cat.listings_count} listings</span>`
                        : ''}
                </div>
                <div class="flex gap-1.5" onclick="event.stopPropagation()">
                    <button onclick="openVariantModal(${cat.id}, '${escapeHtml(cat.name)}')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 h-[28px] px-3 text-[12px] font-semibold bg-[#D4AF37] text-[#000000] rounded hover:brightness-110 transition">
                        <i class="fa-solid fa-sliders text-xs"></i> Variants
                    </button>
                    <a href="/admin/categories/${cat.id}/edit"
                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition no-underline">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <button onclick="confirmDeleteCategory(${cat.id}, '${escapeHtml(cat.name)}')"
                            class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition">
                        <i class="fa-solid fa-trash text-xs"></i>
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
        'border-[#D4AF37]',
        'border-[#37474F]',
        'border-[#D4AF37]',
        'border-[#37474F]',
        'border-[#D4AF37]'
    ];
    return colors[level % colors.length];
}

/**
 * Get Font Awesome icon based on level
 */
function getLevelIcon(level) {
    const icons = [
        '<i class="fa-solid fa-house text-[#D4AF37] text-sm"></i>',
        '<i class="fa-solid fa-folder text-[#D4AF37] text-sm"></i>',
        '<i class="fa-solid fa-folder-open text-[#D4AF37] text-sm"></i>',
        '<i class="fa-solid fa-layer-group text-[#D4AF37] text-sm"></i>',
        '<i class="fa-solid fa-list text-[#D4AF37] text-sm"></i>'
    ];
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
// VARIANT MODAL FUNCTIONS
// ============================================================================

function openVariantModal(categoryId, categoryName) {
    currentCategoryId = categoryId;

    document.getElementById('variantModalBody').innerHTML = `
        <div class="text-center py-12">
            <i class="fa-solid fa-spinner fa-spin text-[#D4AF37] text-3xl"></i>
            <p class="text-[#37474F] text-[13px] mt-4">Loading variants...</p>
        </div>
    `;

    document.getElementById('modalCategoryName').textContent = `Category: ${categoryName}`;

    const modal = document.getElementById('variantModal');
    modal.classList.remove('hidden');
    modal.style.display = '';

    document.body.style.overflow = 'hidden';

    fetch(`/admin/categories/${categoryId}/variants`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            renderVariants(data.variants);
        }
    })
    .catch(err => console.error('Error loading variants:', err));
}

function renderVariants(variants) {
    const body = document.getElementById('variantModalBody');

    if (!variants.length) {
        body.innerHTML = `
            <div class="text-center py-12">
                <i class="fa-solid fa-box-open text-[#E0E0E0] text-5xl mb-4"></i>
                <p class="text-[#37474F] text-[13px] mb-2">No variants available</p>
                <a href="/admin/variants/create" class="text-[#D4AF37] hover:underline text-[13px]">Create a variant first</a>
            </div>
        `;
        return;
    }

    let html = '<div class="space-y-3">';
    variants.forEach(v => {
        const settings = v.settings || {};

        html += `
            <div class="border rounded-[6px] p-4 ${v.is_assigned ? 'bg-[#D4AF37]/5 border-[#D4AF37]/40' : 'bg-gray-50 border-[#E0E0E0]'}">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="var_${v.id}" value="${v.id}" ${v.is_assigned ? 'checked' : ''}
                           onchange="toggleVariantSettings(this)"
                           class="mt-1 w-4 h-4 rounded border-[#E0E0E0] accent-[#D4AF37]">
                    <div class="flex-1">
                        <label for="var_${v.id}" class="cursor-pointer">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-[#1A1A1A] text-[13px]">${v.name}</span>
                                <span class="px-2 py-0.5 text-[11px] font-semibold bg-[#D4AF37]/10 text-[#37474F] rounded-[10px]">${v.type}</span>
                            </div>
                            <p class="text-[12px] text-[#37474F]">${v.items_count} items available</p>
                        </label>
                        <div id="settings_${v.id}" class="mt-3 pl-4 border-l-2 border-[#D4AF37]/40 space-y-2 ${v.is_assigned ? '' : 'hidden'}">
                            <label class="flex items-center gap-2 text-[12px] text-[#1A1A1A] cursor-pointer">
                                <input type="checkbox" name="req_${v.id}" ${settings.is_required ? 'checked' : ''} class="w-4 h-4 rounded accent-[#D4AF37]">
                                Required field
                            </label>
                            <label class="flex items-center gap-2 text-[12px] text-[#1A1A1A] cursor-pointer">
                                <input type="checkbox" name="sea_${v.id}" ${settings.is_searchable !== false ? 'checked' : ''} class="w-4 h-4 rounded accent-[#D4AF37]">
                                Searchable
                            </label>
                            <label class="flex items-center gap-2 text-[12px] text-[#1A1A1A] cursor-pointer">
                                <input type="checkbox" name="fil_${v.id}" ${settings.is_filterable !== false ? 'checked' : ''} class="w-4 h-4 rounded accent-[#D4AF37]">
                                Filterable
                            </label>
                            <label class="flex items-center gap-2 text-[12px] text-[#1A1A1A] font-medium cursor-pointer">
                                <input type="checkbox" name="main_${v.id}" ${settings.is_main_shown ? 'checked' : ''} class="w-4 h-4 rounded accent-[#D4AF37]">
                                <span class="text-[#D4AF37]">Main shown variant</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    body.innerHTML = html;
}

function toggleVariantSettings(checkbox) {
    const settingsDiv = document.getElementById('settings_' + checkbox.value);
    const container = checkbox.closest('div[class*="border"]');
    if (checkbox.checked) {
        settingsDiv.classList.remove('hidden');
        container.className = container.className.replace('bg-gray-50 border-[#E0E0E0]', 'bg-[#D4AF37]/5 border-[#D4AF37]/40');
    } else {
        settingsDiv.classList.add('hidden');
        container.className = container.className.replace('bg-[#D4AF37]/5 border-[#D4AF37]/40', 'bg-gray-50 border-[#E0E0E0]');
    }
}

function closeVariantModal() {
    const modal = document.getElementById('variantModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function saveVariants() {
    const checked = document.querySelectorAll('input[id^="var_"]:checked');

    const variants = Array.from(checked).map(cb => {
        const variantId = cb.value;
        return {
            id: parseInt(variantId),
            is_required: document.querySelector(`input[name="req_${variantId}"]`)?.checked || false,
            is_searchable: document.querySelector(`input[name="sea_${variantId}"]`)?.checked || true,
            is_filterable: document.querySelector(`input[name="fil_${variantId}"]`)?.checked || true,
            is_main_shown: document.querySelector(`input[name="main_${variantId}"]`)?.checked || false,
            sort_order: 0
        };
    });

    const btn = document.getElementById('saveVariantsBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch(`/admin/categories/${currentCategoryId}/variants/sync`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ variants })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        alert('Network error occurred: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Variants';
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
