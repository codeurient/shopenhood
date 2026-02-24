<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.purchases.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Order {{ $purchase->purchase_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Status & Summary --}}
            @php
                $statusColors = [
                    'pending'    => 'bg-yellow-100 text-yellow-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'completed'  => 'bg-green-100 text-green-800',
                    'cancelled'  => 'bg-red-100 text-red-800',
                ];
                $statusColor = $statusColors[$purchase->status] ?? 'bg-gray-100 text-gray-700';
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-mono">{{ $purchase->purchase_number }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $purchase->created_at->format('F d, Y · H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColor }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </div>
            </div>

            {{-- Delivery Address --}}
            @if($purchase->address_snapshot)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Delivery Address</h3>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $purchase->address_snapshot['recipient_name'] ?? '' }}
                </p>
                @if(!empty($purchase->address_snapshot['phone']))
                    <p class="text-sm text-gray-500 mt-0.5">{{ $purchase->address_snapshot['phone'] }}</p>
                @endif
                @if(!empty($purchase->address_snapshot['full_address']))
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $purchase->address_snapshot['full_address'] }}</p>
                @endif
            </div>
            @endif

            {{-- Payment Method --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Payment Method</h3>
                <p class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $purchase->payment_method === 'cash_on_delivery' ? 'Cash on Delivery' : ucfirst($purchase->payment_method) }}
                </p>
            </div>

            {{-- Orders grouped by seller --}}
            @foreach($ordersBySeller as $sellerId => $sellerOrders)
                @php $firstOrder = $sellerOrders->first(); @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Seller: {{ $firstOrder->listing?->user?->name ?? 'Unknown Seller' }}
                        </p>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($sellerOrders as $order)
                            @php
                                $img = $order->listing?->primaryImage ?? $order->listing?->firstImage;
                                $orderStatusColors = [
                                    'pending'    => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped'    => 'bg-indigo-100 text-indigo-700',
                                    'delivered'  => 'bg-teal-100 text-teal-700',
                                    'completed'  => 'bg-green-100 text-green-700',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                ];
                                $orderStatusColor = $orderStatusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <div class="flex items-start gap-3 px-5 py-4">
                                @if($img)
                                    <img src="{{ asset('storage/'.$img->image_path) }}"
                                         alt="{{ $order->listing?->title }}"
                                         class="w-14 h-14 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                @else
                                    <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-lg flex-shrink-0 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-snug">
                                        {{ $order->listing?->title ?? 'Deleted listing' }}
                                    </p>
                                    @if($order->variation)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ collect($order->variation->variant_combination)->values()->implode(' / ') }}
                                        </p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-1">
                                        <p class="text-xs text-gray-500">Qty: {{ $order->quantity }}</p>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $orderStatusColor }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    @if($order->delivery_option_name)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Delivery: {{ $order->delivery_option_name }}
                                            @if($order->shipping_cost > 0)
                                                — {{ number_format($order->shipping_cost, 2) }} {{ $order->currency }}
                                                @if($order->delivery_cost_paid_by === 'buyer')
                                                    <span class="text-orange-500">(pay seller directly)</span>
                                                @endif
                                            @else
                                                <span class="text-green-600">FREE</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($order->subtotal, 2) }} {{ $order->currency }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ number_format($order->unit_price, 2) }} × {{ $order->quantity }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Cost Breakdown --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 space-y-2">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>{{ number_format($purchase->subtotal, 2) }} {{ $purchase->currency }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Delivery</span>
                    @if($purchase->shipping_cost > 0)
                        <span>{{ number_format($purchase->shipping_cost, 2) }} {{ $purchase->currency }}</span>
                    @else
                        <span class="text-green-600 font-medium">FREE</span>
                    @endif
                </div>
                <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-gray-100 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span>Total</span>
                    <span>{{ number_format($purchase->total_amount, 2) }} {{ $purchase->currency }}</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
