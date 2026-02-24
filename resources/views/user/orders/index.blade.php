<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Orders</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($purchases->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No orders yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Your confirmed orders will appear here.</p>
                    <a href="{{ route('home') }}"
                       class="mt-4 inline-block px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition">
                        Start Shopping
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($purchases as $purchase)
                        @php
                            $statusColors = [
                                'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                'completed'  => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                            ];
                            $statusColor = $statusColors[$purchase->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <a href="{{ route('user.purchases.show', $purchase) }}"
                           class="block bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-md transition p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $purchase->purchase_number }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">
                                        {{ $purchase->orders->count() }} {{ Str::plural('item', $purchase->orders->count()) }}
                                        from {{ $purchase->orders->pluck('seller_id')->unique()->count() }} {{ Str::plural('seller', $purchase->orders->pluck('seller_id')->unique()->count()) }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $purchase->created_at->format('M d, Y · H:i') }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                    <span class="text-base font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($purchase->total_amount, 2) }} {{ $purchase->currency }}
                                    </span>
                                </div>
                            </div>

                            {{-- Item thumbnails --}}
                            @if($purchase->orders->isNotEmpty())
                                <div class="flex gap-2 mt-3">
                                    @foreach($purchase->orders->take(4) as $order)
                                        @php
                                            $img = $order->listing?->primaryImage ?? $order->listing?->firstImage;
                                        @endphp
                                        @if($img)
                                            <img src="{{ asset('storage/'.$img->image_path) }}"
                                                 alt="{{ $order->listing?->title }}"
                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                                                </svg>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($purchase->orders->count() > 4)
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500">
                                            +{{ $purchase->orders->count() - 4 }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $purchases->links() }}
                </div>
            @endif

        </div>
    </div>
</x-guest-layout>
