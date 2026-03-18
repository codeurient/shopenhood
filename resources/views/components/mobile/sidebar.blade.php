<!-- Sidebar Overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-50 bg-black/50"
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
       class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-[#E0E0E0] shadow-xl overflow-y-auto flex flex-col"
       role="dialog"
       aria-modal="true"
       aria-label="Categories"
       style="display: none;">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-[#E0E0E0] bg-white">
        <a href="{{ route('home') }}" class="text-[#D4AF37] font-bold text-lg tracking-tight hover:brightness-110 transition">
            {{ config('app.name', 'Shopenhood') }}
        </a>
        <button @click="sidebarOpen = false"
                type="button"
                class="flex items-center justify-center w-7 h-7 rounded text-[#37474F] hover:bg-gray-100 hover:text-[#1A1A1A] transition">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Categories Navigation -->
    <nav class="flex-1 px-2 py-2">
        <p class="px-3 pt-1 pb-1 text-[10px] font-semibold text-[#37474F] uppercase tracking-wider">Browse Categories</p>

        @php
            $sidebarCategories = \App\Models\Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);
        @endphp

        @forelse($sidebarCategories as $cat)
        <a href="{{ route('listings.index', ['category' => $cat->slug]) }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors">
            <i class="fa-solid fa-tag text-xs text-[#37474F] flex-shrink-0"></i>
            {{ $cat->name }}
        </a>
        @empty
        <p class="px-3 py-2 text-[13px] text-[#37474F]">No categories yet.</p>
        @endforelse

        <div class="my-2 border-t border-[#E0E0E0]"></div>

        <a href="{{ route('listings.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] font-medium text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors">
            <i class="fa-solid fa-list text-xs text-[#37474F] flex-shrink-0"></i>
            All Listings
        </a>
    </nav>
</aside>
