<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">My Sales</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Orders placed for your listings</p>
        </div>
    </div>

    {{-- ── Flash Message ────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-check flex-shrink-0 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Filter Tabs ──────────────────────────────────────────────────── --}}
    <div class="flex gap-1 mb-5 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
        @foreach([
            'all'        => ['label' => 'All',        'icon' => 'fa-list'],
            'pending'    => ['label' => 'Pending',    'icon' => 'fa-clock'],
            'processing' => ['label' => 'Processing', 'icon' => 'fa-rotate'],
            'shipped'    => ['label' => 'Shipped',    'icon' => 'fa-truck'],
            'delivered'  => ['label' => 'Delivered',  'icon' => 'fa-box-open'],
            'completed'  => ['label' => 'Completed',  'icon' => 'fa-circle-check'],
            'cancelled'  => ['label' => 'Cancelled',  'icon' => 'fa-circle-xmark'],
        ] as $key => $tab)
        <a href="{{ route('user.sales.index', ['status' => $key]) }}"
           class="flex-shrink-0 inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded text-[12px] font-semibold transition
                  {{ $status === $key
                      ? 'bg-[#1A1A1A] text-[#D4AF37] border border-[#1A1A1A]'
                      : 'bg-white border border-[#E0E0E0] text-[#37474F] hover:border-[#D4AF37]/50 hover:text-[#1A1A1A]' }}">
            <i class="fa-solid {{ $tab['icon'] }} text-[10px]"></i>
            {{ $tab['label'] }}
            @if($counts[$key] > 0)
                <span class="ml-0.5 text-[10px] font-bold {{ $status === $key ? 'text-[#D4AF37]' : 'text-[#37474F]' }}">
                    {{ $counts[$key] }}
                </span>
            @endif
        </a>
        @endforeach
    </div>

    @if($orders->isEmpty())
    {{-- ── Empty State ──────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm py-16 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#F5F5F5] flex items-center justify-center">
            <i class="fa-solid fa-store text-[#37474F] text-xl"></i>
        </div>
        <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No orders yet</p>
        <p class="text-[13px] text-[#37474F]">Orders for your listings will appear here.</p>
    </div>

    @else
    {{-- ── Orders Table ─────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-[#37474F]">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Product</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider hidden sm:table-cell">Buyer</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider hidden md:table-cell">Delivery</th>
                    <th class="text-right px-4 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Amount</th>
                    <th class="text-center px-4 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                    <th class="text-center px-4 py-3 text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E0E0E0]">
                @foreach($orders as $i => $order)
                @php
                    $img = $order->listing?->primaryImage ?? $order->listing?->firstImage;
                    $badge = match($order->status) {
                        'pending'    => 'bg-yellow-100 text-yellow-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'shipped'    => 'bg-indigo-100 text-indigo-700',
                        'delivered'  => 'bg-teal-100 text-teal-700',
                        'completed'  => 'bg-green-100 text-green-700',
                        'cancelled'  => 'bg-red-100 text-red-600',
                        default      => 'bg-[#E0E0E0] text-[#37474F]',
                    };
                    $statusIcon = match($order->status) {
                        'pending'    => 'fa-clock',
                        'processing' => 'fa-rotate',
                        'shipped'    => 'fa-truck',
                        'delivered'  => 'fa-box-open',
                        'completed'  => 'fa-circle-check',
                        'cancelled'  => 'fa-circle-xmark',
                        default      => 'fa-circle',
                    };
                @endphp
                <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-[#FAFAFA]' }} hover:bg-[#D4AF37]/5 transition"
                    x-data="{ shipOpen: false, cantShipOpen: false }">

                    {{-- Product --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            @if($img)
                                <img src="{{ asset('storage/'.$img->image_path) }}"
                                     alt="{{ $order->listing?->title }}"
                                     class="w-10 h-10 rounded object-cover border border-[#E0E0E0] flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-image text-[#37474F] text-xs"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-[#1A1A1A] line-clamp-1">
                                    {{ $order->listing?->title ?? 'Deleted listing' }}
                                </p>
                                <p class="text-[11px] text-[#37474F] mt-0.5">
                                    Qty: {{ $order->quantity }}
                                    @if($order->variation)
                                        · {{ collect($order->variation->variant_combination)->values()->implode(' / ') }}
                                    @endif
                                </p>
                                <p class="text-[11px] font-mono text-[#37474F]/70 mt-0.5">{{ $order->order_number }}</p>
                                @if($order->tracking_number)
                                    <p class="text-[11px] text-[#D4AF37] mt-0.5">
                                        <i class="fa-solid fa-location-crosshairs text-[10px] mr-0.5"></i>
                                        {{ $order->tracking_number }}
                                    </p>
                                @endif
                                @if($order->isCancelled() && $order->cancellation_reason)
                                    <p class="text-[11px] text-red-500 mt-0.5">
                                        Reason: {{ $order->cancellation_reason }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Buyer --}}
                    <td class="px-4 py-3.5 hidden sm:table-cell">
                        <p class="text-[13px] font-semibold text-[#1A1A1A]">{{ $order->buyer?->name ?? '—' }}</p>
                        <p class="text-[11px] text-[#37474F] mt-0.5">
                            <i class="fa-regular fa-calendar text-[10px] mr-0.5"></i>
                            {{ $order->created_at->format('M d, Y') }}
                        </p>
                    </td>

                    {{-- Delivery --}}
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-[13px] text-[#1A1A1A]">{{ $order->delivery_option_name ?? '—' }}</p>
                        @if($order->shipping_cost > 0)
                            <p class="text-[11px] mt-0.5 {{ $order->delivery_cost_paid_by === 'buyer' ? 'text-orange-500' : 'text-[#37474F]' }}">
                                {{ number_format($order->shipping_cost, 2) }} {{ $order->currency }}
                                @if($order->delivery_cost_paid_by === 'buyer')
                                    <span class="text-[10px]">(buyer pays)</span>
                                @endif
                            </p>
                        @else
                            <p class="text-[11px] text-green-600 mt-0.5">FREE</p>
                        @endif
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3.5 text-right">
                        <p class="text-[14px] font-bold text-[#1A1A1A]">
                            {{ number_format($order->total_amount, 2) }}
                            <span class="text-[11px] font-normal text-[#37474F]">{{ $order->currency }}</span>
                        </p>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">
                            <i class="fa-solid {{ $statusIcon }} text-[10px]"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5 text-center">
                        @if($order->canBeShipped())
                            <div class="flex flex-col items-center gap-1.5">
                                <button @click="shipOpen = true" type="button"
                                        class="inline-flex items-center gap-1.5 h-[28px] px-3 bg-[#1A1A1A] text-white text-[11px] font-semibold rounded hover:bg-[#37474F] transition">
                                    <i class="fa-solid fa-truck text-[10px]"></i>
                                    Ship
                                </button>
                                <button @click="cantShipOpen = true" type="button"
                                        class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-red-300 text-red-600 text-[11px] font-semibold rounded hover:bg-red-50 transition">
                                    <i class="fa-solid fa-ban text-[10px]"></i>
                                    Can't Ship
                                </button>
                            </div>
                        @elseif($order->canBeMarkedDelivered())
                            <form method="POST" action="{{ route('user.sales.deliver', $order) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 h-[28px] px-3 bg-green-600 text-white text-[11px] font-semibold rounded hover:bg-green-700 transition">
                                    <i class="fa-solid fa-box-open text-[10px]"></i>
                                    Delivered
                                </button>
                            </form>
                        @else
                            <span class="text-[#E0E0E0] text-[12px]">—</span>
                        @endif

                        {{-- ── Ship Modal ──────────────────────────────── --}}
                        <template x-teleport="body">
                            <div x-show="shipOpen" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                 @click.self="shipOpen = false">
                                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-xl w-full max-w-sm overflow-hidden">
                                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0] bg-[#37474F]">
                                        <i class="fa-solid fa-truck text-[#D4AF37] text-sm"></i>
                                        <h3 class="text-[13px] font-semibold text-white">Mark as Shipped</h3>
                                    </div>
                                    <div class="px-5 py-4">
                                        <p class="text-[12px] text-[#37474F] mb-4">
                                            Order <span class="font-mono font-semibold text-[#1A1A1A]">{{ $order->order_number }}</span>
                                        </p>
                                        <form method="POST" action="{{ route('user.sales.ship', $order) }}">
                                            @csrf
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                                Tracking Number <span class="font-normal normal-case">(optional)</span>
                                            </label>
                                            <input type="text" name="tracking_number"
                                                   placeholder="e.g. 1Z999AA10123456784"
                                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                                            <div class="flex gap-3 mt-4">
                                                <button type="submit"
                                                        class="flex-1 inline-flex items-center justify-center gap-2 h-[34px] bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                                                    <i class="fa-solid fa-truck text-xs"></i>
                                                    Confirm Shipped
                                                </button>
                                                <button type="button" @click="shipOpen = false"
                                                        class="flex-1 inline-flex items-center justify-center h-[34px] border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- ── Can't Ship Modal ─────────────────────────── --}}
                        <template x-teleport="body">
                            <div x-show="cantShipOpen" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                 @click.self="cantShipOpen = false">
                                <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-xl w-full max-w-sm overflow-hidden">
                                    <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0] bg-[#37474F]">
                                        <i class="fa-solid fa-ban text-red-400 text-sm"></i>
                                        <h3 class="text-[13px] font-semibold text-white">Cannot Ship</h3>
                                    </div>
                                    <div class="px-5 py-4">
                                        <p class="text-[12px] text-[#37474F] mb-4">
                                            The buyer will be notified of the reason.
                                        </p>
                                        <form method="POST" action="{{ route('user.sales.cannot-ship', $order) }}">
                                            @csrf
                                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                                Reason <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="reason" rows="3" required
                                                      placeholder="Explain why this order cannot be shipped..."
                                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none"></textarea>
                                            <div class="flex gap-3 mt-4">
                                                <button type="submit"
                                                        class="flex-1 inline-flex items-center justify-center gap-2 h-[34px] bg-red-600 text-white text-[13px] font-semibold rounded hover:bg-red-700 transition">
                                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                    Confirm & Notify
                                                </button>
                                                <button type="button" @click="cantShipOpen = false"
                                                        class="flex-1 inline-flex items-center justify-center h-[34px] border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
    @endif

    @endif

</x-app-layout>
