@extends('admin.layouts.app')

@section('title', 'Adjust Stock - ' . $variation->sku)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.stock.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
                ← Back to Stock Management
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Adjust Stock - {{ $variation->sku }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ $variation->listing->title }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Stock Adjustment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Adjust Stock</h3>

                    @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.stock.adjust', $variation) }}">
                        @csrf

                        <div class="space-y-4">
                            <!-- Transaction Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">
                                    Transaction Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type"
                                        id="type"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('type') border-red-500 @enderror">
                                    <option value="">Select type...</option>
                                    <option value="purchase">Purchase (Add Stock)</option>
                                    <option value="sale">Sale (Remove Stock)</option>
                                    <option value="return">Return (Add Stock)</option>
                                    <option value="adjustment">Adjustment</option>
                                    <option value="damage">Damage/Loss (Remove Stock)</option>
                                </select>
                                @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input type="number"
                                           name="quantity"
                                           id="quantity"
                                           min="1"
                                           required
                                           placeholder="Enter quantity"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('quantity') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Current stock: <strong>{{ $variation->stock_quantity }}</strong> units
                                </p>
                                <p class="mt-1 text-xs text-gray-600">
                                    <strong>Note:</strong> The quantity will be automatically added or removed based on the transaction type selected above.
                                </p>
                                @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference -->
                            <div>
                                <label for="reference" class="block text-sm font-medium text-gray-700">
                                    Reference (Order #, Invoice #, etc.)
                                </label>
                                <input type="text"
                                       name="reference"
                                       id="reference"
                                       placeholder="PO-12345"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('reference') border-red-500 @enderror">
                                @error('reference')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                    Notes
                                </label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="3"
                                          placeholder="Additional notes about this stock adjustment..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('notes') border-red-500 @enderror"></textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('admin.stock.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-500 hover:bg-primary-600">
                                Adjust Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Info & Recent History -->
            <div class="space-y-6">
                <!-- Product Info -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Product Info</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $variation->sku }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $variation->listing->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Variant</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @foreach($variation->attributes as $attr)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">
                                        {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($variation->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
                            <dd class="mt-1">
                                <span class="text-2xl font-bold text-gray-900">{{ $variation->stock_quantity }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Low Stock Threshold</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $variation->low_stock_threshold }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($variation->stock_quantity <= 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @elseif($variation->stock_quantity <= $variation->low_stock_threshold)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        In Stock
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Recent Stock Movements -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Movements</h3>
                    @if($variation->stockMovements->count() > 0)
                    <div class="space-y-3">
                        @foreach($variation->stockMovements as $movement)
                        <div class="border-l-4 pl-3 {{ $movement->isIncrease() ? 'border-green-500' : 'border-red-500' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ ucfirst($movement->type) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $movement->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                                <span class="text-sm font-semibold {{ $movement->isIncrease() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->isIncrease() ? '+' : '' }}{{ $movement->quantity_change }}
                                </span>
                            </div>
                            @if($movement->reference)
                            <p class="text-xs text-gray-500 mt-1">Ref: {{ $movement->reference }}</p>
                            @endif
                            @if($movement->notes)
                            <p class="text-xs text-gray-600 mt-1">{{ $movement->notes }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-4">No movements yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
