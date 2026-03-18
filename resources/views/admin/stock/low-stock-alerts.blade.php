@extends('admin.layouts.app')

@section('title', 'Low Stock Alerts')
@section('page-title', 'Low Stock Alerts')

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
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Low Stock Alerts</h2>
            <p class="text-[#37474F] text-sm mt-1">Products that need restocking soon</p>
        </div>
    </div>

    @if($lowStockVariations->count() > 0)

        <!-- Alert Banner -->
        <div class="mb-5 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-yellow-500 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-[13px] font-semibold text-yellow-800">
                    {{ $lowStockVariations->count() }} {{ $lowStockVariations->count() === 1 ? 'product variation is' : 'product variations are' }} running low on stock
                </p>
                <p class="text-[12px] text-yellow-700 mt-0.5">Please review and restock these items to avoid stockouts.</p>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            @foreach($lowStockVariations as $variation)
                <div class="{{ !$loop->last ? 'border-b border-[#E0E0E0]' : '' }} p-5 hover:bg-[#D4AF37]/5 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">

                            <!-- Product Info -->
                            <div class="mb-3">
                                <h3 class="text-[14px] font-semibold text-[#1A1A1A]">{{ $variation->listing->title }}</h3>
                                <p class="text-[12px] text-[#37474F] mt-0.5">
                                    SKU: {{ $variation->sku }}
                                    @if($variation->listing->category?->name)
                                        · {{ $variation->listing->category->name }}
                                    @endif
                                </p>
                            </div>

                            <!-- Variant Attributes -->
                            @if($variation->attributes->count())
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($variation->attributes as $attr)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-medium bg-gray-100 text-[#37474F]">
                                            {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Stock Metrics -->
                            <div class="flex items-center gap-6 mb-3">
                                <div>
                                    <p class="text-[11px] text-[#37474F]">Current Stock</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ $variation->stock_quantity }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-[#37474F]">Threshold</p>
                                    <p class="text-xl font-semibold text-[#1A1A1A]">{{ $variation->low_stock_threshold }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-[#37474F]">Price</p>
                                    <p class="text-xl font-semibold text-[#1A1A1A]">${{ number_format($variation->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-[#37474F]">Restock Value</p>
                                    <p class="text-xl font-semibold text-[#1A1A1A]">
                                        ${{ number_format(($variation->low_stock_threshold - $variation->stock_quantity + 10) * $variation->price, 2) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @php
                                $percentage = $variation->low_stock_threshold > 0
                                    ? min(100, ($variation->stock_quantity / $variation->low_stock_threshold) * 100)
                                    : 0;
                            @endphp
                            <div>
                                <div class="overflow-hidden h-1.5 rounded bg-[#E0E0E0]">
                                    <div style="width: {{ $percentage }}%"
                                         class="h-full rounded bg-yellow-500 transition-all duration-300"></div>
                                </div>
                                <p class="text-[11px] text-[#37474F] mt-1">{{ number_format($percentage, 1) }}% of threshold</p>
                            </div>

                        </div>

                        <!-- Action Button -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.stock.edit', $variation) }}"
                               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                                <i class="fa-solid fa-plus text-xs"></i>
                                Restock
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else

        <!-- Empty State -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] text-center py-16">
            <i class="fa-solid fa-circle-check text-3xl text-green-500 mb-3 block"></i>
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-1">All Stock Levels Look Good!</h3>
            <p class="text-[#37474F] text-[13px] mb-5">No products are currently running low on stock.</p>
            <a href="{{ route('admin.stock.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                View All Stock
            </a>
        </div>

    @endif

</div>
@endsection
