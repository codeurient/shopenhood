@props(['categories'])

<!-- Horizontal Category Scroll -->
<div class="bg-white border-b border-gray-200" x-data="{ categoryPanelOpen: false }">
    <div class="max-w-[1250px] mx-auto">
        <div class="flex gap-3 px-4 md:px-6 py-4 overflow-x-auto scrollbar-hide">
            <!-- Grid Button (Opens Full Category Panel) -->
            <button type="button"
                    @click="categoryPanelOpen = true"
                    class="flex items-center justify-center flex-shrink-0 w-16 md:w-20 h-20 md:h-24 bg-gray-700 rounded-xl hover:bg-gray-800 transition-colors">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>

            @foreach($categories as $category)
            <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
               class="flex flex-col flex-shrink-0 w-24 md:w-28 space-y-1 group">
                <!-- Category Card with Image -->
                <div class="relative h-20 md:h-24 bg-gray-200 rounded-xl overflow-hidden">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400"></div>
                    @endif
                    <!-- Category Name Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 flex items-end p-2 transition-colors">
                        <span class="text-xs font-semibold text-white truncate">{{ $category->name }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Full-Screen Category Panel -->
    <div x-show="categoryPanelOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="categoryPanelOpen = false"
         class="fixed inset-0 z-50 bg-white overflow-y-auto"
         style="display: none;">

        <!-- Panel Header -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
            <div class="max-w-screen-xl mx-auto flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">All Categories</h2>
                <button type="button"
                        @click="categoryPanelOpen = false"
                        class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Panel Content -->
        <div class="p-4 md:p-8 max-w-screen-xl mx-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4">
                @foreach($categories as $category)
                <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
                   class="flex flex-col p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-16 h-16 object-contain mx-auto mb-3 group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg mx-auto mb-3"></div>
                    @endif
                    <h3 class="text-sm font-semibold text-gray-900 text-center">{{ $category->name }}</h3>
                    @if($category->listings_count > 0)
                        <p class="text-xs text-gray-500 text-center mt-1">{{ $category->listings_count }} items</p>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
