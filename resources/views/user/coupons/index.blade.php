<x-guest-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Coupons</h2>
            <a href="{{ route('user.coupons.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
                + Create Coupon
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Expired</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Inactive</p>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['inactive'] }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                <form method="GET" action="{{ route('user.coupons.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or description..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div>
                        <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div>
                        <select name="applicable_to" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Scope</option>
                            <option value="all" {{ request('applicable_to') === 'all' ? 'selected' : '' }}>All Products</option>
                            <option value="categories" {{ request('applicable_to') === 'categories' ? 'selected' : '' }}>Categories</option>
                            <option value="listings" {{ request('applicable_to') === 'listings' ? 'selected' : '' }}>Listings</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
                        Filter
                    </button>
                    <a href="{{ route('user.coupons.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Coupons Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                @if($coupons->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Scope</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $coupon->code }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($coupon->type) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($coupon->applicable_to) }}
                                    @if($coupon->restrictions_count > 0)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $coupon->restrictions_count }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $coupon->usages_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                                    @endphp
                                    @if($isExpired)
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">Expired</span>
                                    @elseif($coupon->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('user.coupons.edit', $coupon) }}"
                                           class="px-3 py-1 bg-primary-500 text-white rounded hover:bg-primary-600 text-xs transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('user.coupons.toggle-status', $coupon) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-500 text-xs transition">
                                                {{ $coupon->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('user.coupons.destroy', $coupon) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this coupon?');">
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

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $coupons->links() }}
                </div>

                @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No coupons yet</p>
                    <a href="{{ route('user.coupons.create') }}" class="text-primary-600 dark:text-primary-300 hover:underline">
                        Create your first coupon
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
