@props(['categories'])

<!-- Horizontal Category Scroll -->
<div class="bg-white border-b border-gray-200" x-data="{ categoryPanelOpen: false }">
    <div class="flex gap-3 px-4 py-4 overflow-x-auto scrollbar-hide">
        <!-- Grid Button (Opens Full Category Panel) -->
        <button type="button"
                @click="categoryPanelOpen = true"
                class="flex items-center justify-center flex-shrink-0 w-16 h-17 bg-gray-700 rounded-xl hover:bg-gray-800 transition-colors">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </button>

        @foreach($categories as $category)
        <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
           class="flex flex-col flex-shrink-0 w-24 space-y-1 group">
            <!-- Category Card with Image -->
            <div class="relative h-20 bg-gray-200 rounded-xl overflow-hidden">
                @if($category->icon)
                    <img src="{{ asset('storage/' . $category->icon) }}"
                         alt="{{ $category->name }}"
                         class="w-full h-full object-cover">
                @else
                    <!-- Placeholder Image -->
                    <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400"></div>
                @endif
                <!-- Category Name Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-20 flex items-end p-2">
                    <span class="text-xs font-semibold text-white truncate">{{ $category->name }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Full-Screen Category Panel -->
    <div x-show="categoryPanelOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         @click.away="categoryPanelOpen = false"
         class="fixed inset-0 z-50 bg-white overflow-y-auto"
         style="display: none;">

        <!-- Panel Header -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">All Categories</h2>
            <button type="button"
                    @click="categoryPanelOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Panel Content -->
        <div class="p-4">
            <div class="grid grid-cols-2 gap-3">
                @foreach($categories as $category)
                <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
                   class="flex flex-col p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    @if($category->icon)
                        <img src="{{ asset('storage/' . $category->icon) }}"
                             alt="{{ $category->name }}"
                             class="w-16 h-16 object-contain mx-auto mb-3">
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
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
