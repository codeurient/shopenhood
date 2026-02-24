@auth
{{-- ============================================================ --}}
{{-- CHECKOUT MODAL                                               --}}
{{-- Triggered by: window.dispatchEvent(new CustomEvent('open-checkout')) --}}
{{-- ============================================================ --}}
<div x-data="checkoutModal()"
     @open-checkout.window="openCheckout()">

    {{-- Overlay --}}
    <div x-show="open"
         class="fixed inset-0 z-[60]"
         @keydown.escape.window="close()">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" @click="close()"></div>

        {{-- Sheet (slides up from bottom on mobile) --}}
        <div class="fixed inset-x-0 bottom-0 bg-white rounded-t-2xl max-h-[92vh] flex flex-col shadow-2xl"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-base font-bold text-gray-900"
                    x-text="'Placing an order (' + totalCount + ')'"></h2>
                <button @click="close()" class="p-1.5 text-gray-400 hover:text-gray-600 transition rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body (scrollable) --}}
            <div class="flex-1 overflow-y-auto">

                {{-- Loading --}}
                <div x-show="loading" class="flex items-center justify-center py-16">
                    <svg class="animate-spin w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>

                <div x-show="!loading">

                    {{-- ── DELIVERY ADDRESS ───────────────────────────────────── --}}
                    <div class="px-4 py-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Delivery Address</p>

                        <template x-if="selectedAddress">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900" x-text="selectedAddress.recipient_name"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="selectedAddress.phone"></p>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed" x-text="selectedAddress.full_address"></p>
                                </div>
                                <button @click="showAddressPicker = true"
                                        class="flex-shrink-0 text-xs text-primary-600 font-semibold hover:text-primary-700 transition flex items-center gap-0.5 mt-0.5">
                                    Change
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <template x-if="!selectedAddress && addresses.length === 0">
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">No saved addresses.</p>
                                <a href="{{ route('user.addresses.create') }}"
                                   class="text-sm text-primary-600 font-semibold hover:underline mt-1 inline-block">
                                    Add an address →
                                </a>
                            </div>
                        </template>
                    </div>

                    {{-- ── DELIVERY METHOD (per seller) ───────────────────────── --}}
                    <div class="border-b border-gray-100">
                        <div class="px-4 pt-4 pb-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Delivery Method</p>
                        </div>

                        <template x-for="(seller, sellerIdx) in sellers" :key="seller.seller_id">
                            <div class="px-4 py-3">
                                {{-- Seller name header (only shown when >1 seller) --}}
                                <template x-if="sellers.length > 1">
                                    <p class="text-xs font-semibold text-gray-700 mb-2"
                                       x-text="'From: ' + seller.seller_name"></p>
                                </template>

                                <div class="space-y-2">
                                    <template x-for="option in seller.delivery_options" :key="option.key">
                                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition"
                                               :class="seller.selectedDelivery === option.key
                                                   ? 'border-primary-500 bg-primary-50'
                                                   : 'border-gray-200 bg-white hover:border-gray-300'">
                                            <input type="radio"
                                                   :name="'delivery_' + seller.seller_id"
                                                   :value="option.key"
                                                   :checked="seller.selectedDelivery === option.key"
                                                   @change="selectDelivery(sellerIdx, option)"
                                                   class="mt-0.5 text-primary-600 flex-shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-sm font-medium text-gray-900" x-text="option.name"></span>
                                                    <span class="text-sm font-semibold flex-shrink-0"
                                                          :class="option.cost > 0 ? 'text-orange-600' : 'text-green-600'"
                                                          x-text="option.cost > 0 ? fmt(option.cost, totals.currency) : 'FREE'"></span>
                                                </div>
                                                <template x-if="option.note">
                                                    <p class="text-xs text-orange-500 mt-0.5" x-text="option.note"></p>
                                                </template>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ── PAYMENT METHOD ─────────────────────────────────────── --}}
                    <div class="px-4 py-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Method</p>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-primary-500 bg-primary-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked
                                   class="text-primary-600">
                            <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Payment is arranged directly with each seller.
                        </p>
                    </div>

                    {{-- ── ORDER SUMMARY ──────────────────────────────────────── --}}
                    <div class="px-4 py-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Order Summary</p>

                        <template x-for="seller in sellers" :key="seller.seller_id">
                            <div class="mb-4 last:mb-0">
                                <template x-if="sellers.length > 1">
                                    <p class="text-xs text-gray-500 font-medium mb-2" x-text="seller.seller_name"></p>
                                </template>
                                <div class="space-y-2">
                                    <template x-for="item in seller.items" :key="item.id">
                                        <div class="flex items-center gap-3">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.title"
                                                     class="w-12 h-12 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900 leading-snug line-clamp-2" x-text="item.title"></p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Qty: <span x-text="item.quantity"></span>
                                                </p>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900 flex-shrink-0"
                                                  x-text="fmt(item.line_total, item.currency)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ── COST BREAKDOWN ─────────────────────────────────────── --}}
                    <div class="px-4 py-4 border-b border-gray-100 space-y-2">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Total cost</span>
                            <span x-text="fmt(totals.subtotal, totals.currency)"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Delivery</span>
                            <span :class="totals.shipping > 0 ? 'text-gray-900' : 'text-green-600 font-medium'"
                                  x-text="totals.shipping > 0 ? fmt(totals.shipping, totals.currency) : 'FREE'"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm font-bold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total order amount</span>
                            <span x-text="fmt(totals.total, totals.currency)"></span>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="px-4 py-4 border-b border-gray-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            Notes (optional)
                        </label>
                        <textarea x-model="notes"
                                  rows="2"
                                  placeholder="Add a note for sellers..."
                                  class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none"></textarea>
                    </div>

                    {{-- Error --}}
                    <div x-show="error" x-cloak class="px-4 py-3">
                        <p class="text-sm text-red-600 flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="error"></span>
                        </p>
                    </div>

                </div>{{-- /!loading --}}
            </div>{{-- /scrollable body --}}

            {{-- Footer --}}
            <div x-show="!loading" class="flex-shrink-0 px-4 py-4 border-t border-gray-200 bg-white">
                <p class="text-xs text-gray-400 text-center mb-3">
                    By confirming your order you agree to our
                    <a href="#" class="underline">Terms of Service</a>
                    and <a href="#" class="underline">Privacy Policy</a>.
                </p>
                <button @click="confirmOrder()"
                        :disabled="submitting || !selectedAddressId || !allDeliverySelected"
                        class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                    <template x-if="submitting">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="submitting ? 'Placing order...' : 'Confirm Order (' + totalCount + ')'"></span>
                </button>
            </div>

        </div>{{-- /sheet --}}
    </div>{{-- /overlay --}}

    {{-- ── ADDRESS PICKER OVERLAY ─────────────────────────────────────── --}}
    <div x-show="showAddressPicker"
         x-cloak
         style="display:none;"
         class="fixed inset-0 z-[70]">
        <div class="absolute inset-0 bg-black/50" @click="showAddressPicker = false"></div>
        <div class="fixed inset-x-0 bottom-0 bg-white rounded-t-2xl max-h-[70vh] flex flex-col shadow-2xl"
             @click.stop>
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-base font-bold text-gray-900">Select Address</h3>
                <button @click="showAddressPicker = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                <template x-for="addr in addresses" :key="addr.id">
                    <button @click="selectAddress(addr.id)"
                            class="w-full text-left px-4 py-4 hover:bg-gray-50 transition flex items-start gap-3">
                        <div class="w-4 h-4 rounded-full border-2 mt-0.5 flex-shrink-0 transition"
                             :class="selectedAddressId === addr.id
                                 ? 'border-primary-500 bg-primary-500'
                                 : 'border-gray-300'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900" x-text="addr.recipient_name"></span>
                                <template x-if="addr.is_default">
                                    <span class="px-1.5 py-0.5 text-xs bg-primary-100 text-primary-700 rounded font-medium">Default</span>
                                </template>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="addr.phone"></p>
                            <p class="text-xs text-gray-600 mt-0.5 leading-relaxed" x-text="addr.full_address"></p>
                        </div>
                    </button>
                </template>
                <a href="{{ route('user.addresses.create') }}"
                   class="flex items-center gap-3 px-4 py-4 text-primary-600 hover:bg-primary-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-sm font-semibold">Add new address</span>
                </a>
            </div>
        </div>
    </div>

</div>{{-- /x-data --}}

<script>
function checkoutModal() {
    return {
        open: false,
        loading: false,
        submitting: false,
        error: '',
        addresses: [],
        selectedAddressId: null,
        showAddressPicker: false,
        sellers: [],
        notes: '',
        totals: { subtotal: 0, shipping: 0, total: 0, count: 0, currency: 'USD' },

        get selectedAddress() {
            return this.addresses.find(a => a.id === this.selectedAddressId) || null;
        },

        get totalCount() {
            return this.sellers.reduce((sum, s) => sum + s.items.reduce((s2, i) => s2 + i.quantity, 0), 0);
        },

        get allDeliverySelected() {
            return this.sellers.every(s => s.selectedDelivery !== null);
        },

        openCheckout() {
            this.open = true;
            this.loading = true;
            this.error = '';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            fetch('{{ route('checkout.prepare') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
            })
                .then(r => r.json().then(data => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) {
                        this.error = data.message || 'Could not load checkout.';
                        this.loading = false;
                        return;
                    }

                    this.addresses = data.addresses;
                    this.selectedAddressId = data.default_address_id;
                    this.sellers = data.sellers.map(s => ({
                        ...s,
                        selectedDelivery: s.delivery_options[0]?.key ?? null,
                        selectedDeliveryCost: s.delivery_options[0]?.cost ?? 0,
                    }));
                    this.totals.subtotal = data.totals.subtotal;
                    this.totals.count = data.totals.item_count;
                    this.totals.currency = data.totals.currency;
                    this.recalcTotals();
                    this.loading = false;
                })
                .catch(() => {
                    this.error = 'Something went wrong. Please try again.';
                    this.loading = false;
                });
        },

        close() {
            if (this.submitting) { return; }
            this.open = false;
        },

        selectAddress(id) {
            this.selectedAddressId = id;
            this.showAddressPicker = false;
        },

        selectDelivery(sellerIdx, option) {
            this.sellers[sellerIdx].selectedDelivery = option.key;
            this.sellers[sellerIdx].selectedDeliveryCost = option.cost;
            this.recalcTotals();
        },

        recalcTotals() {
            const shipping = this.sellers.reduce((sum, s) => sum + (s.selectedDeliveryCost || 0), 0);
            this.totals.shipping = Math.round(shipping * 100) / 100;
            this.totals.total = Math.round((this.totals.subtotal + this.totals.shipping) * 100) / 100;
        },

        confirmOrder() {
            if (this.submitting || !this.selectedAddressId || !this.allDeliverySelected) { return; }

            this.submitting = true;
            this.error = '';

            const deliverySelections = {};
            this.sellers.forEach(s => {
                deliverySelections[s.seller_id] = s.selectedDelivery;
            });

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            fetch('{{ route('checkout.confirm') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    address_id: this.selectedAddressId,
                    payment_method: 'cash_on_delivery',
                    notes: this.notes,
                    delivery_selections: deliverySelections,
                }),
            })
                .then(r => r.json().then(data => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    if (ok && data.success) {
                        window.dispatchEvent(new CustomEvent('cart-updated'));
                        window.location.href = data.redirect_url;
                    } else {
                        this.error = data.message || 'Failed to place order. Please try again.';
                        this.submitting = false;
                    }
                })
                .catch(() => {
                    this.error = 'Something went wrong. Please try again.';
                    this.submitting = false;
                });
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
@endauth
