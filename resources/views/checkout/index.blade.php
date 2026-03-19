<x-guest-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8"
             x-data="checkoutPage({{ Illuminate\Support\Js::from($sellers) }}, {{ $defaultAddressId ?? 'null' }}, {{ Illuminate\Support\Js::from($addresses->map(fn($a) => ['id' => $a->id, 'label' => $a->label, 'recipient_name' => $a->recipient_name, 'phone' => $a->phone, 'full_address' => $a->full_address, 'city' => $a->city, 'country' => $a->country, 'is_default' => $a->is_default])->values()) }})">

            {{-- Back link --}}
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-[#37474F] hover:text-[#D4AF37] mb-5 transition-colors">
                <i class="fa-solid fa-chevron-left text-xs"></i>
                Continue shopping
            </a>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 rounded p-4">
                    <p class="text-sm font-medium text-red-700 mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success flash --}}
            @if(session('success'))
                <div class="mb-5 bg-green-50 border border-green-200 rounded p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.confirm') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="address_id" :value="selectedAddressId">
                <input type="hidden" name="payment_method" value="cash_on_delivery">

                {{-- ── 1. DELIVERY ADDRESS ── --}}
                <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                        <h3 class="font-semibold text-[#1A1A1A] text-[13px]">Delivery Address</h3>
                        @if($addresses->count() > 1)
                            <button type="button" @click="showAddressPicker = !showAddressPicker"
                                    class="text-xs text-[#D4AF37] hover:brightness-110 font-medium transition-colors">
                                <span x-text="showAddressPicker ? 'Cancel' : 'Change'"></span>
                            </button>
                        @endif
                    </div>

                    {{-- Selected address display --}}
                    <div x-show="!showAddressPicker" class="px-5 py-4">
                        <template x-if="selectedAddress">
                            <div>
                                <p class="font-medium text-[#1A1A1A] text-[13px]" x-text="selectedAddress.recipient_name"></p>
                                <p class="text-xs text-[#37474F] mt-0.5" x-text="selectedAddress.phone"></p>
                                <p class="text-xs text-[#37474F] mt-1" x-text="selectedAddress.full_address"></p>
                            </div>
                        </template>
                        <template x-if="!selectedAddress">
                            <p class="text-sm text-red-500">No address selected. Please add a delivery address first.</p>
                        </template>
                    </div>

                    {{-- Address picker --}}
                    <div x-show="showAddressPicker" x-cloak class="px-5 pb-4 space-y-2">
                        <template x-for="addr in addresses" :key="addr.id">
                            <button type="button"
                                    @click="selectAddress(addr.id)"
                                    :class="selectedAddressId === addr.id
                                        ? 'border-[#D4AF37] bg-[#D4AF37]/5'
                                        : 'border-[#E0E0E0] bg-white hover:border-[#D4AF37]/50'"
                                    class="w-full text-left border rounded px-4 py-3 transition">
                                <p class="font-medium text-[13px] text-[#1A1A1A]"
                                   x-text="addr.recipient_name + (addr.is_default ? ' (Default)' : '')"></p>
                                <p class="text-xs text-[#37474F] mt-0.5" x-text="addr.full_address"></p>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ── 2. DELIVERY METHOD (auto-computed) ── --}}
                <template x-for="(seller, idx) in sellers" :key="seller.seller_id">
                    <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                        <input type="hidden" :name="'delivery_selections[' + seller.seller_id + ']'" :value="seller.applicableTierKey">
                        <div class="px-5 py-4 border-b border-[#E0E0E0]">
                            <h3 class="font-semibold text-[#1A1A1A] text-[13px]">Delivery Method</h3>
                            <p x-show="sellers.length > 1" class="text-xs text-[#37474F] mt-0.5" x-text="'Seller: ' + seller.seller_name"></p>
                        </div>
                        <div class="px-5 py-4">
                            <template x-if="seller.applicableTier">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[13px] font-medium text-[#1A1A1A]" x-text="seller.applicableTier.name"></p>
                                        <p x-show="seller.applicableTier.days" class="text-xs text-[#37474F] mt-0.5"
                                           x-text="'Estimated ' + seller.applicableTier.days + ' day(s)'"></p>
                                    </div>
                                    <span class="text-[13px] font-semibold"
                                          :class="seller.applicableTier.cost > 0 ? 'text-[#37474F]' : 'text-[#D4AF37]'"
                                          x-text="seller.applicableTier.cost > 0 ? fmt(seller.applicableTier.cost) : 'FREE'"></span>
                                </div>
                            </template>
                            <template x-if="!seller.applicableTier">
                                <p class="text-sm text-[#37474F] bg-[#E0E0E0]/30 rounded px-3 py-2">
                                    No delivery option available for your address. Contact the seller directly.
                                </p>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- ── 3. PAYMENT METHOD ── --}}
                <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <div class="px-5 py-4 border-b border-[#E0E0E0]">
                        <h3 class="font-semibold text-[#1A1A1A] text-[13px]">Payment Method</h3>
                    </div>
                    <div class="px-5 py-4">
                        <label class="flex items-center gap-3">
                            <input type="radio" name="payment_method_display" checked disabled
                                   class="text-[#D4AF37] focus:ring-0">
                            <span class="text-[13px] font-medium text-[#1A1A1A]">Cash on Delivery</span>
                        </label>
                        <p class="text-xs text-[#37474F] mt-2 ml-6">
                            Payment is arranged directly with each seller upon delivery or pickup.
                        </p>
                    </div>
                </div>

                {{-- ── 4. ORDER SUMMARY ── --}}
                <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <div class="px-5 py-4 border-b border-[#E0E0E0]">
                        <h3 class="font-semibold text-[#1A1A1A] text-[13px]">
                            Order Summary
                            <span class="text-[#37474F] font-normal text-xs ml-1">({{ $itemCount }} {{ Str::plural('item', $itemCount) }})</span>
                        </h3>
                    </div>
                    <div class="divide-y divide-[#E0E0E0]">
                        @foreach($sellers as $seller)
                            @if(count($sellers) > 1)
                                <p class="px-5 pt-3 pb-1 text-xs font-semibold text-[#37474F] uppercase tracking-wide">
                                    {{ $seller['seller_name'] }}
                                </p>
                            @endif
                            @foreach($seller['items'] as $item)
                                <div class="flex items-center gap-3 px-5 py-3">
                                    @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt=""
                                             class="w-14 h-14 rounded object-cover flex-shrink-0 bg-[#E0E0E0]">
                                    @else
                                        <div class="w-14 h-14 rounded bg-[#E0E0E0] flex-shrink-0 flex items-center justify-center">
                                            <i class="fa-regular fa-image text-[#37474F] text-xl"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-medium text-[#1A1A1A] truncate">{{ $item['title'] }}</p>
                                        <p class="text-xs text-[#37474F] mt-0.5">
                                            Qty: {{ $item['quantity'] }} ×
                                            {{ Number::currency($item['unit_price'], $item['currency']) }}
                                        </p>
                                    </div>
                                    <span class="text-[13px] font-semibold text-[#1A1A1A] flex-shrink-0">
                                        {{ Number::currency($item['line_total'], $item['currency']) }}
                                    </span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                {{-- ── 5. NOTES ── --}}
                <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <div class="px-5 py-4 border-b border-[#E0E0E0]">
                        <h3 class="font-semibold text-[#1A1A1A] text-[13px]">Notes <span class="text-[#37474F] font-normal text-xs">(optional)</span></h3>
                    </div>
                    <div class="px-5 py-4">
                        <textarea name="notes" rows="3"
                                  placeholder="Special instructions or notes for the seller..."
                                  class="w-full text-[13px] border border-[#E0E0E0] rounded px-3 py-2 bg-white text-[#1A1A1A] placeholder-[#37474F] focus:outline-none focus:ring-0 focus:border-[#D4AF37] resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- ── 6. COST BREAKDOWN ── --}}
                <div class="bg-white rounded border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] px-5 py-4 space-y-2">
                    <div class="flex justify-between text-[13px] text-[#37474F]">
                        <span>Subtotal</span>
                        <span>{{ Number::currency($subtotal, $currency) }}</span>
                    </div>
                    @if($couponTotal > 0)
                        <div class="flex justify-between text-[13px] text-[#D4AF37]">
                            <span>Coupon savings</span>
                            <span>- {{ Number::currency($couponTotal, $currency) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-[13px] text-[#37474F]">
                        <span>Delivery</span>
                        <span x-text="shipping > 0 ? fmt(shipping) : 'FREE'"
                              :class="shipping > 0 ? 'text-[#37474F]' : 'text-[#D4AF37]'"></span>
                    </div>
                    <div class="border-t border-[#E0E0E0] pt-2 flex justify-between font-bold text-[#1A1A1A]">
                        <span>Total</span>
                        <span x-text="fmt(total - {{ $couponTotal }})"></span>
                    </div>
                </div>

                {{-- ── 7. LEGAL NOTE ── --}}
                <p class="text-xs text-center text-[#37474F]">
                    By confirming your order you agree to our
                    <a href="#" class="underline hover:text-[#D4AF37] transition-colors">Terms of Service</a>.
                </p>

                {{-- ── 8. SUBMIT ── --}}
                <button type="submit"
                        class="w-full h-[48px] bg-[#D4AF37] hover:brightness-110 text-[#000000] font-semibold rounded text-[15px] transition-colors">
                    Confirm Order ({{ $itemCount }})
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function checkoutPage(sellers, defaultAddressId, addresses) {
        return {
            sellers: sellers.map(s => ({ ...s, applicableTier: null, applicableTierKey: 'pickup' })),
            selectedAddressId: defaultAddressId,
            showAddressPicker: false,
            addresses: addresses,

            init() {
                this.recalcDelivery();
            },

            get selectedAddress() {
                return this.addresses.find(a => a.id === this.selectedAddressId) ?? null;
            },

            selectAddress(id) {
                this.selectedAddressId = id;
                this.showAddressPicker = false;
                this.recalcDelivery();
            },

            recalcDelivery() {
                const addr = this.selectedAddress;
                this.sellers.forEach(seller => {
                    const tierKey = addr ? this.calcDeliveryTier(addr, seller) : null;
                    const tier = tierKey ? (seller.delivery_tiers.find(t => t.key === tierKey) ?? null) : null;
                    seller.applicableTier = tier;
                    seller.applicableTierKey = tier ? tierKey : 'pickup';
                });
            },

            calcDeliveryTier(address, seller) {
                const buyerCity = (address.city || '').trim().toLowerCase();
                const buyerCountry = (address.country || '').trim().toLowerCase();
                const sellerCity = (seller.seller_city || '').trim().toLowerCase();
                const sellerCountry = (seller.seller_country || '').trim().toLowerCase();

                if (buyerCity && sellerCity && buyerCity === sellerCity) {
                    return 'same_city';
                }
                if (buyerCountry && sellerCountry && buyerCountry === sellerCountry) {
                    return 'domestic';
                }
                return 'international';
            },

            get shipping() {
                return this.sellers.reduce((sum, s) => sum + (s.applicableTier?.cost ?? 0), 0);
            },

            get total() {
                return {{ $subtotal }} + this.shipping;
            },

            fmt(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: '{{ $currency }}',
                    minimumFractionDigits: 2,
                }).format(amount);
            },
        };
    }
    </script>
    @endpush
</x-guest-layout>
