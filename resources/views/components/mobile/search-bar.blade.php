<!-- Mobile Search Bar -->
<div class="px-3 py-2.5 bg-white border-b border-[#E0E0E0]"
     x-data="{
         query: '{{ request('search') }}',
         suggestions: [],
         loading: false,
         open: false,
         _timer: null,
         onInput() {
             if (this.query.length < 2) {
                 this.open = false;
                 this.suggestions = [];
                 return;
             }
             this.loading = true;
             this.open = true;
             clearTimeout(this._timer);
             this._timer = setTimeout(() => this.fetchSuggestions(), 300);
         },
         fetchSuggestions() {
             fetch('/search/suggestions?q=' + encodeURIComponent(this.query), {
                 headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
             })
             .then(r => r.json())
             .then(data => { this.suggestions = data; this.loading = false; })
             .catch(() => { this.suggestions = []; this.loading = false; });
         },
         close() { this.open = false; },
         fmt(amount, currency) {
             if (!amount) return '';
             return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(amount);
         }
     }"
     @keydown.escape.window="close()">
    <form action="{{ route('listings.index') }}" method="GET" class="w-full">
        <div class="relative w-full">

            {{-- Search icon (left) --}}
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-[#37474F] text-sm"></i>
            </div>

            {{-- Input --}}
            <input type="text"
                   name="search"
                   x-model="query"
                   @input="onInput()"
                   @focus="query.length >= 2 && suggestions.length > 0 && (open = true)"
                   autocomplete="off"
                   placeholder="Search for items or services..."
                   class="w-full pl-9 pr-10 h-[38px] text-[13px] bg-white border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] placeholder-[#37474F] text-[#1A1A1A] transition-colors">

            {{-- Filter icon (right) --}}
            <button type="button"
                    @click.stop="window.dispatchEvent(new CustomEvent('toggle-filter-panel'))"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#37474F] hover:text-[#D4AF37] transition-colors"
                    title="Filters">
                <i class="fa-solid fa-filter text-sm"></i>
            </button>

            {{-- Suggestions Dropdown --}}
            <div x-show="open"
                 @click.outside="close()"
                 style="display:none;"
                 class="absolute left-0 right-0 top-full mt-1 bg-white rounded border border-[#E0E0E0] shadow-xl z-50 overflow-hidden">

                {{-- Loading --}}
                <div x-show="loading" class="flex items-center justify-center py-6">
                    <i class="fa-solid fa-spinner animate-spin text-[#37474F] text-lg"></i>
                </div>

                {{-- Results --}}
                <div x-show="!loading">
                    <div x-show="suggestions.length === 0"
                         class="px-4 py-5 text-[13px] text-center text-[#37474F]">
                        No results for "<span class="font-medium text-[#1A1A1A]" x-text="query"></span>"
                    </div>

                    <template x-for="item in suggestions" :key="item.url">
                        <a :href="item.url"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-[#D4AF37]/5 transition-colors border-b border-[#E0E0E0] last:border-0">
                            <div class="flex-shrink-0 w-10 h-10 rounded overflow-hidden bg-gray-50 border border-[#E0E0E0]">
                                <template x-if="item.image_url">
                                    <img :src="item.image_url" :alt="item.title" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.image_url">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fa-regular fa-image text-[#E0E0E0] text-lg"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-medium text-[#1A1A1A] truncate" x-text="item.title"></p>
                                <p class="text-[12px] text-[#37474F] truncate" x-text="item.category_name"></p>
                            </div>
                            <span x-show="item.base_price"
                                  class="text-[13px] font-semibold text-[#1A1A1A] flex-shrink-0"
                                  x-text="fmt(item.base_price, item.currency)"></span>
                        </a>
                    </template>

                    <a x-show="suggestions.length > 0"
                       :href="'{{ route('listings.index') }}?search=' + encodeURIComponent(query)"
                       class="flex items-center justify-center gap-1.5 px-4 py-3 text-[13px] font-medium text-[#D4AF37] hover:bg-[#D4AF37]/5 border-t border-[#E0E0E0] transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        View all results for "<span x-text="query"></span>"
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
