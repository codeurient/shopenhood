<!-- Sidebar Overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-50 bg-black bg-opacity-50"
     style="display: none;">
</div>

<!-- Sidebar -->
<aside x-show="sidebarOpen"
       x-trap.inert="sidebarOpen"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl overflow-y-auto flex flex-col"
       role="dialog"
       aria-modal="true"
       aria-label="Categories"
       style="display: none;">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-200 bg-gray-50">
        <a href="{{ route('home') }}" class="text-lg font-bold text-primary-600">
            {{ config('app.name', 'Shopenhood') }}
        </a>
        <button @click="sidebarOpen = false"
                type="button"
                class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Categories Navigation -->
    <nav class="flex-1 px-2 py-2">
        <p class="px-3 pt-1 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Browse Categories</p>

        @php
            $sidebarCategories = \App\Models\Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);
        @endphp

        @forelse($sidebarCategories as $cat)
        <a href="{{ route('listings.index', ['category' => $cat->slug]) }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition-colors">
            <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            {{ $cat->name }}
        </a>
        @empty
        <p class="px-3 py-2 text-sm text-gray-400">No categories yet.</p>
        @endforelse

        <div class="my-2 border-t border-gray-100"></div>

        <a href="{{ route('listings.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition-colors font-medium">
            <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            All Listings
        </a>
    </nav>
</aside>
