<!-- Responsive Header -->
<header class="sticky top-0 z-40 bg-[#000000] shadow-sm"
        x-data="cartPanel()"
        x-init="init()">

    <div class="flex items-center gap-2 px-3 md:px-6 py-3 md:h-14 max-w-[1250px] mx-auto">

        <!-- Logo (desktop only) -->
        <a href="{{ route('home') }}"
           class="hidden md:flex items-center flex-shrink-0 text-[#D4AF37] font-bold text-xl tracking-tight hover:brightness-110 transition whitespace-nowrap ml-1">
            {{ config('app.name', 'Shopenhood') }}
        </a>

        <!-- Search Bar (desktop only) -->
        <div class="hidden md:flex flex-1"
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
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-[#37474F] text-sm"></i>
                    </div>
                    <input type="text"
                           name="search"
                           x-model="query"
                           @input="onInput()"
                           @focus="query.length >= 2 && suggestions.length > 0 && (open = true)"
                           autocomplete="off"
                           placeholder="Search for items or services..."
                           class="w-full pl-9 pr-9 h-[34px] text-[13px] bg-white border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] transition-all">
                    <!-- Filter toggle button (all screen sizes) -->
                    <button type="button"
                            @click.stop="window.dispatchEvent(new CustomEvent('toggle-filter-panel'))"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#37474F] hover:text-[#D4AF37] transition-colors"
                            title="Filters">
                        <i class="fa-solid fa-filter text-sm"></i>
                    </button>

                    <!-- Suggestions Dropdown -->
                    <div x-show="open"
                         @click.outside="close()"
                         style="display: none;"
                         class="absolute left-0 right-0 top-full mt-1 bg-white rounded border border-[#E0E0E0] shadow-xl z-50 overflow-hidden">

                        <!-- Loading -->
                        <div x-show="loading" class="flex items-center justify-center py-6">
                            <i class="fa-solid fa-spinner animate-spin text-[#37474F] text-lg"></i>
                        </div>

                        <!-- Results -->
                        <div x-show="!loading">
                            <!-- No results -->
                            <div x-show="suggestions.length === 0" class="px-4 py-5 text-[13px] text-center text-[#37474F]">
                                No results for "<span class="font-medium text-[#1A1A1A]" x-text="query"></span>"
                            </div>

                            <!-- Suggestion rows -->
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
                                    <span x-show="item.base_price" class="text-[13px] font-semibold text-[#1A1A1A] flex-shrink-0" x-text="fmt(item.base_price, item.currency)"></span>
                                </a>
                            </template>

                            <!-- View all results -->
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

        <!-- Mobile quick links: Sell | Buy | Gift -->
        <div class="md:hidden flex flex-1 items-center justify-center gap-6">
            @auth
            <a href="{{ route('user.listings.create') }}"
               class="flex flex-col items-center gap-0.5 text-[#E0E0E0] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-tag text-base"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Sell</span>
            </a>
            @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center gap-0.5 text-[#E0E0E0] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-tag text-base"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Sell</span>
            </a>
            @endauth
            <a href="{{ route('listings.index') }}"
               class="flex flex-col items-center gap-0.5 text-[#E0E0E0] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-bag-shopping text-base"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Buy</span>
            </a>
            <a href="{{ route('listings.index', ['category' => 'gifts']) }}"
               class="flex flex-col items-center gap-0.5 text-[#E0E0E0] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-gift text-base"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Gift</span>
            </a>
        </div>

        <!-- Right: Favorites (auth) + Cart + Account -->
        <div class="flex items-center gap-1 flex-shrink-0">

            @auth
            <a href="{{ route('user.favorites.index') }}"
               class="relative flex items-center justify-center w-9 h-9 rounded hover:bg-white/10 transition-colors">
                <i class="fa-regular fa-heart text-[#E0E0E0] text-lg"></i>
            </a>
            @endauth

            <!-- Cart -->
            <button @click="openCart()"
                    type="button"
                    class="relative flex items-center justify-center w-9 h-9 rounded hover:bg-white/10 transition-colors">
                <i class="fa-solid fa-cart-shopping text-[#E0E0E0] text-lg"></i>
                <span x-show="count > 0" x-cloak
                      style="display:none;"
                      class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-[#000000] bg-[#D4AF37] rounded-full"
                      x-text="count"></span>
            </button>

            <!-- Account Panel Button -->
            <button type="button"
                    @click="accountPanelOpen = true"
                    class="flex items-center justify-center w-9 h-9 rounded hover:bg-white/10 transition-colors">
                @auth
                    <div class="w-7 h-7 rounded-full bg-[#D4AF37] flex items-center justify-center text-xs font-bold text-[#000000] select-none">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @else
                    <i class="fa-regular fa-user text-[#E0E0E0] text-lg"></i>
                @endauth
            </button>
        </div>
    </div>

    <!-- Cart Drawer Overlay -->
    <div x-show="open"
         x-cloak
         style="display:none;"
         class="fixed inset-0 z-50 flex justify-end"
         @keydown.escape.window="open = false">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="open = false"></div>

        <!-- Panel -->
        <div class="relative w-full sm:max-w-sm bg-white flex flex-col shadow-xl h-full"
             @click.stop>

            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-[#E0E0E0] flex-shrink-0">
                <h2 class="text-[15px] font-bold text-[#1A1A1A]">Cart</h2>
                <button @click="open = false" class="flex items-center justify-center w-7 h-7 text-[#37474F] hover:text-[#1A1A1A] transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Loading state -->
            <div x-show="loading" class="flex-1 flex items-center justify-center py-12">
                <i class="fa-solid fa-spinner animate-spin text-[#E0E0E0] text-3xl"></i>
            </div>

            <!-- Empty state -->
            <div x-show="!loading && items.length === 0" class="flex-1 flex flex-col items-center justify-center py-12">
                <i class="fa-solid fa-cart-shopping text-5xl text-[#E0E0E0] mb-3"></i>
                <p class="text-[13px] text-[#37474F]">Your cart is empty</p>
            </div>

            <!-- Items list -->
            <div x-show="!loading && items.length > 0" class="flex flex-col flex-1 min-h-0">
                <!-- Select-all bar -->
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-[#E0E0E0] flex-shrink-0">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               id="select-all-cart"
                               :checked="allSelected"
                               @change="toggleSelectAll($event.target.checked)"
                               class="w-4 h-4 rounded border-[#E0E0E0]">
                        <span class="text-[13px] text-[#1A1A1A]" x-text="`Selected ${selectedCount} of ${items.length}`"></span>
                    </label>
                    <button @click="deleteSelected()"
                            :disabled="selectedCount === 0"
                            class="p-1.5 text-[#37474F] hover:text-[#C0392B] disabled:opacity-30 transition">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>

                <!-- Scrollable items -->
                <div class="flex-1 overflow-y-auto divide-y divide-[#E0E0E0]">
                    <template x-for="item in items" :key="item.id">
                        <div class="flex gap-3 px-4 py-3">
                            <!-- Checkbox -->
                            <div class="flex-shrink-0 pt-1">
                                <input type="checkbox"
                                       :id="`cart-item-${item.id}`"
                                       :checked="item.is_selected"
                                       @change="toggleItem(item, $event.target.checked)"
                                       class="w-4 h-4 rounded border-[#E0E0E0]">
                            </div>

                            <!-- Image -->
                            <div class="flex-shrink-0">
                                <template x-if="item.image_url">
                                    <img :src="item.image_url" :alt="item.title"
                                         class="w-16 h-16 object-cover rounded border border-[#E0E0E0]">
                                </template>
                                <template x-if="!item.image_url">
                                    <div class="w-16 h-16 bg-gray-50 rounded border border-[#E0E0E0] flex items-center justify-center">
                                        <i class="fa-regular fa-image text-[#E0E0E0] text-2xl"></i>
                                    </div>
                                </template>
                            </div>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-1">
                                    <p class="text-[13px] font-medium text-[#1A1A1A] leading-tight line-clamp-2" x-text="item.title"></p>
                                    <button @click="removeItem(item)"
                                            class="flex-shrink-0 p-1 text-[#37474F] hover:text-[#C0392B] transition ml-1">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>

                                <p class="text-[12px] text-[#37474F] mt-0.5" x-text="'Seller: ' + item.seller_name"></p>

                                <!-- Qty + price row -->
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center border border-[#E0E0E0] rounded overflow-hidden">
                                        <button @click="changeQty(item, -1)"
                                                :disabled="item.quantity <= 1"
                                                class="w-7 h-7 flex items-center justify-center text-[#37474F] hover:bg-gray-50 disabled:opacity-40 transition font-bold text-sm">−</button>
                                        <span class="w-8 text-center text-[13px] font-medium text-[#1A1A1A]" x-text="item.quantity"></span>
                                        <button @click="changeQty(item, 1)"
                                                :disabled="item.quantity >= 99 || (item.max_qty !== null && item.quantity >= item.max_qty)"
                                                class="w-7 h-7 flex items-center justify-center text-[#37474F] hover:bg-gray-50 disabled:opacity-40 transition font-bold text-sm">+</button>
                                    </div>

                                    <div class="text-right">
                                        <template x-if="item.discount_price">
                                            <div>
                                                <span class="text-[12px] text-[#37474F] line-through" x-text="fmt(item.base_price * item.quantity, item.currency)"></span>
                                                <span class="block text-[13px] font-bold text-[#C0392B]" x-text="fmt(item.unit_price * item.quantity, item.currency)"></span>
                                            </div>
                                        </template>
                                        <template x-if="!item.discount_price">
                                            <span class="text-[13px] font-bold text-[#1A1A1A]" x-text="fmt(item.unit_price * item.quantity, item.currency)"></span>
                                        </template>
                                    </div>
                                </div>

                                <template x-if="item.has_delivery && item.delivery_cost > 0">
                                    <p class="text-[12px] text-[#37474F] mt-1">+ Delivery: <span class="font-medium" x-text="fmt(item.delivery_cost, item.currency)"></span></p>
                                </template>
                                <template x-if="item.has_delivery && item.delivery_cost === 0">
                                    <p class="text-[12px] text-green-600 font-medium mt-1">Free delivery</p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer: Total + Checkout -->
                <div class="flex-shrink-0 border-t border-[#E0E0E0] bg-white px-4 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[14px] font-semibold text-[#1A1A1A]">Total</span>
                        <span class="text-[14px] font-bold text-[#1A1A1A]" x-text="fmt(total, 'USD')"></span>
                    </div>
                    <a href="{{ route('checkout.index') }}"
                       :class="selectedCount === 0 ? 'pointer-events-none opacity-50' : ''"
                       class="flex items-center justify-center gap-2 w-full h-10 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                        Place Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Search Bar (outside header, sticky below it) -->
<div class="md:hidden sticky top-[60px] z-[39] bg-white border-b border-[#E0E0E0] px-3 py-2 shadow-sm"
     x-data="{
         query: '{{ request('search') }}',
         suggestions: [],
         loading: false,
         open: false,
         _timer: null,
         onInput() {
             if (this.query.length < 2) { this.open = false; this.suggestions = []; return; }
             this.loading = true; this.open = true;
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
    <form action="{{ route('listings.index') }}" method="GET">
        <div class="relative">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-[#37474F] text-sm"></i>
            </div>
            <input type="text"
                   name="search"
                   x-model="query"
                   @input="onInput()"
                   @focus="query.length >= 2 && suggestions.length > 0 && (open = true)"
                   autocomplete="off"
                   placeholder="Search for items or services..."
                   class="w-full pl-9 pr-9 h-[36px] text-[13px] bg-white border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] transition-all">
            <button type="button"
                    @click.stop="window.dispatchEvent(new CustomEvent('toggle-filter-panel'))"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#37474F] hover:text-[#D4AF37] transition-colors"
                    title="Filters">
                <i class="fa-solid fa-filter text-sm"></i>
            </button>

            <!-- Suggestions Dropdown -->
            <div x-show="open"
                 @click.outside="close()"
                 style="display:none;"
                 class="absolute left-0 right-0 top-full mt-1 bg-white rounded border border-[#E0E0E0] shadow-xl z-50 overflow-hidden">
                <div x-show="loading" class="flex items-center justify-center py-6">
                    <i class="fa-solid fa-spinner animate-spin text-[#37474F] text-lg"></i>
                </div>
                <div x-show="!loading">
                    <div x-show="suggestions.length === 0" class="px-4 py-5 text-[13px] text-center text-[#37474F]">
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
                            <span x-show="item.base_price" class="text-[13px] font-semibold text-[#1A1A1A] flex-shrink-0" x-text="fmt(item.base_price, item.currency)"></span>
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

<script>
function cartPanel() {
    return {
        open: false,
        loading: false,
        items: [],
        total: 0,
        count: 0,

        get selectedCount() {
            return this.items.filter(i => i.is_selected).length;
        },
        get allSelected() {
            return this.items.length > 0 && this.items.every(i => i.is_selected);
        },

        init() {
            this.$watch('open', (value) => {
                document.body.style.overflow = value ? 'hidden' : '';
            });
            @auth
                this.fetchCount();
                window.addEventListener('cart-updated', () => this.fetchCount());
            @endauth
        },

        fetchCount() {
            fetch('/api/cart', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null)
                .then(data => { if (data) { this.count = data.count; this.total = data.total; } })
                .catch(() => {});
        },

        openCart() {
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest
            this.open = true;
            this.loadItems();
        },

        loadItems() {
            this.loading = true;
            fetch('/api/cart', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    this.items = data.items;
                    this.total = data.total;
                    this.count = data.count;
                    this.loading = false;
                })
                .catch(() => { this.loading = false; });
        },

        _syncCheckout() {
            if (!window.location.pathname.startsWith('/checkout')) { return; }
            if (this.selectedCount === 0) {
                window.location.replace('{{ route('home') }}');
            } else {
                window.location.reload();
            }
        },

        toggleSelectAll(checked) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch('/api/cart/select-all', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ selected: checked }),
            }).then(() => {
                this.items.forEach(i => i.is_selected = checked);
                this.recalcTotal();
                this._syncCheckout();
            });
        },

        toggleItem(item, checked) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            item.is_selected = checked;
            this.recalcTotal();
            fetch(`/api/cart/${item.id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ is_selected: checked }),
            }).then(() => this._syncCheckout());
        },

        changeQty(item, delta) {
            const newQty = item.quantity + delta;
            if (newQty < 1 || newQty > 99) { return; }
            if (delta > 0 && item.max_qty !== null && newQty > item.max_qty) { return; }
            item.quantity = newQty;
            this.recalcTotal();
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch(`/api/cart/${item.id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ quantity: newQty }),
            }).then(() => {
                if (window.location.pathname.startsWith('/checkout')) {
                    window.location.reload();
                }
            });
        },

        removeItem(item) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch(`/api/cart/${item.id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            }).then(r => r.json()).then(data => {
                this.items = this.items.filter(i => i.id !== item.id);
                this.count = data.count;
                this.recalcTotal();
                this._syncCheckout();
            });
        },

        deleteSelected() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            fetch('/api/cart/selected', {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            }).then(r => r.json()).then(data => {
                this.items = this.items.filter(i => !i.is_selected);
                this.count = data.count;
                this.recalcTotal();
                this._syncCheckout();
            });
        },

        recalcTotal() {
            this.total = this.items
                .filter(i => i.is_selected)
                .reduce((sum, i) => sum + i.unit_price * i.quantity, 0);
        },

        fmt(amount, currency) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency || 'USD',
                minimumFractionDigits: 2,
            }).format(amount);
        },
    };
}
</script>
