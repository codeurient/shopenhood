<x-guest-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8"
             x-data="checkoutPage({{ Illuminate\Support\Js::from($sellers) }}, {{ $defaultAddressId ?? 'null' }}, {{ Illuminate\Support\Js::from($addresses->map(fn($a) => ['id' => $a->id, 'label' => $a->label, 'recipient_name' => $a->recipient_name, 'phone' => $a->phone, 'full_address' => $a->full_address, 'is_default' => $a->is_default])->values()) }})">

            {{-- Back link --}}
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Continue shopping
            </a>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
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
                <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.confirm') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="address_id" :value="selectedAddressId">
                <input type="hidden" name="payment_method" value="cash_on_delivery">

                {{-- ── 1. DELIVERY ADDRESS ── --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Delivery Address</h3>
                        @if($addresses->count() > 1)
                            <button type="button" @click="showAddressPicker = !showAddressPicker"
                                    class="text-sm text-green-600 hover:text-green-700 font-medium">
                                <span x-text="showAddressPicker ? 'Cancel' : 'Change'"></span>
                            </button>
                        @endif
                    </div>

                    {{-- Selected address display --}}
                    <div x-show="!showAddressPicker" class="px-5 py-4">
                        <template x-if="selectedAddress">
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedAddress.recipient_name"></p>
                                <p class="text-sm text-gray-500 mt-0.5" x-text="selectedAddress.phone"></p>
                                <p class="text-sm text-gray-600 mt-1" x-text="selectedAddress.full_address"></p>
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
                                    @click="selectedAddressId = addr.id; showAddressPicker = false"
                                    :class="selectedAddressId === addr.id
                                        ? 'border-green-500 bg-green-50'
                                        : 'border-gray-200 bg-white hover:border-gray-300'"
                                    class="w-full text-left border rounded-lg px-4 py-3 transition">
                                <p class="font-medium text-sm text-gray-900"
                                   x-text="addr.recipient_name + (addr.is_default ? ' (Default)' : '')"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="addr.full_address"></p>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ── 2. DELIVERY METHOD ── --}}
                @foreach($sellers as $idx => $seller)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900">Delivery Method</h3>
                            @if(count($sellers) > 1)
                                <p class="text-xs text-gray-500 mt-0.5">Seller: {{ $seller['seller_name'] }}</p>
                            @endif
                        </div>
                        <div class="px-5 py-4 space-y-3">
                            @foreach($seller['delivery_options'] as $option)
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="radio"
                                           name="delivery_selections[{{ $seller['seller_id'] }}]"
                                           value="{{ $option['key'] }}"
                                           {{ $option['key'] === $seller['selected_delivery'] ? 'checked' : '' }}
                                           @change="sellers[{{ $idx }}].selected_delivery = '{{ $option['key'] }}'"
                                           class="mt-0.5 text-green-600 focus:ring-green-500">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">{{ $option['name'] }}</span>
                                            @if($option['cost'] > 0)
                                                <span class="text-sm font-semibold text-orange-600">
                                                    {{ Number::currency($option['cost'], $seller['items'][0]['currency'] ?? 'USD') }}
                                                </span>
                                            @else
                                                <span class="text-sm font-semibold text-green-600">FREE</span>
                                            @endif
                                        </div>
                                        @if($option['note'])
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $option['note'] }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- ── 3. PAYMENT METHOD ── --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Payment Method</h3>
                    </div>
                    <div class="px-5 py-4">
                        <label class="flex items-center gap-3">
                            <input type="radio" name="payment_method_display" checked disabled
                                   class="text-green-600 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-900">Cash on Delivery</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-2 ml-6">
                            Payment is arranged directly with each seller upon delivery or pickup.
                        </p>
                    </div>
                </div>

                {{-- ── 4. ORDER SUMMARY ── --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">
                            Order Summary
                            <span class="text-gray-400 font-normal text-sm ml-1">({{ $itemCount }} {{ Str::plural('item', $itemCount) }})</span>
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($sellers as $seller)
                            @if(count($sellers) > 1)
                                <p class="px-5 pt-3 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ $seller['seller_name'] }}
                                </p>
                            @endif
                            @foreach($seller['items'] as $item)
                                <div class="flex items-center gap-3 px-5 py-3">
                                    @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt=""
                                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0 bg-gray-100">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['title'] }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Qty: {{ $item['quantity'] }} ×
                                            {{ Number::currency($item['unit_price'], $item['currency']) }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 flex-shrink-0">
                                        {{ Number::currency($item['line_total'], $item['currency']) }}
                                    </span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                {{-- ── 5. NOTES ── --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Notes <span class="text-gray-400 font-normal text-sm">(optional)</span></h3>
                    </div>
                    <div class="px-5 py-4">
                        <textarea name="notes" rows="3"
                                  placeholder="Special instructions or notes for the seller..."
                                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- ── 6. COST BREAKDOWN ── --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ Number::currency($subtotal, $currency) }}</span>
                    </div>
                    @if($couponTotal > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Coupon savings</span>
                            <span>- {{ Number::currency($couponTotal, $currency) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Delivery</span>
                        <span x-text="shipping > 0 ? fmt(shipping) : 'FREE'"
                              :class="shipping > 0 ? 'text-orange-600' : 'text-green-600'"></span>
                    </div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span x-text="fmt(total - {{ $couponTotal }})"></span>
                    </div>
                </div>

                {{-- ── 7. LEGAL NOTE ── --}}
                <p class="text-xs text-center text-gray-400">
                    By confirming your order you agree to our
                    <a href="#" class="underline hover:text-gray-600">Terms of Service</a>.
                </p>

                {{-- ── 8. SUBMIT ── --}}
                <button type="submit"
                        class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-base transition-colors shadow-sm">
                    Confirm Order ({{ $itemCount }})
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function checkoutPage(sellers, defaultAddressId, addresses) {
        return {
            sellers: sellers.map(s => ({ ...s })),
            selectedAddressId: defaultAddressId,
            showAddressPicker: false,
            addresses: addresses,

            get selectedAddress() {
                return this.addresses.find(a => a.id === this.selectedAddressId) ?? null;
            },

            get shipping() {
                return this.sellers.reduce((sum, s) => {
                    const opt = s.delivery_options.find(o => o.key === s.selected_delivery);
                    return sum + (opt?.cost ?? 0);
                }, 0);
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
