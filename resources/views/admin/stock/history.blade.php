@extends('admin.layouts.app')

@section('title', 'Stock Movement History')
@section('page-title', 'Stock Movement History')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.stock.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Stock Management
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Stock Movement History</h2>
            <p class="text-[#37474F] text-sm mt-1">Complete audit trail of all stock movements</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.stock.history') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU or reference..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="type"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Types</option>
                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Damage</option>
                <option value="initial" {{ request('type') == 'initial' ? 'selected' : '' }}>Initial</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From date"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To date"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.stock.history') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Date & Time</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Product / SKU</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Type</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Change</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Before → After</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Reference / Notes</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] text-[#1A1A1A]">{{ $movement->created_at->format('M d, Y') }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $movement->created_at->format('H:i:s') }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $movement->productVariation->listing->title }}</p>
                                    <p class="text-[12px] text-[#37474F]">SKU: {{ $movement->productVariation->sku }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    @php
                                        $typeConfig = [
                                            'purchase'   => ['bg-blue-50 text-blue-700',     'fa-cart-plus'],
                                            'sale'       => ['bg-purple-50 text-purple-700',  'fa-receipt'],
                                            'return'     => ['bg-green-50 text-green-700',    'fa-rotate-left'],
                                            'adjustment' => ['bg-yellow-50 text-yellow-700',  'fa-sliders'],
                                            'damage'     => ['bg-red-50 text-[#C0392B]',      'fa-triangle-exclamation'],
                                            'initial'    => ['bg-gray-100 text-[#37474F]',    'fa-boxes-stacked'],
                                        ];
                                        [$cls, $icon] = $typeConfig[$movement->type] ?? ['bg-gray-100 text-[#37474F]', 'fa-circle'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $cls }}">
                                        <i class="fa-solid {{ $icon }} text-[10px]"></i>
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="text-[13px] font-semibold {{ $movement->isIncrease() ? 'text-green-600' : 'text-[#C0392B]' }}">
                                        {{ $movement->isIncrease() ? '+' : '' }}{{ $movement->quantity_change }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($movement->reference)
                                        <p class="text-[13px] text-[#1A1A1A]">{{ $movement->reference }}</p>
                                    @endif
                                    @if($movement->notes)
                                        <p class="text-[12px] text-[#37474F] max-w-xs truncate">{{ $movement->notes }}</p>
                                    @endif
                                    @if(!$movement->reference && !$movement->notes)
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    {{ $movement->user->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-12 text-center">
                                    <i class="fa-solid fa-clock-rotate-left text-3xl text-[#E0E0E0] mb-3 block"></i>
                                    <p class="text-[#37474F] text-[13px]">No stock movements found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="px-4 py-3 border-t border-[#E0E0E0]">
                    {{ $movements->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-clock-rotate-left text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No stock movements found</p>
            </div>
        @endif
    </div>

</div>
@endsection
