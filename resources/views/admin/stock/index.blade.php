@extends('admin.layouts.app')

@section('title', 'Stock Management')
@section('page-title', 'Stock Management')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Stock Management</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage product variation stock levels</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.stock.low-stock-alerts') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-bell text-xs"></i>
                Low Stock Alerts
            </a>
            <a href="{{ route('admin.stock.history') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                Movement History
            </a>
            <a href="{{ route('admin.stock.export') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-download text-xs"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-boxes-stacked text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['total_variations']) }}</p>
                    <p class="text-[#37474F] text-xs">Total Variations</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['low_stock_count']) }}</p>
                    <p class="text-[#37474F] text-xs">Low Stock</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#C0392B]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-[#C0392B]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['out_of_stock_count']) }}</p>
                    <p class="text-[#37474F] text-xs">Out of Stock</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-dollar-sign text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">${{ number_format($stats['total_stock_value'], 2) }}</p>
                    <p class="text-[#37474F] text-xs">Stock Value</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU or product name..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="stock_status"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Status</option>
                <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>In Stock</option>
                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
            </select>

            <select name="sort_by"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="stock_quantity" {{ request('sort_by') == 'stock_quantity' ? 'selected' : '' }}>Sort: Quantity</option>
                <option value="sku" {{ request('sort_by') == 'sku' ? 'selected' : '' }}>Sort: SKU</option>
                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Sort: Price</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.stock.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($variations->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Product / SKU</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Variant</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Price</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Stock</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($variations as $variation)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $variation->listing->title }}</p>
                                    <p class="text-[12px] text-[#37474F]">SKU: {{ $variation->sku }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($variation->attributes as $attr)
                                            <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-medium bg-gray-100 text-[#37474F]">
                                                {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">${{ number_format($variation->price, 2) }}</td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] font-semibold text-[#1A1A1A]">{{ $variation->stock_quantity }}</p>
                                    <p class="text-[12px] text-[#37474F]">Threshold: {{ $variation->low_stock_threshold }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($variation->stock_quantity <= 0)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Out of Stock</span>
                                    @elseif($variation->stock_quantity <= $variation->low_stock_threshold)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700">Low Stock</span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">In Stock</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('admin.stock.edit', $variation) }}"
                                       class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition">
                                        <i class="fa-solid fa-sliders text-xs"></i>
                                        Adjust
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($variations->hasPages())
                <div class="px-4 py-3 border-t border-[#E0E0E0]">
                    {{ $variations->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-boxes-stacked text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No stock variations found</p>
            </div>
        @endif
    </div>

</div>
@endsection
