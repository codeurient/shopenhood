@extends('admin.layouts.app')

@section('title', 'Listings Management')
@section('page-title', 'Listings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Listings</h2>
            <p class="text-gray-600 mt-1">Manage product listings</p>
        </div>
        <a href="{{ route('admin.listings.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            ➕ Create Listing
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            ✗ {{ session('error') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-gray-600 text-sm">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            <p class="text-gray-600 text-sm">Active</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-gray-600 text-sm">Pending</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-gray-600 text-sm">Rejected</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-gray-600">{{ $stats['draft'] }}</p>
            <p class="text-gray-600 text-sm">Drafts</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['deleted'] }}</p>
            <p class="text-gray-600 text-sm">Deleted</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search listings..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.listings.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    ↻ Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Listing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($listings as $listing)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($listing->primaryImage)
                                <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}" 
                                     alt="{{ $listing->title }}" 
                                     class="w-16 h-16 rounded object-cover mr-4">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center mr-4">
                                    <span class="text-2xl">📦</span>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $listing->title }}</div>
                                <div class="text-sm text-gray-500">{{ $listing->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $listing->category->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $listing->listingType->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if($listing->base_price)
                            {{ $listing->currency }} {{ number_format($listing->base_price, 2) }}
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($listing->trashed())
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-700">
                                Deleted
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ $listing->deleted_at->diffForHumans() }}</div>
                        @else
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'active' => 'bg-green-100 text-green-700',
                                    'sold' => 'bg-blue-100 text-blue-700',
                                    'expired' => 'bg-red-100 text-red-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex gap-2 flex-wrap">
                            @if($listing->trashed())
                                <form method="POST" action="{{ route('admin.listings.restore', $listing->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Restore</button>
                                </form>
                                <form action="{{ route('admin.listings.force-delete', $listing->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Permanently delete this listing? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Permanently Delete
                                    </button>
                                </form>
                            @else
                                @if($listing->isPending())
                                <form method="POST" action="{{ route('admin.listings.approval.approve', $listing) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                                </form>
                                @endif
                                <a href="{{ route('admin.listings.show', $listing) }}"
                                   class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 inline-block">
                                    View
                                </a>
                                <a href="{{ route('admin.listings.edit', $listing) }}"
                                   class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 inline-block">
                                    Edit
                                </a>
                                <form action="{{ route('admin.listings.destroy', $listing) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this listing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-6xl mb-4">📦</div>
                        <p class="text-gray-600 text-lg mb-2">No listings found</p>
                        <a href="{{ route('admin.listings.create') }}" class="text-indigo-600 hover:underline">
                            Create your first listing
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($listings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection