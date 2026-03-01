@props(['categories'])

@php
    $buildTree = null;
    $buildTree = function ($cats, $depth = 0) use (&$buildTree) {
        return $cats->map(fn ($c) => [
            'id'       => $c->id,
            'name'     => $c->name,
            'slug'     => $c->slug,
            'icon_url' => $c->icon ? asset('storage/' . $c->icon) : null,
            'children' => $depth < 2 ? $buildTree($c->children, $depth + 1) : [],
        ])->values()->toArray();
    };

    $categoryTree = $buildTree($categories);
@endphp

<script>
    window.__categoryTree = @json($categoryTree);
</script>

<!-- Horizontal Category Scroll -->
<div class="bg-white border-b border-gray-200"
     x-data="{
         categoryPanelOpen: false,
         stack: [],
         categoriesData: window.__categoryTree || [],
         baseUrl: '{{ route('listings.index') }}',
         get currentLevel() {
             return this.stack.length > 0 ? this.stack[this.stack.length - 1] : { title: 'All Categories', items: [] };
         },
         openPanel() {
             this.stack = [{ title: 'All Categories', items: this.categoriesData }];
             this.categoryPanelOpen = true;
         },
         push(cat) {
             this.stack.push({ title: cat.name, items: cat.children });
         },
         pop() {
             if (this.stack.length > 1) {
                 this.stack.pop();
             } else {
                 this.categoryPanelOpen = false;
             }
         },
     }"
     x-init="$watch('categoryPanelOpen', val => document.body.style.overflow = val ? 'hidden' : '')">

    <div class="max-w-[1250px] mx-auto">
        <div class="flex gap-3 px-4 md:px-6 py-4 overflow-x-auto scrollbar-hide">
            <!-- Grid Button (Opens Category Drawer) -->
            <button type="button"
                    @click="openPanel()"
                    class="flex items-center justify-center flex-shrink-0 w-16 md:w-20 h-20 md:h-24 bg-gray-700 rounded-xl hover:bg-gray-800 transition-colors">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>

            @foreach($categories as $category)
            <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
               class="flex flex-col flex-shrink-0 w-24 md:w-28 space-y-1 group">
                <div class="relative h-20 md:h-24 bg-gray-200 rounded-xl overflow-hidden">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400"></div>
                    @endif
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 flex items-end p-2 transition-colors">
                        <span class="text-xs font-semibold text-white truncate">{{ $category->name }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Backdrop -->
    <div x-show="categoryPanelOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="categoryPanelOpen = false"
         class="fixed inset-0 z-50 bg-black bg-opacity-50"
         style="display: none;">
    </div>

    <!-- Category Drawer (slides from left) -->
    <aside x-show="categoryPanelOpen"
           x-trap.inert="categoryPanelOpen"
           x-transition:enter="transition ease-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-[51] w-80 bg-white shadow-xl flex flex-col"
           role="dialog"
           aria-modal="true"
           aria-label="All categories"
           style="display: none;">

        <!-- Drawer Header -->
        <div class="flex items-center gap-1 px-3 py-4 border-b border-gray-200 flex-shrink-0">
            <!-- Back button — visible when drilled into a subcategory level -->
            <button type="button"
                    x-show="stack.length > 1"
                    @click="pop()"
                    style="display: none;"
                    class="flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <h2 class="text-base font-bold text-gray-900 flex-1 truncate px-1"
                x-text="currentLevel.title"></h2>

            <button type="button"
                    @click="categoryPanelOpen = false"
                    class="flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Category List for current level -->
        <nav class="flex-1 overflow-y-auto py-2">
            <template x-for="item in currentLevel.items" :key="item.id">
                <div class="border-b border-gray-100">
                    <div class="flex items-center">
                        <!-- Category name — navigates to listing page -->
                        <a :href="baseUrl + '?category=' + item.slug"
                           class="flex-1 flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors min-w-0">
                            <template x-if="item.icon_url">
                                <img :src="item.icon_url"
                                     :alt="item.name"
                                     class="w-7 h-7 rounded-lg object-cover flex-shrink-0">
                            </template>
                            <template x-if="!item.icon_url">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex-shrink-0"></div>
                            </template>
                            <span class="font-medium truncate" x-text="item.name"></span>
                        </a>

                        <!-- Drill-down arrow — opens subcategory panel -->
                        <template x-if="item.children && item.children.length > 0">
                            <button type="button"
                                    @click.stop="push(item)"
                                    class="flex-shrink-0 flex items-center justify-center w-10 h-12 text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-colors mr-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </nav>
    </aside>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
