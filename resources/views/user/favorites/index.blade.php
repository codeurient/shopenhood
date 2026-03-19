<x-guest-layout>
    <x-slot name="title">My Favorites — {{ config('app.name') }}</x-slot>

    <div class="max-w-[1250px] mx-auto px-4 md:px-6 pt-4 pb-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3 mb-4">
            <i class="fa-solid fa-heart text-red-500 text-xl"></i>
            <h1 class="text-lg font-bold text-[#1A1A1A]">My Favorites</h1>
            <span class="text-sm text-[#37474F]">({{ $listings->total() }})</span>
        </div>

        @if($listings->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="fa-regular fa-heart text-[#E0E0E0] mb-4" style="font-size: 4rem;"></i>
                <h2 class="text-base font-semibold text-[#1A1A1A] mb-1">No favorites yet</h2>
                <p class="text-sm text-[#37474F] mb-6">Tap the heart on any listing to save it here.</p>
                <a href="{{ route('listings.index') }}"
                   class="inline-flex items-center justify-center px-5 h-[38px] bg-[#D4AF37] text-[#000000] text-sm font-semibold rounded hover:brightness-110 transition-colors">
                    Browse Listings
                </a>
            </div>
        @else
            <!-- Listings Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
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
