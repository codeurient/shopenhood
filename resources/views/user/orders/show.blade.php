<x-guest-layout>
    <x-slot name="title">Order {{ $purchase->purchase_number }} — {{ config('app.name') }}</x-slot>

    @php
        $statusBadge = [
            'pending'    => 'bg-[#37474F] text-[#E0E0E0]',
            'processing' => 'bg-blue-100 text-blue-700',
            'completed'  => 'bg-[#D4AF37]/20 text-[#D4AF37]',
            'cancelled'  => 'bg-[#C0392B]/20 text-[#E74C3C]',
        ];
        $orderStatusBadge = [
            'pending'    => 'bg-[#37474F] text-[#E0E0E0]',
            'processing' => 'bg-blue-100 text-blue-700',
            'shipped'    => 'bg-indigo-100 text-indigo-700',
            'delivered'  => 'bg-teal-100 text-teal-700',
            'completed'  => 'bg-[#D4AF37]/20 text-[#D4AF37]',
            'cancelled'  => 'bg-[#C0392B]/20 text-[#E74C3C]',
        ];
    @endphp

    {{-- Hero Header --}}
    <div class="bg-[#000000] py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <nav class="flex items-center gap-2 text-xs text-[#37474F] mb-4">
                <a href="{{ route('user.purchases.index') }}" class="hover:text-[#D4AF37] transition-colors">My Orders</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-[#E0E0E0]">{{ $purchase->purchase_number }}</span>
            </nav>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="font-bold text-[#FFFFFF] font-['Inter','Segoe_UI',sans-serif]"
                        style="font-size: clamp(1.25rem, 2.5vw, 2rem);">
                        Order Details
                    </h1>
                    <p class="text-[#E0E0E0] text-sm mt-1 font-mono">{{ $purchase->purchase_number }}</p>
                </div>
                <span class="inline-flex items-center h-5 px-3 rounded-[10px] text-[11px] font-semibold {{ $statusBadge[$purchase->status] ?? 'bg-[#37474F] text-[#E0E0E0]' }}">
                    {{ ucfirst($purchase->status) }}
                </span>
            </div>
            <p class="text-[#37474F] text-xs mt-3">{{ $purchase->created_at->format('F d, Y · H:i') }}</p>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-[#FFFFFF] min-h-screen py-10 px-4">
        <div class="max-w-3xl mx-auto flex flex-col gap-5">

            {{-- Delivery Address --}}
            @if($purchase->address_snapshot)
            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-5 py-3 border-b border-[#E0E0E0] flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-[#D4AF37] text-sm"></i>
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] uppercase tracking-wider">Delivery Address</h3>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[13px] font-semibold text-[#1A1A1A]">{{ $purchase->address_snapshot['recipient_name'] ?? '' }}</p>
                    @if(!empty($purchase->address_snapshot['phone']))
                        <p class="text-[13px] text-[#37474F] mt-0.5">{{ $purchase->address_snapshot['phone'] }}</p>
                    @endif
                    @if(!empty($purchase->address_snapshot['full_address']))
                        <p class="text-[13px] text-[#37474F] mt-1">{{ $purchase->address_snapshot['full_address'] }}</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Payment Method --}}
            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-5 py-3 border-b border-[#E0E0E0] flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-[#D4AF37] text-sm"></i>
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] uppercase tracking-wider">Payment Method</h3>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[13px] text-[#1A1A1A]">
                        {{ $purchase->payment_method === 'cash_on_delivery' ? 'Cash on Delivery' : ucfirst($purchase->payment_method) }}
                    </p>
                </div>
            </div>

            {{-- Orders grouped by seller --}}
            @foreach($ordersBySeller as $sellerId => $sellerOrders)
                @php $firstOrder = $sellerOrders->first(); @endphp
                <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-5 py-3 border-b border-[#E0E0E0] bg-[#F9F9F9] flex items-center gap-2">
                        <i class="fa-solid fa-store text-[#D4AF37] text-sm"></i>
                        <p class="text-[13px] font-semibold text-[#1A1A1A]">
                            {{ $firstOrder->listing?->user?->name ?? 'Unknown Seller' }}
                        </p>
                    </div>

                    <div class="divide-y divide-[#E0E0E0]">
                        @foreach($sellerOrders as $order)
                            @php
                                $img = $order->listing?->primaryImage ?? $order->listing?->firstImage;
                            @endphp
                            <div class="flex items-start gap-4 px-5 py-4" x-data="{ cancelOpen: false }">

                                {{-- Image --}}
                                @if($img)
                                    <img src="{{ asset('storage/'.$img->image_path) }}"
                                         alt="{{ $order->listing?->title }}"
                                         class="w-14 h-14 rounded-[6px] object-cover border border-[#E0E0E0] flex-shrink-0">
                                @else
                                    <div class="w-14 h-14 bg-[#F5F5F5] rounded-[6px] flex-shrink-0 flex items-center justify-center border border-[#E0E0E0]">
                                        <i class="fa-solid fa-image text-[#E0E0E0] text-lg"></i>
                                    </div>
                                @endif

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-semibold text-[#1A1A1A] leading-snug">
                                        {{ $order->listing?->title ?? 'Deleted listing' }}
                                    </p>
                                    @if($order->variation)
                                        <p class="text-[12px] text-[#37474F] mt-0.5">
                                            {{ collect($order->variation->variant_combination)->values()->implode(' / ') }}
                                        </p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="text-[12px] text-[#37474F]">Qty: {{ $order->quantity }}</span>
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $orderStatusBadge[$order->status] ?? 'bg-[#37474F] text-[#E0E0E0]' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    @if($order->delivery_option_name)
                                        <p class="text-[12px] text-[#37474F] mt-1">
                                            <i class="fa-solid fa-truck text-xs mr-1"></i>
                                            {{ $order->delivery_option_name }}
                                            @if($order->shipping_cost > 0)
                                                — {{ number_format($order->shipping_cost, 2) }} {{ $order->currency }}
                                                @if($order->delivery_cost_paid_by === 'buyer')
                                                    <span class="text-orange-500">(pay seller directly)</span>
                                                @endif
                                            @else
                                                <span class="text-[#D4AF37] font-semibold">FREE</span>
                                            @endif
                                        </p>
                                    @endif
                                    @if($order->tracking_number)
                                        <p class="text-[12px] text-[#37474F] mt-1">
                                            <i class="fa-solid fa-barcode text-xs mr-1"></i>
                                            Tracking: <span class="font-mono font-semibold text-[#1A1A1A]">{{ $order->tracking_number }}</span>
                                        </p>
                                    @endif
                                    @if($order->isCancelled() && $order->cancellation_reason)
                                        <p class="text-[12px] text-[#E74C3C] mt-1">
                                            Reason: {{ $order->cancellation_reason }}
                                        </p>
                                    @endif
                                    @if($order->canBeCancelled())
                                        <button @click="cancelOpen = true" type="button"
                                                class="mt-2 text-[12px] text-[#C0392B] hover:text-[#E74C3C] underline underline-offset-2 transition">
                                            Cancel this order
                                        </button>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[13px] font-bold text-[#1A1A1A]">
                                        {{ number_format($order->subtotal, 2) }} {{ $order->currency }}
                                    </p>
                                    <p class="text-[12px] text-[#37474F]">
                                        {{ number_format($order->unit_price, 2) }} × {{ $order->quantity }}
                                    </p>
                                </div>

                                {{-- Cancel Modal --}}
                                <template x-teleport="body">
                                    <div x-show="cancelOpen" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                         @click.self="cancelOpen = false">
                                        <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] shadow-xl w-full max-w-sm p-6">
                                            <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-1">Cancel Order</h3>
                                            <p class="text-[13px] text-[#37474F] mb-4">
                                                Cancel <span class="font-mono font-semibold text-[#1A1A1A]">{{ $order->order_number }}</span>?
                                                This cannot be undone.
                                            </p>
                                            <form method="POST" action="{{ route('user.orders.cancel', $order) }}">
                                                @csrf
                                                <textarea name="reason" rows="3"
                                                          placeholder="Reason for cancellation (optional)"
                                                          class="w-full text-[13px] border border-[#E0E0E0] rounded px-3 py-2 text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none resize-none"></textarea>
                                                <div class="flex gap-3 mt-4">
                                                    <button type="submit"
                                                            class="flex-1 h-[34px] text-[13px] font-semibold bg-[#C0392B] hover:bg-[#a93226] text-white rounded transition">
                                                        Yes, Cancel
                                                    </button>
                                                    <button type="button" @click="cancelOpen = false"
                                                            class="flex-1 h-[34px] text-[13px] font-semibold border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-50 transition">
                                                        Keep Order
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Cost Breakdown --}}
            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] px-5 py-4 flex flex-col gap-2">
                <div class="flex justify-between text-[13px] text-[#37474F]">
                    <span>Subtotal</span>
                    <span>{{ number_format($purchase->subtotal, 2) }} {{ $purchase->currency }}</span>
                </div>
                <div class="flex justify-between text-[13px] text-[#37474F]">
                    <span>Delivery</span>
                    @if($purchase->shipping_cost > 0)
                        <span>{{ number_format($purchase->shipping_cost, 2) }} {{ $purchase->currency }}</span>
                    @else
                        <span class="text-[#D4AF37] font-semibold">FREE</span>
                    @endif
                </div>
                <div class="flex justify-between text-[14px] font-bold text-[#1A1A1A] pt-3 border-t border-[#E0E0E0]">
                    <span>Total</span>
                    <span>{{ number_format($purchase->total_amount, 2) }} {{ $purchase->currency }}</span>
                </div>
            </div>

            {{-- Back link --}}
            <div>
                <a href="{{ route('user.purchases.index') }}"
                   class="inline-flex items-center justify-center gap-2 h-[34px] px-[14px] rounded-[4px] border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold hover:bg-[#D4AF37]/10 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to My Orders
                </a>
            </div>

        </div>
    </div>

</x-guest-layout>
