<x-guest-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('business.stock.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2 inline-block">
                    &larr; Back to Stock Management
                </a>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Adjust Stock — {{ $variation->sku }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $variation->listing->title }}</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Adjustment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Adjust Stock</h3>

                        <form method="POST" action="{{ route('business.stock.adjust', $variation) }}">
                            @csrf

                            <div class="space-y-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Transaction Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="type" id="type" required
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                                        <option value="">Select type...</option>
                                        <option value="purchase" {{ old('type') == 'purchase' ? 'selected' : '' }}>Purchase (Add Stock)</option>
                                        <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>Return (Add Stock)</option>
                                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Quantity <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="quantity" id="quantity" min="1" required
                                           value="{{ old('quantity') }}" placeholder="Enter quantity"
                                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Current stock: <strong>{{ $variation->stock_quantity }}</strong> units
                                    </p>
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Reference (Order #, Invoice #, etc.)
                                    </label>
                                    <input type="text" name="reference" id="reference"
                                           value="{{ old('reference') }}" placeholder="PO-12345"
                                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reference') border-red-500 @enderror">
                                    @error('reference')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              placeholder="Additional notes about this stock adjustment..."
                                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 flex justify-between">
                                <a href="{{ route('business.stock.index') }}"
                                   class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-sm font-medium transition">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
                                    Adjust Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar: Product Info + History -->
                <div class="space-y-6">
                    <!-- Product Info -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Product Info</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $variation->sku }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Product</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $variation->listing->title }}</dd>
                            </div>
                            @if($variation->attributes->count())
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Variant</dt>
                                <dd class="mt-1 flex flex-wrap gap-1">
                                    @foreach($variation->attributes as $attr)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                            {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                        </span>
                                    @endforeach
                                </dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($variation->price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Stock</dt>
                                <dd class="mt-1">
                                    <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $variation->stock_quantity }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Threshold</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $variation->low_stock_threshold }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    @if($variation->stock_quantity <= 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300">Out of Stock</span>
                                    @elseif($variation->stock_quantity <= $variation->low_stock_threshold)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300">Low Stock</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">In Stock</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Recent Movements -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Movements</h3>
                        @if($variation->stockMovements->count() > 0)
                            <div class="space-y-3">
                                @foreach($variation->stockMovements as $movement)
                                <div class="border-l-4 pl-3 {{ $movement->isIncrease() ? 'border-green-500' : 'border-red-500' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($movement->type) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <span class="text-sm font-semibold {{ $movement->isIncrease() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $movement->isIncrease() ? '+' : '' }}{{ $movement->quantity_change }}
                                        </span>
                                    </div>
                                    @if($movement->reference)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ref: {{ $movement->reference }}</p>
                                    @endif
                                    @if($movement->notes)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $movement->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $movement->quantity_before }} &rarr; {{ $movement->quantity_after }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No movements yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
