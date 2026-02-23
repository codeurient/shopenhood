<x-guest-layout>
    <x-slot name="title">My Favorites — {{ config('app.name') }}</x-slot>

    <div class="px-4 pt-4 pb-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <h1 class="text-lg font-bold text-gray-900">My Favorites</h1>
            <span class="text-sm text-gray-500">({{ $listings->total() }})</span>
        </div>

        @if($listings->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h2 class="text-base font-semibold text-gray-700 mb-1">No favorites yet</h2>
                <p class="text-sm text-gray-500 mb-6">Tap the heart on any listing to save it here.</p>
                <a href="{{ route('listings.index') }}"
                   class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                    Browse Listings
                </a>
            </div>
        @else
            <!-- Listings Grid -->
            <div class="grid grid-cols-2 gap-3">
                @foreach($listings as $listing)
                    <x-mobile.listing-card :listing="$listing" />
                @endforeach
            </div>

            <!-- Pagination -->
            @if($listings->hasPages())
                <div class="mt-6">
                    {{ $listings->links() }}
                </div>
            @endif
        @endif
    </div>
</x-guest-layout>
