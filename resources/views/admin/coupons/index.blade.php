@extends('admin.layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Coupons</h2>
            <p class="text-gray-600 mt-1">Manage discount coupons and promotions</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            + Create Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">🎟️</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-gray-600 text-sm">Total Coupons</p>
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
                <div class="p-3 rounded-full bg-red-100 text-red-600 text-2xl">⏰</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['expired'] }}</p>
                    <p class="text-gray-600 text-sm">Expired</p>
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
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by code or description..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                </select>
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div>
                <select name="applicable_to" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Scope</option>
                    <option value="all" {{ request('applicable_to') === 'all' ? 'selected' : '' }}>All Products</option>
                    <option value="categories" {{ request('applicable_to') === 'categories' ? 'selected' : '' }}>Categories</option>
                    <option value="listings" {{ request('applicable_to') === 'listings' ? 'selected' : '' }}>Listings</option>
                    <option value="users" {{ request('applicable_to') === 'users' ? 'selected' : '' }}>Users</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Filter
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Reset
            </a>
        </form>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applies To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="bg-gray-100 px-2 py-1 rounded font-mono font-semibold text-sm">{{ $coupon->code }}</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs rounded {{ $coupon->type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($coupon->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    @if($coupon->type === 'percentage')
                                        {{ rtrim(rtrim($coupon->value, '0'), '.') }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="capitalize">{{ $coupon->applicable_to === 'all' ? 'All Products' : ucfirst($coupon->applicable_to) }}</span>
                                    @if($coupon->restrictions_count > 0)
                                        <span class="text-xs text-gray-400">({{ $coupon->restrictions_count }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold">{{ $coupon->usage_count }}</span>
                                    <span class="text-gray-400">/ {{ $coupon->usage_limit ?? '∞' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        @if($coupon->starts_at)
                                            <span class="text-xs">From: {{ $coupon->starts_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($coupon->expires_at)
                                            <span class="text-xs {{ $coupon->expires_at->isPast() ? 'text-red-500 font-semibold' : '' }}">
                                                To: {{ $coupon->expires_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($coupon->expires_at && $coupon->expires_at->isPast())
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Expired</span>
                                    @else
                                        <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2 py-1 text-xs rounded {{ $coupon->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
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

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $coupons->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎟️</div>
                <p class="text-gray-500 text-lg">No coupons found</p>
                <a href="{{ route('admin.coupons.create') }}" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                    Create your first coupon
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
