@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div>
    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Users</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Normal Users</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['normal'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Business Users</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['business'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Admins</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['admin'] }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="normal_user" {{ request('role') === 'normal_user' ? 'selected' : '' }}>Normal User</option>
                    <option value="business_user" {{ request('role') === 'business_user' ? 'selected' : '' }}>Business User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Listings</th>
                    <th class="px-4 py-3">Business Until</th>
                    <th class="px-4 py-3">Joined</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($user->current_role === 'admin')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                        @elseif($user->current_role === 'business_user')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Business</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Normal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($user->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @elseif($user->status === 'suspended')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Suspended</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Banned</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $user->listings_count }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($user->business_valid_until)
                            {{ $user->business_valid_until->format('M d, Y') }}
                            @if($user->business_valid_until->isPast())
                                <span class="text-red-500">(Expired)</span>
                            @endif
                        @endif
                        @if($user->businessProfile)
                            @php $cs = $user->businessProfile->confident_seller_status; @endphp
                            <div class="mt-1">
                                @if($cs === 'approved')
                                    <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200 transition">
                                        ✓ Confident Seller
                                    </a>
                                @elseif($cs === 'rejected')
                                    <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">
                                        ✗ Confident Seller
                                    </a>
                                @elseif($cs === 'pending')
                                    <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 hover:bg-red-200 transition">
                                        ● Confident Seller
                                    </a>
                                @else
                                    <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                       class="text-xs text-blue-600 hover:underline">View profile</a>
                                @endif
                            </div>
                        @elseif(!$user->business_valid_until)
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                            @if($user->id !== auth()->guard('admin')->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
