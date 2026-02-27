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
            <div class="flex gap-2 items-center flex-wrap">
                @if($businessProfile->isApproved())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verified
                    </span>
                    <form action="{{ route('admin.business-profiles.revoke', $businessProfile) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Revoke Badge
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.business-profiles.approve', $businessProfile) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Approve &amp; Verify
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.business-profiles.edit', $businessProfile) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600">
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

        {{-- Confident Seller Review Panel --}}
        @if($businessProfile->confident_seller_status)
        <div x-data="{ rejectOpen: false }" class="mb-6 rounded-lg border-2 p-5
            {{ $businessProfile->isConfidentSellerApproved() ? 'border-green-400 bg-green-50' : '' }}
            {{ $businessProfile->isConfidentSellerRejected() ? 'border-yellow-400 bg-yellow-50' : '' }}
            {{ $businessProfile->isConfidentSellerPending() ? 'border-red-400 bg-red-50' : '' }}">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        @if($businessProfile->isConfidentSellerApproved())
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">&#10003; Confident Seller &mdash; Approved</span>
                        @elseif($businessProfile->isConfidentSellerRejected())
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">&#10007; Confident Seller &mdash; Rejected</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">&#9679; Confident Seller &mdash; Pending Review</span>
                        @endif
                    </div>
                    @if($businessProfile->isConfidentSellerRejected() && $businessProfile->confident_seller_rejection_reason)
                        <p class="mt-2 text-sm text-yellow-800">
                            <span class="font-medium">Rejection reason:</span> {{ $businessProfile->confident_seller_rejection_reason }}
                        </p>
                    @endif
                </div>

                <div class="flex gap-2">
                    @if(! $businessProfile->isConfidentSellerApproved())
                        <form action="{{ route('admin.business-profiles.confident-seller.approve', $businessProfile) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-md uppercase tracking-widest hover:bg-green-700 transition">
                                Approve
                            </button>
                        </form>
                    @endif
                    @if(! $businessProfile->isConfidentSellerRejected())
                        <button type="button" @click="rejectOpen = true"
                                class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-xs font-semibold rounded-md uppercase tracking-widest hover:bg-yellow-600 transition">
                            Reject
                        </button>
                    @endif
                </div>
            </div>

            @if($businessProfile->isConfidentSellerRejected() && ! $businessProfile->isConfidentSellerApproved())
                <div class="mt-3 text-sm text-yellow-700">
                    User can reapply by updating their business profile.
                </div>
            @endif

            {{-- Rejection Modal --}}
            <div x-show="rejectOpen" x-cloak style="display:none"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="rejectOpen = false"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 z-10" @click.stop>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Reject Application</h3>
                    <p class="text-sm text-gray-500 mb-4">This will notify the user with your reason.</p>
                    <form action="{{ route('admin.business-profiles.confident-seller.reject', $businessProfile) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" rows="4" required
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                      placeholder="Explain why this application is being rejected…"></textarea>
                            @error('rejection_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="rejectOpen = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition">
                                Submit Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

                <!-- Sensitive Identity & Tax Data -->
                <div class="bg-white shadow rounded-lg overflow-hidden border border-red-200">
                    <div class="px-6 py-4 border-b border-red-100 bg-red-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-red-800">Sensitive Identity & Tax Data</h3>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 border border-red-300">Confidential</span>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-xs text-gray-500 mb-4">These fields are stored encrypted (AES-256-GCM) and are only visible to administrators.</p>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Full Name (as on ID)</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $sensitiveData['id_full_name'] ?? '— Not provided —' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID / Passport Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $sensitiveData['id_number'] ?? '— Not provided —' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">FIN (Financial ID Number)</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $sensitiveData['fin'] ?? '— Not provided —' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $sensitiveData['registration_number'] ?? '— Not provided —' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tax ID (VAT/GST)</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $sensitiveData['tax_id'] ?? '— Not provided —' }}
                                </dd>
                            </div>
                        </dl>
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
                        <div>
                            <dt class="font-medium text-gray-500">Verified</dt>
                            <dd class="mt-1 text-gray-900">
                                @if($businessProfile->isApproved())
                                    <span class="text-blue-700 font-medium">{{ $businessProfile->approved_at->format('M d, Y H:i') }}</span>
                                @else
                                    <span class="text-gray-400">Not verified</span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
