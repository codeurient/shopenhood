@extends('admin.layouts.app')

@section('title', 'Listing Details: ' . $listing->title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.listings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
                    ← Back to Listings
                </a>
                <h2 class="text-2xl font-bold text-gray-900">{{ $listing->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Created {{ $listing->created_at->diffForHumans() }} by {{ $listing->user->name }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.listings.edit', $listing) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Edit Listing
                </a>
                <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this listing?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Approval Panel --}}
        @if($listing->isPending())
        <div x-data="{ showRejectModal: false }" class="mb-6 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium text-yellow-800">This listing is pending approval</span>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.listings.approval.approve', $listing) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Approve
                        </button>
                    </form>
                    <button @click="showRejectModal = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </div>

            {{-- Rejection Modal --}}
            <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @keydown.escape.window="showRejectModal = false">
                <div @click.away="showRejectModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Listing</h3>
                    <form method="POST" action="{{ route('admin.listings.approval.reject', $listing) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" required maxlength="500"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Explain why this listing is being rejected..."></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">Reject Listing</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Rejection Info --}}
        @if($listing->isRejected() && $listing->rejection_reason)
        <div class="mb-6 bg-red-50 border border-red-300 rounded-lg p-4">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-medium text-red-800">This listing was rejected</p>
                    <p class="text-sm text-red-700 mt-1">{{ $listing->rejection_reason }}</p>
                    @if($listing->rejected_at)
                    <p class="text-xs text-red-500 mt-1">Rejected {{ $listing->rejected_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Title</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $listing->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $listing->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $listing->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Listing Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $listing->listingType->name }}</dd>
                            </div>
                            @if($listing->base_price)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Base Price</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $listing->currency }} {{ number_format($listing->base_price, 2) }}</dd>
                            </div>
                            @endif
                            @if($listing->discount_price)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Discount Price</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $listing->currency }} {{ number_format($listing->discount_price, 2) }}
                                    <span class="text-xs text-gray-500">
                                        ({{ $listing->discount_start_date->format('M d') }} - {{ $listing->discount_end_date->format('M d') }})
                                    </span>
                                </dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'sold' => 'bg-blue-100 text-blue-800',
                                            'expired' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($listing->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        @if($listing->description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                            <dd class="text-sm text-gray-900 prose prose-sm max-w-none">
                                {!! nl2br(e($listing->description)) !!}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Variations -->
                @if($listing->variations->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Product Variations ({{ $listing->variations->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($listing->variations as $variation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $variation->sku }}
                                        @if($variation->is_default)
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-100 text-indigo-800 rounded">Default</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @foreach($variation->attributes as $attr)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                            {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                        </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($variation->price, 2) }}
                                        @if($variation->discount_price)
                                        <br><span class="text-xs text-green-600">${{ number_format($variation->discount_price, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $variation->stock_quantity }}
                                        @if($variation->stock_quantity <= $variation->low_stock_threshold && $variation->stock_quantity > 0)
                                        <span class="text-xs text-orange-600">(Low)</span>
                                        @elseif($variation->stock_quantity <= 0)
                                        <span class="text-xs text-red-600">(Out)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($variation->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('admin.stock.edit', $variation) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Manage Stock
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Images -->
                @if($listing->images->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Images ({{ $listing->images->count() }})</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            @foreach($listing->images as $image)
                            <div class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover">
                                @if($image->is_primary)
                                <span class="absolute top-2 left-2 px-2 py-1 bg-indigo-600 text-white text-xs rounded">
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
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Status & Visibility</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Visible</span>
                            <span class="text-sm font-medium {{ $listing->is_visible ? 'text-green-600' : 'text-red-600' }}">
                                {{ $listing->is_visible ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Featured</span>
                            <span class="text-sm font-medium {{ $listing->is_featured ? 'text-green-600' : 'text-red-600' }}">
                                {{ $listing->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Negotiable</span>
                            <span class="text-sm font-medium {{ $listing->is_negotiable ? 'text-green-600' : 'text-red-600' }}">
                                {{ $listing->is_negotiable ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        @if($listing->views_count > 0)
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <span class="text-sm text-gray-700">Views</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ number_format($listing->views_count) }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Location -->
                @if($listing->country || $listing->city)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Location</h3>
                    </div>
                    <div class="px-6 py-4 space-y-2">
                        @if($listing->country)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $listing->country }}</dd>
                        </div>
                        @endif
                        @if($listing->city)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">City</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $listing->city }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- SEO -->
                @if($listing->meta_title || $listing->meta_description)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">SEO</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        @if($listing->meta_title)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $listing->meta_title }}</dd>
                        </div>
                        @endif
                        @if($listing->meta_description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $listing->meta_description }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Timestamps -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Timestamps</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-gray-900">{{ $listing->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Updated</dt>
                            <dd class="mt-1 text-gray-900">{{ $listing->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
