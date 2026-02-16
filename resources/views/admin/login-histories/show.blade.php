@extends('admin.layouts.app')

@section('title', 'Login Details')
@section('page-title', 'Login Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Login Details</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Detailed information about this login event</p>
        </div>
        <a href="{{ route('admin.login-histories.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
            &larr; Back to Histories
        </a>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                @if($loginHistory->user)
                    <div class="h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-300 font-bold text-2xl">
                        {{ substr($loginHistory->user->name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $loginHistory->user->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ $loginHistory->user->email }}</p>
                        <a href="{{ route('admin.login-histories.user', $loginHistory->user) }}"
                           class="text-primary-600 dark:text-primary-300 text-sm hover:underline">
                            View all logins for this user
                        </a>
                    </div>
                @else
                    <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 font-bold text-2xl">?</div>
                    <div class="ml-4">
                        <h3 class="text-xl font-semibold text-gray-500 dark:text-gray-400">Deleted User</h3>
                    </div>
                @endif
            </div>
            @if($loginHistory->is_suspicious)
                <span class="px-4 py-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded-full font-medium">
                    Suspicious Login
                </span>
            @else
                <span class="px-4 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full font-medium">
                    Normal Login
                </span>
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Connection Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Connection Information</h4>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">IP Address</dt>
                    <dd class="text-gray-900 dark:text-white font-mono">{{ $loginHistory->ip_address }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Country</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->country ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">City</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->city ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Login Time</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->logged_in_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Device Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Device Information</h4>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Device</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->device ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Browser</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->browser ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Platform</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $loginHistory->platform ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- User Agent -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Agent</h4>
        <code class="block p-4 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-800 dark:text-gray-200 break-all">
            {{ $loginHistory->user_agent ?? 'Not available' }}
        </code>
    </div>

    <!-- Same IP Logins -->
    @if($sameIpLogins->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Other Logins from {{ $loginHistory->ip_address }}
        </h4>
        <div class="space-y-3">
            @foreach($sameIpLogins as $login)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-300 font-semibold text-sm">
                            {{ $login->user ? substr($login->user->name, 0, 1) : '?' }}
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $login->user?->name ?? 'Deleted User' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $login->logged_in_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @if($login->is_suspicious)
                        <span class="px-2 py-1 text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded">Suspicious</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- User Recent Logins -->
    @if($userRecentLogins->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Recent Logins by {{ $loginHistory->user?->name ?? 'this User' }}
        </h4>
        <div class="space-y-3">
            @foreach($userRecentLogins as $login)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <code class="text-sm text-gray-800 dark:text-gray-200">{{ $login->ip_address }}</code>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $login->device }} / {{ $login->browser }} / {{ $login->platform }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $login->logged_in_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $login->logged_in_at->format('h:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h4>
        <form action="{{ route('admin.login-histories.block-ip') }}" method="POST" class="flex items-center gap-4"
              onsubmit="return confirm('Are you sure you want to flag this IP address?')">
            @csrf
            <input type="hidden" name="ip_address" value="{{ $loginHistory->ip_address }}">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Flag IP Address
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                This will log the action. Configure firewall rules separately.
            </span>
        </form>
    </div>
</div>
@endsection
