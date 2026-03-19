<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">My Orders</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Your purchase history</p>
        </div>
    </div>

    @if($purchases->isEmpty())
    {{-- ── Empty State ──────────────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm py-16 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#F5F5F5] flex items-center justify-center">
            <i class="fa-solid fa-bag-shopping text-[#37474F] text-xl"></i>
        </div>
        <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No orders yet</p>
        <p class="text-[13px] text-[#37474F] mb-4">Your confirmed purchases will appear here.</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-store text-xs"></i>
            Start Shopping
        </a>
    </div>

    @else
    {{-- ── Orders List ──────────────────────────────────────────────────── --}}
    <div class="space-y-3">
        @foreach($purchases as $purchase)
        @php
            $badge = match($purchase->status) {
                'pending'    => 'bg-yellow-100 text-yellow-700',
                'processing' => 'bg-blue-100 text-blue-700',
                'completed'  => 'bg-green-100 text-green-700',
                'cancelled'  => 'bg-red-100 text-red-600',
                default      => 'bg-[#E0E0E0] text-[#37474F]',
            };
            $statusIcon = match($purchase->status) {
                'pending'    => 'fa-clock',
                'processing' => 'fa-rotate',
                'completed'  => 'fa-circle-check',
                'cancelled'  => 'fa-circle-xmark',
                default      => 'fa-circle',
            };
        @endphp
        <a href="{{ route('user.purchases.show', $purchase) }}"
           class="block bg-white border border-[#E0E0E0] rounded-lg shadow-sm hover:border-[#D4AF37]/50 hover:shadow-md transition-all p-5">

            <div class="flex items-start justify-between gap-4">
                {{-- Left: order info --}}
                <div class="min-w-0">
                    <p class="text-[11px] font-mono text-[#37474F] mb-0.5">{{ $purchase->purchase_number }}</p>
                    <p class="text-[13px] font-semibold text-[#1A1A1A]">
                        {{ $purchase->orders->count() }} {{ Str::plural('item', $purchase->orders->count()) }}
                        <span class="text-[#37474F] font-normal">from</span>
                        {{ $purchase->orders->pluck('seller_id')->unique()->count() }} {{ Str::plural('seller', $purchase->orders->pluck('seller_id')->unique()->count()) }}
                    </p>
                    <p class="text-[11px] text-[#37474F] mt-0.5">
                        <i class="fa-regular fa-calendar text-[10px] mr-1"></i>
                        {{ $purchase->created_at->format('M d, Y · H:i') }}
                    </p>
                </div>

                {{-- Right: status + amount --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">
                        <i class="fa-solid {{ $statusIcon }} text-[10px]"></i>
                        {{ ucfirst($purchase->status) }}
                    </span>
                    <span class="text-[15px] font-bold text-[#1A1A1A]">
                        {{ number_format($purchase->total_amount, 2) }}
                        <span class="text-[12px] font-normal text-[#37474F]">{{ $purchase->currency }}</span>
                    </span>
                </div>
            </div>

            {{-- Item thumbnails --}}
            @if($purchase->orders->isNotEmpty())
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-[#E0E0E0]">
                @foreach($purchase->orders->take(5) as $order)
                @php $img = $order->listing?->primaryImage ?? $order->listing?->firstImage; @endphp
                @if($img)
                    <img src="{{ asset('storage/'.$img->image_path) }}"
                         alt="{{ $order->listing?->title }}"
                         class="w-10 h-10 rounded object-cover border border-[#E0E0E0] flex-shrink-0">
                @else
                    <div class="w-10 h-10 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-image text-[#37474F] text-xs"></i>
                    </div>
                @endif
                @endforeach
                @if($purchase->orders->count() > 5)
                <div class="w-10 h-10 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                    <span class="text-[11px] font-semibold text-[#37474F]">+{{ $purchase->orders->count() - 5 }}</span>
                </div>
                @endif
                <i class="fa-solid fa-chevron-right text-[#E0E0E0] text-xs ml-auto"></i>
            </div>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($purchases->hasPages())
    <div class="mt-6">
        {{ $purchases->links() }}
    </div>
    @endif

    @endif

</x-app-layout>
