@extends('admin.layouts.app')

@section('title', 'Listing Types')
@section('page-title', 'Listing Types')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Listing Types</h2>
            <p class="text-gray-600 mt-1">Manage listing types (sell, buy, gift, barter, auction)</p>
        </div>
        <a href="{{ route('admin.listing-types.create') }}" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
            ➕ Create Listing Type
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">📋</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-gray-600 text-sm">Total Types</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 text-2xl">✓</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    <p class="text-gray-600 text-sm">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-600 text-2xl">○</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                    <p class="text-gray-600 text-sm">Inactive</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.listing-types.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or description..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                🔍 Filter
            </button>
            <a href="{{ route('admin.listing-types.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                🔄 Reset
            </a>
        </form>
    </div>

    <!-- Listing Types Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($listingTypes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requires Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listings Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($listingTypes as $type)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($type->icon)
                                            <span class="text-2xl mr-2">{!! $type->icon !!}</span>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $type->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <code class="bg-gray-100 px-2 py-1 rounded">{{ $type->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ Str::limit($type->description, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($type->requires_price)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold">{{ $type->listings_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.listing-types.toggle-status', $type) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs rounded {{ $type->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $type->sort_order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.listing-types.edit', $type) }}" class="text-primary-600 hover:text-primary-700">
                                            Edit
                                        </a>
                                        @if($type->listings_count === 0)
                                            <form action="{{ route('admin.listing-types.destroy', $type) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing type?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400" title="Cannot delete: has {{ $type->listings_count }} listing(s)">Delete</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $listingTypes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <p class="text-gray-500 text-lg">No listing types found</p>
                <a href="{{ route('admin.listing-types.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                    Create your first listing type
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
