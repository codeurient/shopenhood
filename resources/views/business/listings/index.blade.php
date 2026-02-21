<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Business Listings</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($user->isBusinessUser())

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-end">
                    <a href="{{ route('business.listings.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                        + Create Listing
                    </a>
                </div>

                <!-- Active Listings -->
                <div class="bg-white rounded-lg shadow overflow-hidden mt-4">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Active Listings ({{ $activeListings->count() }})</h3>
                    </div>

                    @if($activeListings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Listing</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activeListings as $listing)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $listing->title }}</div>
                                                @if($listing->listingType)
                                                    <div class="text-xs text-gray-500">{{ $listing->listingType->name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $listing->category->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $listing->variations->count() }} {{ Str::plural('variant', $listing->variations->count()) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'draft'    => 'bg-gray-100 text-gray-700',
                                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                                'active'   => 'bg-green-100 text-green-700',
                                                'sold'     => 'bg-blue-100 text-blue-700',
                                                'expired'  => 'bg-red-100 text-red-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($listing->status) }}
                                        </span>
                                        @if($listing->status === 'rejected' && $listing->rejection_reason)
                                            <p class="text-xs text-red-500 mt-1 max-w-xs truncate" title="{{ $listing->rejection_reason }}">
                                                {{ $listing->rejection_reason }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('business.listings.edit', $listing) }}"
                                               class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('business.listings.toggle', $listing) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs transition">
                                                    {{ $listing->is_visible ? 'Hide' : 'Show' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('business.listings.destroy', $listing) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this listing?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                        </svg>
                        <p class="text-gray-500 text-lg mb-2">No business listings yet</p>
                        <a href="{{ route('business.listings.create') }}" class="text-blue-600 hover:underline">
                            Create your first business listing
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Deleted Listings -->
                @if($trashedListings->count() > 0)
                <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Deleted Listings ({{ $trashedListings->count() }})
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Deleted listings are kept for {{ $retentionDays }} days before being permanently removed.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Listing</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deleted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($trashedListings as $listing)
                                <tr class="hover:bg-gray-50 opacity-75">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $listing->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $listing->category->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $listing->deleted_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <form action="{{ route('business.listings.reshare', $listing->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs transition">
                                                    Reshare
                                                </button>
                                            </form>
                                            <form action="{{ route('business.listings.force-destroy', $listing->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Permanently delete? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                                    Permanently Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            @else

                {{-- Upgrade to Business Account --}}
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Upgrade to Business Account</h1>
                        <p class="text-gray-500 mb-8">Unlock powerful tools to grow your business on {{ config('app.name') }}.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left mb-8">
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">Multiple Listings</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Publish as many products as your plan allows.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">Product Variants</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Manage SKUs with different sizes, colours, and prices.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">Wholesale Pricing</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Set minimum order quantities and bulk price tiers.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">SEO Tools &amp; Coupons</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Custom meta titles, descriptions, and discount coupons.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <p class="text-blue-800 font-medium mb-1">Ready to upgrade?</p>
                            <p class="text-blue-600 text-sm mb-4">
                                Business accounts are available on a paid plan. Contact our admin team to get started.
                            </p>
                            <a href="mailto:{{ config('mail.from.address', 'admin@'.parse_url(config('app.url'), PHP_URL_HOST)) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Contact Admin
                            </a>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('user.listings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                                &larr; Back to My Listings
                            </a>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
