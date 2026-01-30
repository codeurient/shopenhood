<div class="mb-4">
    <h2 id="accordion-heading-{{ $category->id }}">
        <button type="button" 
                class="flex items-center justify-between w-full p-5 font-medium text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-100 gap-3"
                data-accordion-target="#accordion-body-{{ $category->id }}" 
                aria-expanded="false" 
                aria-controls="accordion-body-{{ $category->id }}">
            <div class="flex items-center gap-3 flex-1">
                @if($category->icon)
                    <span class="text-2xl">{{ $category->icon }}</span>
                @endif
                <div class="text-left">
                    <div class="font-semibold">{{ $category->name }}</div>
                    <div class="flex gap-2 mt-1">
                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">{{ $category->slug }}</span>
                        @if($category->variants_count > 0)
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ $category->variants_count }} variants</span>
                        @endif
                        @if($category->is_active)
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Active</span>
                        @else
                            <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                <button type="button" 
                        onclick="openVariantModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                        class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    🔧 Variants
                </button>
                <button type="button" 
                        class="px-3 py-1.5 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    ✏️ Edit
                </button>
            </div>
            @if($category->children->count() > 0)
                <svg data-accordion-icon class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            @endif
        </button>
    </h2>
    
    @if($category->children->count() > 0)
        <div id="accordion-body-{{ $category->id }}" class="hidden" aria-labelledby="accordion-heading-{{ $category->id }}">
            <div class="p-5 border border-t-0 border-gray-200 rounded-b-lg bg-gray-50">
                <div class="space-y-3 pl-6 border-l-2 border-indigo-300">
                    @foreach($category->children as $childIndex => $child)
                        @include('admin.categories.partials.accordion-item', ['category' => $child, 'index' => $childIndex])
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>