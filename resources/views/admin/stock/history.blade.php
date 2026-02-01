@extends('admin.layouts.app')

@section('title', 'Stock Movement History')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.stock.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
                ← Back to Stock Management
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Stock Movement History
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Complete audit trail of all stock movements
            </p>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6 p-4">
            <form method="GET" action="{{ route('admin.stock.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="SKU or reference..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type"
                            id="type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Damage</option>
                        <option value="initial" {{ request('type') == 'initial' ? 'selected' : '' }}>Initial</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date"
                           name="date_from"
                           id="date_from"
                           value="{{ request('date_from') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date"
                           name="date_to"
                           id="date_to"
                           value="{{ request('date_to') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Movement History Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product / SKU
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Change
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Before → After
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reference / Notes
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            By
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $movement->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $movement->created_at->format('H:i:s') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $movement->productVariation->listing->title }}
                            </div>
                            <div class="text-xs text-gray-500">
                                SKU: {{ $movement->productVariation->sku }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeColors = [
                                    'purchase' => 'bg-blue-100 text-blue-800',
                                    'sale' => 'bg-purple-100 text-purple-800',
                                    'return' => 'bg-green-100 text-green-800',
                                    'adjustment' => 'bg-yellow-100 text-yellow-800',
                                    'damage' => 'bg-red-100 text-red-800',
                                    'initial' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$movement->type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold {{ $movement->isIncrease() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->isIncrease() ? '+' : '' }}{{ $movement->quantity_change }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                        </td>
                        <td class="px-6 py-4">
                            @if($movement->reference)
                            <div class="text-sm text-gray-900">{{ $movement->reference }}</div>
                            @endif
                            @if($movement->notes)
                            <div class="text-xs text-gray-500 max-w-xs truncate">{{ $movement->notes }}</div>
                            @endif
                            @if(!$movement->reference && !$movement->notes)
                            <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $movement->user->name ?? 'System' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No stock movements found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($movements->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $movements->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
