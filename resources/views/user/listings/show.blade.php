<x-guest-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('user.listings.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2 inline-block">
                    &larr; Back to Listings
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $listing->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Created {{ $listing->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex gap-2">
                @if(!in_array($listing->status, ['sold', 'expired']))
                <a href="{{ route('user.listings.edit', $listing) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 transition">
                    Edit Listing
                </a>
                @endif
                <form action="{{ route('user.listings.toggle', $listing) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        {{ $listing->is_visible ? 'Hide' : 'Show' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Status Banners --}}
            @if($listing->status === 'pending')
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium text-yellow-800 dark:text-yellow-200">This listing is pending approval</span>
                </div>
            </div>
            @endif

            @if($listing->isRejected() && $listing->rejection_reason)
            <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-medium text-red-800 dark:text-red-200">This listing was rejected</p>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ $listing->rejection_reason }}</p>
                        @if($listing->rejected_at)
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1">Rejected {{ $listing->rejected_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Basic Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->category->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Listing Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->listingType->name ?? '-' }}</dd>
                                </div>
                                @if($listing->base_price)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Base Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->currency ?? 'USD' }} {{ number_format($listing->base_price, 2) }}</dd>
                                </div>
                                @endif
                                @if($listing->discount_price)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Discount Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $listing->currency ?? 'USD' }} {{ number_format($listing->discount_price, 2) }}
                                        @if($listing->discount_start_date && $listing->discount_end_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ $listing->discount_start_date->format('M d') }} - {{ $listing->discount_end_date->format('M d') }})
                                        </span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                @if($listing->store_name)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Store Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->store_name }}</dd>
                                </div>
                                @endif
                            </dl>

                            @if($listing->short_description)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Short Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $listing->short_description }}</dd>
                            </div>
                            @endif

                            @if($listing->description)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 prose prose-sm dark:prose-invert max-w-none">
                                    {!! nl2br(e($listing->description)) !!}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Listing Variants (sidebar attribute selections) -->
                    @if($listing->listingVariants && $listing->listingVariants->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Attributes</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                @foreach($listing->listingVariants->groupBy('variant_id') as $variantId => $items)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $items->first()->variant->name ?? 'Attribute' }}</dt>
                                    <dd class="mt-1 flex flex-wrap gap-1">
                                        @foreach($items as $item)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            @if($item->variantItem)
                                                {{ $item->variantItem->value }}
                                            @elseif($item->custom_value)
                                                {{ $item->custom_value }}
                                            @endif
                                        </span>
                                        @endforeach
                                    </dd>
                                </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                    @endif

                    <!-- Product Variations -->
                    @if($listing->variations && $listing->variations->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Product Variations ({{ $listing->variations->count() }})</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Variant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($listing->variations as $variation)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $variation->sku }}
                                            @if($variation->is_default)
                                            <span class="ml-2 px-2 py-0.5 text-xs bg-primary-100 dark:bg-primary-900/50 text-primary-800 dark:text-primary-300 rounded">Default</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @foreach($variation->attributes as $attr)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 mr-1">
                                                {{ $attr->variant->name ?? '' }}: {{ $attr->variantItem->value ?? '' }}
                                            </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $listing->currency ?? 'USD' }} {{ number_format($variation->price, 2) }}
                                            @if($variation->discount_price)
                                            <br><span class="text-xs text-green-600 dark:text-green-400">{{ $listing->currency ?? 'USD' }} {{ number_format($variation->discount_price, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $variation->stock_quantity ?? 0 }}
                                            @if(($variation->stock_quantity ?? 0) <= ($variation->low_stock_threshold ?? 5) && ($variation->stock_quantity ?? 0) > 0)
                                            <span class="text-xs text-orange-600 dark:text-orange-400">(Low)</span>
                                            @elseif(($variation->stock_quantity ?? 0) <= 0)
                                            <span class="text-xs text-red-600 dark:text-red-400">(Out)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($variation->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">
                                                Active
                                            </span>
                                            @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                                Inactive
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Images -->
                    @if($listing->images && $listing->images->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Images ({{ $listing->images->count() }})</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                @foreach($listing->images as $image)
                                <div class="relative aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="{{ $listing->title }}"
                                         class="w-full h-full object-cover">
                                    @if($image->is_primary)
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-primary-500 text-white text-xs rounded">
                                        Primary
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status & Visibility -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status & Visibility</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Status</span>
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200',
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
                                        'active' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
                                        'sold' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                                        'expired' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
                                        'rejected' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Visible</span>
                                <span class="text-sm font-medium {{ $listing->is_visible ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $listing->is_visible ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Negotiable</span>
                                <span class="text-sm font-medium {{ $listing->is_negotiable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $listing->is_negotiable ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($listing->views_count > 0)
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Views</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($listing->views_count) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location -->
                    @if($listing->country || $listing->city)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Location</h3>
                        </div>
                        <div class="px-6 py-4 space-y-2">
                            @if($listing->country)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->country }}</dd>
                            </div>
                            @endif
                            @if($listing->city)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $listing->city }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Timestamps</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $listing->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Updated</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $listing->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @if($listing->expires_at)
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $listing->expires_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
