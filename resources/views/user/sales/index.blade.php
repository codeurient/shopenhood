<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Sales</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash message --}}
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-sm text-green-700 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter Tabs --}}
            <div class="flex gap-1 mb-5 bg-gray-100 dark:bg-gray-700 rounded-xl p-1 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                @foreach([
                    'all'        => ['label' => 'All',        'count' => $counts['all']],
                    'pending'    => ['label' => 'Pending',    'count' => $counts['pending']],
                    'processing' => ['label' => 'Processing', 'count' => $counts['processing']],
                    'shipped'    => ['label' => 'Shipped',    'count' => $counts['shipped']],
                    'delivered'  => ['label' => 'Delivered',  'count' => $counts['delivered']],
                    'completed'  => ['label' => 'Completed',  'count' => $counts['completed']],
                    'cancelled'  => ['label' => 'Cancelled',  'count' => $counts['cancelled']],
                ] as $key => $tab)
                    <a href="{{ route('user.sales.index', ['status' => $key]) }}"
                       class="flex-shrink-0 whitespace-nowrap text-center text-sm font-medium py-2 px-4 rounded-lg transition
                              {{ $status === $key
                                    ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm'
                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        {{ $tab['label'] }}
                        @if($tab['count'] > 0)
                            <span class="ml-1 text-xs font-bold {{ $status === $key ? 'text-primary-600' : 'text-gray-400' }}">
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            @if($orders->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No orders yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Orders for your listings will appear here.</p>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Buyer</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Delivery</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($orders as $order)
                                @php
                                    $img = $order->listing?->primaryImage ?? $order->listing?->firstImage;
                                    $statusColors = [
                                        'pending'    => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'shipped'    => 'bg-indigo-100 text-indigo-700',
                                        'delivered'  => 'bg-teal-100 text-teal-700',
                                        'completed'  => 'bg-green-100 text-green-700',
                                        'cancelled'  => 'bg-red-100 text-red-700',
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                    x-data="{ shipOpen: false, cantShipOpen: false }">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($img)
                                                <img src="{{ asset('storage/'.$img->image_path) }}"
                                                     alt="{{ $order->listing?->title }}"
                                                     class="w-12 h-12 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex-shrink-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-gray-100 line-clamp-1">
                                                    {{ $order->listing?->title ?? 'Deleted listing' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    Qty: {{ $order->quantity }}
                                                    @if($order->variation)
                                                        · {{ collect($order->variation->variant_combination)->values()->implode(' / ') }}
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $order->order_number }}</p>
                                                @if($order->tracking_number)
                                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">
                                                        Tracking: {{ $order->tracking_number }}
                                                    </p>
                                                @endif
                                                @if($order->isCancelled() && $order->cancellation_reason)
                                                    <p class="text-xs text-red-500 mt-0.5">
                                                        Reason: {{ $order->cancellation_reason }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 hidden sm:table-cell">
                                        <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $order->buyer?->name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <p class="text-gray-700 dark:text-gray-300 text-xs">
                                            {{ $order->delivery_option_name ?? '—' }}
                                        </p>
                                        @if($order->shipping_cost > 0)
                                            <p class="text-xs mt-0.5 {{ $order->delivery_cost_paid_by === 'buyer' ? 'text-orange-500' : 'text-gray-400' }}">
                                                {{ number_format($order->shipping_cost, 2) }} {{ $order->currency }}
                                                @if($order->delivery_cost_paid_by === 'buyer')
                                                    (buyer pays)
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-xs text-green-600 mt-0.5">FREE</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <p class="font-bold text-gray-900 dark:text-gray-100">
                                            {{ number_format($order->total_amount, 2) }} {{ $order->currency }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>

                                    {{-- Actions column --}}
                                    <td class="px-4 py-4 text-center">
                                        @if($order->canBeShipped())
                                            <div class="flex flex-col items-center gap-1.5">
                                                <button @click="shipOpen = true" type="button"
                                                        class="w-full px-3 py-1.5 text-xs font-semibold bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition">
                                                    Ship
                                                </button>
                                                <button @click="cantShipOpen = true" type="button"
                                                        class="w-full px-3 py-1.5 text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition">
                                                    Can't Ship
                                                </button>
                                            </div>
                                        @elseif($order->canBeMarkedDelivered())
                                            <form method="POST" action="{{ route('user.sales.deliver', $order) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full px-3 py-1.5 text-xs font-semibold bg-teal-500 hover:bg-teal-600 text-white rounded-lg transition">
                                                    Delivered
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif

                                        {{-- Ship Modal --}}
                                        <template x-teleport="body">
                                            <div x-show="shipOpen" x-cloak
                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                 @click.self="shipOpen = false">
                                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm p-6">
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Mark as Shipped</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                                        Order <span class="font-mono font-semibold">{{ $order->order_number }}</span>
                                                    </p>
                                                    <form method="POST" action="{{ route('user.sales.ship', $order) }}">
                                                        @csrf
                                                        <input type="text" name="tracking_number"
                                                               placeholder="Tracking number (optional)"
                                                               class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-400">
                                                        <div class="flex gap-3 mt-4">
                                                            <button type="submit"
                                                                    class="flex-1 py-2 text-sm font-semibold bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition">
                                                                Confirm Shipped
                                                            </button>
                                                            <button type="button" @click="shipOpen = false"
                                                                    class="flex-1 py-2 text-sm font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Cannot Ship Modal --}}
                                        <template x-teleport="body">
                                            <div x-show="cantShipOpen" x-cloak
                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                 @click.self="cantShipOpen = false">
                                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm p-6">
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Cannot Ship</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                                        The buyer will be notified of the reason.
                                                    </p>
                                                    <form method="POST" action="{{ route('user.sales.cannot-ship', $order) }}">
                                                        @csrf
                                                        <textarea name="reason" rows="3" required
                                                                  placeholder="Explain why this order cannot be shipped..."
                                                                  class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-red-400 resize-none"></textarea>
                                                        <div class="flex gap-3 mt-4">
                                                            <button type="submit"
                                                                    class="flex-1 py-2 text-sm font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                                                                Confirm & Notify Buyer
                                                            </button>
                                                            <button type="button" @click="cantShipOpen = false"
                                                                    class="flex-1 py-2 text-sm font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>
</x-guest-layout>
