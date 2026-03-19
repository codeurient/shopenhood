@props(['listingTypes', 'currentType' => 'sell'])

<!-- Listing Type Tabs -->
<div class="bg-gray-700 border-b border-gray-600">
    <div class="max-w-[1250px] mx-auto px-0 md:px-6">
        <div class="flex gap-0 overflow-x-auto scrollbar-hide justify-between">
            @foreach($listingTypes as $type)
            @php $isActive = $type->slug === $currentType; @endphp
            <button type="button"
                    onclick="filterByType('{{ $type->slug }}')"
                    data-type="{{ $type->slug }}"
                    title="{{ $type->name }}"
                    class="listing-type-tab flex-1 min-w-0 px-3 md:px-6 py-3 hover:bg-gray-600 transition-colors {{ $isActive ? 'border-b-2 border-[#D4AF37]' : '' }}">
                <span class="flex flex-col items-center gap-1">
                    {{-- Mobile: icon only --}}
                    <span class="listing-type-icon md:hidden text-lg leading-none transition-colors"
                          style="color: {{ $isActive ? '#D4AF37' : '#ffffff' }}">
                        @if($type->icon)
                            {!! $type->icon !!}
                        @else
                            <i class="fa-solid fa-tag"></i>
                        @endif
                    </span>
                    {{-- Desktop: text label --}}
                    <span class="hidden md:inline text-sm font-semibold text-white whitespace-nowrap">{{ $type->name }}</span>
                </span>
            </button>
            @endforeach
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
(function () {
    const GOLD = '#D4AF37';
    const WHITE = '#ffffff';

    function updateTabStyles(activeSlug) {
        document.querySelectorAll('.listing-type-tab').forEach(function (btn) {
            const slug = btn.dataset.type;
            const isActive = slug === activeSlug;
            const iconSpan = btn.querySelector('.listing-type-icon');

            // Border
            btn.classList.toggle('border-b-2', isActive);
            btn.classList.toggle('border-[#D4AF37]', isActive);

            // Icon colour
            if (iconSpan) {
                iconSpan.style.color = isActive ? GOLD : WHITE;
            }
        });
    }

    // Patch filterByType to also update icon colours immediately
    const _originalFilter = window.filterByType;
    window.filterByType = function (slug) {
        updateTabStyles(slug);
        if (typeof _originalFilter === 'function') {
            _originalFilter(slug);
        }
    };
}());
</script>
