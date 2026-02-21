<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Business Listings</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
        </div>
    </div>
</x-app-layout>
