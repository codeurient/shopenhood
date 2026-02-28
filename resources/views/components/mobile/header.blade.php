<!-- Responsive Header: compact on mobile, full nav on desktop -->
<header class="sticky top-0 z-40 bg-gray-800 shadow-sm"
        x-data="cartPanel()"
        x-init="init()">

    <div class="flex items-center gap-3 px-4 md:px-6 py-3 md:h-16 max-w-[1250px] mx-auto">

        <!-- Hamburger Menu Button -->
        <button @click="sidebarOpen = true"
                type="button"
                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-700 transition-colors flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Logo (desktop only) -->
        <a href="{{ route('home') }}"
           class="hidden md:flex items-center flex-shrink-0 text-white font-bold text-xl tracking-tight hover:text-primary-300 transition-colors whitespace-nowrap">
            {{ config('app.name', 'Shopenhood') }}
        </a>

<!-- Search Bar -->
        <div class="flex-1">
            <form action="{{ route('listings.index') }}" method="GET">
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 text-primary-600 md:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search for items or services..."
                           class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    <!-- Filter button (mobile only) -->
                    <button type="button"
                            onclick="toggleFilterPanel()"
                            class="md:hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600 transition-colors">
                        <i class="fa-solid fa-filter text-lg"></i>
                    </button>
                    <!-- Search submit button (desktop only, inside input) -->
                    <button type="submit"
                            class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 items-center justify-center text-gray-400 hover:text-primary-600 transition-colors">
                        <i class="fa-solid fa-filter text-lg"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- User Account (desktop only) -->
        @auth
        <button type="button"
                @click="accountPanelOpen = true"
                class="hidden md:flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-700 text-white transition-colors flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-sm font-bold select-none">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </button>
        @else
        <a href="{{ route('login') }}"
           class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-700 text-white text-sm transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>
        @endauth

        <!-- Favorites Icon (authenticated users) -->
        @auth
        <a href="{{ route('user.favorites.index') }}"
           class="relative flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-700 transition-colors flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </a>
        @endauth

        <!-- Cart Icon with Badge (both mobile and desktop) -->
        <button @click="openCart()"
                type="button"
                class="relative flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-700 transition-colors flex-shrink-0">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2 12h10l2-8H6M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            <span x-show="count > 0" x-cloak
                  style="display:none;"
                  class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-danger-500 rounded-full"
                  x-text="count"></span>
        </button>
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
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Cart</h2>
                <button @click="open = false" class="p-1 text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Loading state -->
            <div x-show="loading" class="flex-1 flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Empty state -->
            <div x-show="!loading && items.length === 0" class="flex-1 flex flex-col items-center justify-center py-12 text-gray-400">
                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l2 12h10l2-8H6M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
                <p class="text-sm">Your cart is empty</p>
            </div>

            <!-- Items list -->
            <div x-show="!loading && items.length > 0" class="flex flex-col flex-1 min-h-0">
                <!-- Select-all bar -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               id="select-all-cart"
                               :checked="allSelected"
                               @change="toggleSelectAll($event.target.checked)"
                               class="w-5 h-5 rounded">
                        <span class="text-sm text-gray-700" x-text="`Selected ${selectedCount} of ${items.length}`"></span>
                    </label>
                    <button @click="deleteSelected()"
                            :disabled="selectedCount === 0"
                            class="p-1.5 text-gray-400 hover:text-red-500 disabled:opacity-30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Scrollable items -->
                <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                    <template x-for="item in items" :key="item.id">
                        <div class="flex gap-3 px-4 py-3">
                            <!-- Checkbox -->
                            <div class="flex-shrink-0 pt-1">
                                <input type="checkbox"
                                       :id="`cart-item-${item.id}`"
                                       :checked="item.is_selected"
                                       @change="toggleItem(item, $event.target.checked)"
                                       class="w-5 h-5 rounded">
                            </div>

                            <!-- Image -->
                            <div class="flex-shrink-0">
                                <template x-if="item.image_url">
                                    <img :src="item.image_url" :alt="item.title"
                                         class="w-16 h-16 object-cover rounded-lg border border-gray-100">
                                </template>
                                <template x-if="!item.image_url">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-1">
                                    <p class="text-sm font-medium text-gray-900 leading-tight line-clamp-2" x-text="item.title"></p>
                                    <!-- Remove -->
                                    <button @click="removeItem(item)"
                                            class="flex-shrink-0 p-1 text-gray-400 hover:text-red-500 transition ml-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-xs text-gray-500 mt-0.5" x-text="'Seller: ' + item.seller_name"></p>

                                <!-- Qty + price row -->
                                <div class="flex items-center justify-between mt-2">
                                    <!-- Quantity controls -->
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                        <button @click="changeQty(item, -1)"
                                                :disabled="item.quantity <= 1"
                                                class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-40 transition font-bold">−</button>
                                        <span class="w-8 text-center text-sm font-medium text-gray-800" x-text="item.quantity"></span>
                                        <button @click="changeQty(item, 1)"
                                                :disabled="item.quantity >= 99 || (item.max_qty !== null && item.quantity >= item.max_qty)"
                                                class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-40 transition font-bold">+</button>
                                    </div>

                                    <!-- Prices -->
                                    <div class="text-right">
                                        <template x-if="item.discount_price">
                                            <div>
                                                <span class="text-xs text-gray-400 line-through" x-text="fmt(item.base_price * item.quantity, item.currency)"></span>
                                                <span class="block text-sm font-bold text-red-600" x-text="fmt(item.unit_price * item.quantity, item.currency)"></span>
                                            </div>
                                        </template>
                                        <template x-if="!item.discount_price">
                                            <span class="text-sm font-bold text-gray-900" x-text="fmt(item.unit_price * item.quantity, item.currency)"></span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Delivery fee -->
                                <template x-if="item.has_delivery && item.delivery_cost > 0">
                                    <p class="text-xs text-gray-500 mt-1">+ Delivery: <span class="font-medium" x-text="fmt(item.delivery_cost, item.currency)"></span></p>
                                </template>
                                <template x-if="item.has_delivery && item.delivery_cost === 0">
                                    <p class="text-xs text-green-600 font-medium mt-1">Free delivery</p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer: Total + Checkout -->
                <div class="flex-shrink-0 border-t border-gray-200 bg-green-50 px-4 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-base font-bold text-gray-900" x-text="fmt(total, 'USD')"></span>
                    </div>
                    <a href="{{ route('checkout.index') }}"
                       :class="selectedCount === 0 ? 'pointer-events-none opacity-50' : ''"
                       class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors text-center">
                        Place Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

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
