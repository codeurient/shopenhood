@extends('admin.layouts.app')

@section('title', 'Business Profile: ' . $businessProfile->business_name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.business-profiles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
                    &larr; Back to Business Profiles
                </a>
                <h2 class="text-2xl font-bold text-gray-900">{{ $businessProfile->business_name }}</h2>
                @if($businessProfile->legal_name && $businessProfile->legal_name !== $businessProfile->business_name)
                    <p class="text-sm text-gray-500 mt-1">{{ $businessProfile->legal_name }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.business-profiles.edit', $businessProfile) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Edit Profile
                </a>
                <form action="{{ route('admin.business-profiles.destroy', $businessProfile) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this business profile?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Branding -->
                @if($businessProfile->banner || $businessProfile->logo)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    @if($businessProfile->banner)
                        <div class="h-48 bg-gray-100">
                            <img src="{{ asset('storage/' . $businessProfile->banner) }}" alt="Banner" class="w-full h-full object-cover">
                        </div>
                    @endif
                    @if($businessProfile->logo)
                        <div class="px-6 py-4 {{ $businessProfile->banner ? '-mt-12' : '' }}">
                            <img src="{{ asset('storage/' . $businessProfile->logo) }}" alt="Logo" class="w-24 h-24 rounded-lg object-cover border-4 border-white shadow">
                        </div>
                    @endif
                </div>
                @endif

                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Business Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->business_name }}</dd>
                            </div>
                            @if($businessProfile->legal_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Legal Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->legal_name }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->slug }}</dd>
                            </div>
                            @if($businessProfile->industry)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Industry</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($businessProfile->industry) }}</dd>
                            </div>
                            @endif
                            @if($businessProfile->business_type)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ str_replace('_', ' ', ucfirst($businessProfile->business_type)) }}</dd>
                            </div>
                            @endif
                            @if($businessProfile->registration_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->registration_number }}</dd>
                            </div>
                            @endif
                            @if($businessProfile->tax_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tax ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->tax_id }}</dd>
                            </div>
                            @endif
                        </dl>

                        @if($businessProfile->description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                            <dd class="text-sm text-gray-900 prose prose-sm max-w-none">
                                {!! nl2br(e($businessProfile->description)) !!}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Contact Information -->
                @if($businessProfile->business_email || $businessProfile->business_phone || $businessProfile->website)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            @if($businessProfile->business_email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $businessProfile->business_email }}" class="text-blue-600 hover:underline">{{ $businessProfile->business_email }}</a>
                                </dd>
                            </div>
                            @endif
                            @if($businessProfile->business_phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->business_phone }}</dd>
                            </div>
                            @endif
                            @if($businessProfile->website)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Website</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ $businessProfile->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $businessProfile->website }}</a>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                @endif

                <!-- Policies -->
                @if($businessProfile->return_policy || $businessProfile->shipping_policy)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Policies</h3>
                    </div>
                    <div class="px-6 py-4 space-y-6">
                        @if($businessProfile->return_policy)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Return Policy</dt>
                            <dd class="text-sm text-gray-900 prose prose-sm max-w-none">
                                {!! nl2br(e($businessProfile->return_policy)) !!}
                            </dd>
                        </div>
                        @endif
                        @if($businessProfile->shipping_policy)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Shipping Policy</dt>
                            <dd class="text-sm text-gray-900 prose prose-sm max-w-none">
                                {!! nl2br(e($businessProfile->shipping_policy)) !!}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Owner Info -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Owner</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->email }}</dd>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('admin.users.edit', $businessProfile->user) }}" class="text-sm text-blue-600 hover:underline">View User Profile &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                @if($businessProfile->address_line_1 || $businessProfile->city || $businessProfile->country)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Address</h3>
                    </div>
                    <div class="px-6 py-4 text-sm text-gray-900">
                        @if($businessProfile->address_line_1)
                            {{ $businessProfile->address_line_1 }}<br>
                        @endif
                        @if($businessProfile->address_line_2)
                            {{ $businessProfile->address_line_2 }}<br>
                        @endif
                        @if($businessProfile->city || $businessProfile->state_province || $businessProfile->postal_code)
                            {{ $businessProfile->city }}{{ $businessProfile->city && $businessProfile->state_province ? ', ' : '' }}{{ $businessProfile->state_province }} {{ $businessProfile->postal_code }}<br>
                        @endif
                        @if($businessProfile->country)
                            {{ $businessProfile->country->name }}
                        @endif
                    </div>
                </div>
                @endif

                <!-- Settings -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Settings</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Default Currency</span>
                            <span class="text-sm font-medium text-gray-900">{{ $businessProfile->default_currency }}</span>
                        </div>
                        @if($businessProfile->timezone)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Timezone</span>
                            <span class="text-sm font-medium text-gray-900">{{ $businessProfile->timezone }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Timestamps</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-gray-900">{{ $businessProfile->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Updated</dt>
                            <dd class="mt-1 text-gray-900">{{ $businessProfile->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
