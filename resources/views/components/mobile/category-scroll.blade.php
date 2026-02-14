@props(['categories'])

<!-- Horizontal Category Scroll -->
<div class="bg-white border-b border-gray-200">
    <div class="flex gap-6 px-4 py-4 overflow-x-auto scrollbar-hide">
        <!-- All Categories -->
        <a href="{{ route('listings.index') }}"
           class="flex flex-col items-center justify-center flex-shrink-0 space-y-2 group">
            <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-md group-hover:shadow-lg transition-all">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-700 text-center max-w-[80px] truncate">All</span>
        </a>

        @foreach($categories as $category)
        <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
           class="flex flex-col items-center justify-center flex-shrink-0 space-y-2 group">
            <div class="flex items-center justify-center w-16 h-16 bg-gray-100 rounded-2xl group-hover:bg-primary-50 transition-colors">
                @if($category->icon)
                    <img src="{{ asset('storage/' . $category->icon) }}"
                         alt="{{ $category->name }}"
                         class="w-8 h-8 object-contain">
                @else
                    <!-- Placeholder Icon -->
                    <div class="w-10 h-10 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg"></div>
                @endif
            </div>
            <span class="text-xs font-medium text-gray-700 text-center max-w-[80px] truncate">{{ $category->name }}</span>
        </a>
        @endforeach
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
