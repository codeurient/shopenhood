@extends('admin.layouts.app')

@section('title', 'Edit User: ' . $user->name)
@section('page-title', 'Edit User')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit User</h2>
            <p class="text-[#37474F] text-sm mt-1">{{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Users
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- User Info -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                User Information
            </h3>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4">
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Name</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-medium">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Email</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Joined</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $user->created_at->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Active Listings</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-medium">{{ $user->listings_count }}</dd>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Role & Business Settings -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5"
             x-data="{ role: '{{ old('current_role', $user->current_role) }}' }">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-[#D4AF37]"></i>
                    Role & Business Settings
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <!-- Role Selector -->
                <div>
                    <label class="block mb-2 text-[13px] font-medium text-[#1A1A1A]">Role</label>
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Normal User -->
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="normal_user" x-model="role" class="sr-only">
                            <div :class="role === 'normal_user' ? 'border-[#D4AF37] bg-[#D4AF37]/5' : 'border-[#E0E0E0] hover:border-[#37474F]'"
                                 class="rounded border-2 p-4 text-center transition select-none">
                                <i class="fa-solid fa-user text-xl mx-auto mb-2 block"
                                   :class="role === 'normal_user' ? 'text-[#D4AF37]' : 'text-[#37474F]'"></i>
                                <span class="block text-[13px] font-semibold text-[#1A1A1A]">Normal User</span>
                                <span class="block text-[12px] text-[#37474F] mt-0.5">Standard account</span>
                            </div>
                        </label>

                        <!-- Business User -->
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="business_user" x-model="role" class="sr-only">
                            <div :class="role === 'business_user' ? 'border-[#D4AF37] bg-[#D4AF37]/5' : 'border-[#E0E0E0] hover:border-[#37474F]'"
                                 class="rounded border-2 p-4 text-center transition select-none">
                                <i class="fa-solid fa-briefcase text-xl mx-auto mb-2 block"
                                   :class="role === 'business_user' ? 'text-[#D4AF37]' : 'text-[#37474F]'"></i>
                                <span class="block text-[13px] font-semibold text-[#1A1A1A]">Business User</span>
                                <span class="block text-[12px] text-[#37474F] mt-0.5">Enables business features</span>
                            </div>
                        </label>

                        <!-- Admin -->
                        <label class="cursor-pointer">
                            <input type="radio" name="current_role" value="admin" x-model="role" class="sr-only">
                            <div :class="role === 'admin' ? 'border-[#D4AF37] bg-[#D4AF37]/5' : 'border-[#E0E0E0] hover:border-[#37474F]'"
                                 class="rounded border-2 p-4 text-center transition select-none">
                                <i class="fa-solid fa-shield-halved text-xl mx-auto mb-2 block"
                                   :class="role === 'admin' ? 'text-[#D4AF37]' : 'text-[#37474F]'"></i>
                                <span class="block text-[13px] font-semibold text-[#1A1A1A]">Admin</span>
                                <span class="block text-[12px] text-[#37474F] mt-0.5">Full platform access</span>
                            </div>
                        </label>
                    </div>
                    @error('current_role')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Downgrade warning -->
                @if($user->current_role === 'business_user' && $user->listings_count > 1)
                <div x-show="role === 'normal_user'" x-transition>
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-[13px] text-yellow-800">
                            This user has {{ $user->listings_count }} listings. Changing to Normal User will hide all but the most recent listing from public view. Hidden listings will be restored if the role is changed back to Business User.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Business settings panel -->
                <div x-show="role === 'business_user'" x-transition>
                    <div class="bg-gray-50 border border-[#E0E0E0] rounded p-4 space-y-4">

                        <!-- Business Enabled toggle -->
                        <div class="flex items-start gap-4">
                            <label for="is_business_enabled" class="inline-flex items-center gap-3 cursor-pointer mt-0.5">
                                <input type="checkbox" name="is_business_enabled" id="is_business_enabled" value="1"
                                       {{ old('is_business_enabled', $user->is_business_enabled) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                            </label>
                            <div>
                                <p class="text-[13px] font-medium text-[#1A1A1A]">Business Enabled</p>
                            </div>
                        </div>

                        <!-- Listing Limit -->
                        <div>
                            <label for="listing_limit" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Listing Limit <span class="text-[12px] font-normal text-[#37474F]">(leave empty for unlimited)</span>
                            </label>
                            <input type="number" name="listing_limit" id="listing_limit"
                                   value="{{ old('listing_limit', $user->listing_limit) }}"
                                   min="1" placeholder="Unlimited"
                                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                            @error('listing_limit')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Valid Until -->
                        <div>
                            <label for="business_valid_until" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Business Valid Until <span class="text-[12px] font-normal text-[#37474F]">(leave empty for no expiry)</span>
                            </label>
                            <input type="date" name="business_valid_until" id="business_valid_until"
                                   value="{{ old('business_valid_until', $user->business_valid_until?->format('Y-m-d')) }}"
                                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                            @error('business_valid_until')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Account Status -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-circle-dot text-[#D4AF37]"></i>
                    Account Status
                </h3>
            </div>
            <div class="p-5">
                <label for="status" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Status</label>
                <select name="status" id="status"
                        class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                @error('status')
                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                <i class="fa-solid fa-check text-xs"></i>
                Update User
            </button>
        </div>
    </form>

</div>
@endsection
