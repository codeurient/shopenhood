@props(['listingTypes', 'currentType' => 'sell'])

<!-- Listing Type Tabs -->
<div class="bg-gray-700 border-b border-gray-600">
    <div class="max-w-screen-2xl mx-auto px-0 md:px-6">
        <div class="flex gap-0 overflow-x-auto scrollbar-hide justify-between">
            @foreach($listingTypes as $type)
            <button type="button"
                    onclick="filterByType('{{ $type->slug }}')"
                    data-type="{{ $type->slug }}"
                    class="listing-type-tab flex-1 min-w-[80px] md:flex-none md:min-w-0 px-4 md:px-6 py-3 text-sm font-semibold text-white hover:bg-gray-600 transition-colors whitespace-nowrap {{ $type->slug === $currentType ? 'border-b-2 border-primary-500' : '' }}">
                {{ $type->name }}
            </button>
            @endforeach
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
