@extends('admin.layouts.app')

@section('title', 'Low Stock Alerts')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.stock.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
                ← Back to Stock Management
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Low Stock Alerts
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Products that need restocking soon
            </p>
        </div>

        @if($lowStockVariations->count() > 0)
        <!-- Alert Banner -->
        <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-orange-800">
                        {{ $lowStockVariations->count() }} {{ $lowStockVariations->count() === 1 ? 'product variation is' : 'product variations are' }} running low on stock
                    </h3>
                    <p class="mt-2 text-sm text-orange-700">
                        Please review and restock these items to avoid stockouts.
                    </p>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($lowStockVariations as $variation)
                <li class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <!-- Product Info -->
                            <div class="mb-3">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $variation->listing->title }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    SKU: {{ $variation->sku }} • {{ $variation->listing->category->name }}
                                </p>
                            </div>

                            <!-- Variant Info -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($variation->attributes as $attr)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                    </span>
                                @endforeach
                            </div>

                            <!-- Stock Info -->
                            <div class="flex items-center gap-6">
                                <div>
                                    <p class="text-xs text-gray-500">Current Stock</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ $variation->stock_quantity }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Threshold</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $variation->low_stock_threshold }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Price</p>
                                    <p class="text-lg font-semibold text-gray-900">${{ number_format($variation->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Restock Value</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        ${{ number_format(($variation->low_stock_threshold - $variation->stock_quantity + 10) * $variation->price, 2) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-3">
                                @php
                                    $percentage = ($variation->stock_quantity / $variation->low_stock_threshold) * 100;
                                    $percentage = min(100, $percentage);
                                @endphp
                                <div class="relative">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                        <div style="width: {{ $percentage }}%"
                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-orange-500 transition-all duration-300"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ number_format($percentage, 1) }}% of threshold
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="ml-6 flex-shrink-0">
                            <a href="{{ route('admin.stock.edit', $variation) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Restock
                            </a>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white shadow rounded-lg p-12">
            <div class="text-center">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    All Stock Levels Look Good!
                </h3>
                <p class="text-sm text-gray-500">
                    No products are currently running low on stock.
                </p>
                <div class="mt-6">
                    <a href="{{ route('admin.stock.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-500 hover:bg-primary-600">
                        View All Stock
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
