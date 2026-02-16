<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Welcome back, {{ auth()->user()->name }}!
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Here's a quick overview of your account.
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @php
                    $user = auth()->user();
                    $activeCount = $user->listings()->whereNull('deleted_at')->count();
                    $pendingCount = $user->listings()->where('status', 'pending')->count();
                    $limit = $user->getListingLimit();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Listings</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $activeCount }}</div>
                    @if($limit !== null)
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Limit: {{ $limit }}</div>
                    @else
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unlimited</div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Review</div>
                    <div class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</div>
                    <div class="mt-2 text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $user->isBusinessUser() ? 'Business' : 'Normal' }}
                    </div>
                    @if($user->isBusinessUser() && $user->business_valid_until)
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Valid until {{ $user->business_valid_until->format('M d, Y') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.listings.create') }}"
                       class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
                        + Create Listing
                    </a>
                    <a href="{{ route('user.listings.index') }}"
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-sm font-medium transition">
                        My Listings
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 text-sm font-medium transition">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
