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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <div class="grid grid-cols-3 gap-3">
                        {{-- Normal User --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="normal_user" x-model="role" class="sr-only peer">
                            <div x-bind:class="role === 'normal_user' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-gray-300'"
                                 class="rounded-lg border-2 p-4 text-center transition select-none">
                                <svg class="w-7 h-7 mx-auto mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="block text-sm font-medium text-gray-800">Normal User</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Standard account</span>
                            </div>
                        </label>

                        {{-- Business User --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="business_user" x-model="role" class="sr-only peer">
                            <div x-bind:class="role === 'business_user' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200 hover:border-gray-300'"
                                 class="rounded-lg border-2 p-4 text-center transition select-none">
                                <svg class="w-7 h-7 mx-auto mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="block text-sm font-medium text-gray-800">Business User</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Enables business features</span>
                            </div>
                        </label>

                        {{-- Admin --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="admin" x-model="role" class="sr-only peer">
                            <div x-bind:class="role === 'admin' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-200 hover:border-gray-300'"
                                 class="rounded-lg border-2 p-4 text-center transition select-none">
                                <svg class="w-7 h-7 mx-auto mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span class="block text-sm font-medium text-gray-800">Admin</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Full platform access</span>
                            </div>
                        </label>
                    </div>
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
