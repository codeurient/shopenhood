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
<div class="bg-white border-b border-[#E0E0E0]"
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
                    class="flex items-center justify-center flex-shrink-0 w-16 md:w-20 h-20 md:h-24 bg-[#000000] rounded hover:bg-[#1A1A1A] transition-colors">
                <i class="fa-solid fa-grip text-[#D4AF37] text-2xl"></i>
            </button>

            @foreach($categories as $category)
            <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
               class="flex flex-col flex-shrink-0 w-24 md:w-28 space-y-1 group">
                <div class="relative h-20 md:h-24 bg-[#E0E0E0] rounded overflow-hidden border border-[#E0E0E0]">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-full h-full bg-[#E0E0E0]"></div>
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
        <div class="flex items-center gap-1 px-3 py-3.5 border-b border-[#E0E0E0] flex-shrink-0">
            <!-- Back button — visible when drilled into a subcategory level -->
            <button type="button"
                    x-show="stack.length > 1"
                    @click="pop()"
                    style="display: none;"
                    class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 transition-colors flex-shrink-0">
                <i class="fa-solid fa-chevron-left text-[#37474F] text-sm"></i>
            </button>

            <h2 class="text-[13px] font-semibold text-[#1A1A1A] flex-1 truncate px-1"
                x-text="currentLevel.title"></h2>

            <button type="button"
                    @click="categoryPanelOpen = false"
                    class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 transition-colors flex-shrink-0">
                <i class="fa-solid fa-xmark text-[#37474F] text-sm"></i>
            </button>
        </div>

        <!-- Category List for current level -->
        <nav class="flex-1 overflow-y-auto py-1">
            <template x-for="item in currentLevel.items" :key="item.id">
                <div class="border-b border-[#E0E0E0]">
                    <div class="flex items-center">
                        <!-- Category name — navigates to listing page -->
                        <a :href="baseUrl + '?category=' + item.slug"
                           class="flex-1 flex items-center gap-3 px-4 py-2.5 text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors min-w-0">
                            <template x-if="item.icon_url">
                                <img :src="item.icon_url"
                                     :alt="item.name"
                                     class="w-7 h-7 rounded object-cover flex-shrink-0">
                            </template>
                            <template x-if="!item.icon_url">
                                <div class="w-7 h-7 rounded bg-[#E0E0E0] flex-shrink-0"></div>
                            </template>
                            <span class="font-medium truncate" x-text="item.name"></span>
                        </a>

                        <!-- Drill-down arrow — opens subcategory panel -->
                        <template x-if="item.children && item.children.length > 0">
                            <button type="button"
                                    @click.stop="push(item)"
                                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 text-[#37474F] hover:text-[#D4AF37] hover:bg-[#D4AF37]/5 transition-colors mr-1">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
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
