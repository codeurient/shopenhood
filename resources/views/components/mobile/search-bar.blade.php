<!-- Mobile Search Bar -->
<div class="px-4 py-3 bg-white"
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
    <form action="{{ route('listings.index') }}" method="GET" class="relative">
        <input type="text"
               name="search"
               x-model="query"
               @input="onInput()"
               @focus="query.length >= 2 && suggestions.length > 0 && (open = true)"
               autocomplete="off"
               placeholder="Search for items or services..."
               class="w-full pl-11 pr-4 py-3 text-sm bg-gray-100 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
        <button type="submit"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>

        <!-- Suggestions Dropdown -->
        <div x-show="open"
             @click.outside="close()"
             style="display: none;"
             class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">

            <!-- Loading -->
            <div x-show="loading" class="flex items-center justify-center py-6">
                <svg class="animate-spin w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Results -->
            <div x-show="!loading">
                <!-- No results -->
                <div x-show="suggestions.length === 0" class="px-4 py-5 text-sm text-center text-gray-500">
                    No results for "<span class="font-medium text-gray-700" x-text="query"></span>"
                </div>

                <!-- Suggestion rows -->
                <template x-for="item in suggestions" :key="item.url">
                    <a :href="item.url"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                        <!-- Thumbnail -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-gray-100">
                            <template x-if="item.image_url">
                                <img :src="item.image_url" :alt="item.title" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.image_url">
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                        </div>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                            <p class="text-xs text-gray-400 truncate" x-text="item.category_name"></p>
                        </div>
                        <!-- Price -->
                        <span x-show="item.base_price" class="text-sm font-semibold text-gray-800 flex-shrink-0" x-text="fmt(item.base_price, item.currency)"></span>
                    </a>
                </template>

                <!-- View all results -->
                <a x-show="suggestions.length > 0"
                   :href="'{{ route('listings.index') }}?search=' + encodeURIComponent(query)"
                   class="flex items-center justify-center gap-1.5 px-4 py-3 text-sm font-medium text-primary-600 hover:bg-primary-50 border-t border-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    View all results for "<span x-text="query"></span>"
                </a>
            </div>
        </div>
    </form>
</div>
