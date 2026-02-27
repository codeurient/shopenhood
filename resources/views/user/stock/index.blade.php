<x-guest-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Stock Management</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor and adjust stock for your listings</p>
                </div>
                <a href="{{ route('business.listings.index') }}"
                   class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    &larr; My Listings
                </a>
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

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                    <div class="flex items-center gap-4">
                        <div class="text-3xl">📦</div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Variations</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_variations']) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                    <div class="flex items-center gap-4">
                        <div class="text-3xl">⚠️</div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock</dt>
                            <dd class="text-2xl font-semibold text-orange-600">{{ number_format($stats['low_stock_count']) }}</dd>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                    <div class="flex items-center gap-4">
                        <div class="text-3xl">🚫</div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Out of Stock</dt>
                            <dd class="text-2xl font-semibold text-red-600">{{ number_format($stats['out_of_stock_count']) }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
                <form method="GET" action="{{ route('business.stock.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="SKU or product name..."
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Status</label>
                        <select name="stock_status" id="stock_status"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>In Stock</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                        <div class="flex gap-2">
                            <select name="sort_by" id="sort_by"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="stock_quantity" {{ request('sort_by') == 'stock_quantity' ? 'selected' : '' }}>Stock Qty</option>
                                <option value="sku" {{ request('sort_by') == 'sku' ? 'selected' : '' }}>SKU</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                            </select>
                            <button type="submit"
                                    class="px-4 py-2 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 transition">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Stock Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product / SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Variant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($variations as $variation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $variation->listing->title }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    SKU: {{ $variation->sku }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($variation->attributes as $attr)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                            {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                ${{ number_format($variation->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $variation->stock_quantity }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Threshold: {{ $variation->low_stock_threshold }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($variation->stock_quantity <= 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300">
                                        Out of Stock
                                    </span>
                                @elseif($variation->stock_quantity <= $variation->low_stock_threshold)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('business.stock.edit', $variation) }}"
                                   class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                                    Adjust Stock
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No stock-managed variations found. Enable stock management on your listings to track inventory.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($variations->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $variations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
