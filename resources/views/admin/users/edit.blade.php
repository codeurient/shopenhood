@extends('admin.layouts.app')

@section('title', 'Edit User: ' . $user->name)
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-3xl">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">&larr; Back to Users</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- User Info --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">User Information</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Name:</span>
                <span class="font-medium ml-2">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-gray-500">Email:</span>
                <span class="font-medium ml-2">{{ $user->email }}</span>
            </div>
            <div>
                <span class="text-gray-500">Joined:</span>
                <span class="font-medium ml-2">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Active Listings:</span>
                <span class="font-medium ml-2">{{ $user->listings_count }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Role & Business Settings --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="{ role: '{{ old('current_role', $user->current_role) }}' }">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Role & Business Settings</h2>

            <div class="space-y-4">
                <div>
                    <label for="current_role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="current_role" id="current_role" x-model="role"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="normal_user">Normal User</option>
                        <option value="business_user">Business User</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('current_role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($user->current_role === 'business_user' && $user->listings_count > 1)
                <div x-show="role === 'normal_user'" x-transition>
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-800">
                        This user has {{ $user->listings_count }} listings. Changing to Normal User will hide all but the most recent listing from public view. The hidden listings will be restored if the role is changed back to Business User.
                    </div>
                </div>
                @endif

                <div x-show="role === 'business_user'" x-transition>
                    <div class="p-4 bg-gray-50 rounded-lg space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_business_enabled" id="is_business_enabled" value="1"
                                   {{ old('is_business_enabled', $user->is_business_enabled) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_business_enabled" class="text-sm font-medium text-gray-700">Business Enabled</label>
                        </div>

                        <div>
                            <label for="listing_limit" class="block text-sm font-medium text-gray-700 mb-1">
                                Listing Limit <span class="text-gray-400">(leave empty for unlimited)</span>
                            </label>
                            <input type="number" name="listing_limit" id="listing_limit"
                                   value="{{ old('listing_limit', $user->listing_limit) }}"
                                   min="1" placeholder="Unlimited"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('listing_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="business_valid_until" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Valid Until <span class="text-gray-400">(leave empty for no expiry)</span>
                            </label>
                            <input type="date" name="business_valid_until" id="business_valid_until"
                                   value="{{ old('business_valid_until', $user->business_valid_until?->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_valid_until')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Account Status</h2>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Update User</button>
        </div>
    </form>
</div>
@endsection
