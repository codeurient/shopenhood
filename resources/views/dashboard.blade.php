<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="px-4 py-4 space-y-3">
        <!-- Welcome -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="text-base font-semibold text-gray-900">
                Welcome back, {{ auth()->user()->name }}!
            </h3>
            <p class="mt-0.5 text-sm text-gray-500">
                Here's a quick overview of your account.
            </p>
        </div>

        <!-- Quick Stats -->
        @php
            $user = auth()->user();
            $activeCount = $user->listings()->whereNull('deleted_at')->count();
            $pendingCount = $user->listings()->where('status', 'pending')->count();
            $limit = $user->getListingLimit();
        @endphp

        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="text-xs font-medium text-gray-500">Active</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $activeCount }}</div>
                <div class="mt-0.5 text-xs text-gray-400">
                    {{ $limit !== null ? 'Limit: ' . $limit : 'Unlimited' }}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="text-xs font-medium text-gray-500">Pending</div>
                <div class="mt-1 text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                <div class="mt-0.5 text-xs text-gray-400">Review</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="text-xs font-medium text-gray-500">Account</div>
                <div class="mt-1 text-sm font-bold text-gray-900">
                    {{ $user->isBusinessUser() ? 'Business' : 'Normal' }}
                </div>
                @if($user->isBusinessUser() && $user->business_valid_until)
                    <div class="mt-0.5 text-xs text-gray-400">
                        Until {{ $user->business_valid_until->format('M d, Y') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('user.listings.create') }}"
                   class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
                    + Create Listing
                </a>
                <a href="{{ route('user.listings.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition">
                    My Listings
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
