@extends('admin.layouts.app')

@section('title', 'Business Profile: ' . $businessProfile->business_name)
@section('page-title', 'Business Profile')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
        <div>
            <a href="{{ route('admin.business-profiles.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Business Profiles
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $businessProfile->business_name }}</h2>
            @if($businessProfile->legal_name && $businessProfile->legal_name !== $businessProfile->business_name)
                <p class="text-[#37474F] text-sm mt-1">{{ $businessProfile->legal_name }}</p>
            @endif
        </div>
        <div class="flex gap-2 items-center flex-wrap">
            @if($businessProfile->isApproved())
                <span class="inline-flex items-center gap-1.5 h-[34px] px-3 bg-[#D4AF37]/20 text-[#D4AF37] rounded text-[13px] font-semibold">
                    <i class="fa-solid fa-circle-check text-xs"></i> Verified
                </span>
                <form action="{{ route('admin.business-profiles.revoke', $businessProfile) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                        <i class="fa-solid fa-ban text-xs"></i>
                        Revoke Badge
                    </button>
                </form>
            @else
                <form action="{{ route('admin.business-profiles.approve', $businessProfile) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        Approve & Verify
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.business-profiles.edit', $businessProfile) }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-pen text-xs"></i>
                Edit Profile
            </a>
            <form action="{{ route('admin.business-profiles.destroy', $businessProfile) }}" method="POST" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this business profile?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Confident Seller Review Panel -->
    @if($businessProfile->confident_seller_status)
    <div x-data="{ rejectOpen: false }" class="mb-5 rounded-[6px] border-2 p-5
        {{ $businessProfile->isConfidentSellerApproved() ? 'border-[#D4AF37] bg-[#D4AF37]/5' : '' }}
        {{ $businessProfile->isConfidentSellerRejected() ? 'border-yellow-400 bg-yellow-50' : '' }}
        {{ $businessProfile->isConfidentSellerPending() ? 'border-[#C0392B] bg-red-50' : '' }}">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2">
                    @if($businessProfile->isConfidentSellerApproved())
                        <span class="inline-flex items-center gap-1.5 h-6 px-3 rounded-[10px] text-[12px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">
                            <i class="fa-solid fa-circle-check text-[11px]"></i> Confident Seller — Approved
                        </span>
                    @elseif($businessProfile->isConfidentSellerRejected())
                        <span class="inline-flex items-center gap-1.5 h-6 px-3 rounded-[10px] text-[12px] font-semibold bg-yellow-100 text-yellow-800">
                            <i class="fa-solid fa-circle-xmark text-[11px]"></i> Confident Seller — Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 h-6 px-3 rounded-[10px] text-[12px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">
                            <i class="fa-solid fa-clock text-[11px]"></i> Confident Seller — Pending Review
                        </span>
                    @endif
                </div>
                @if($businessProfile->isConfidentSellerRejected() && $businessProfile->confident_seller_rejection_reason)
                    <p class="mt-2 text-[13px] text-yellow-800">
                        <span class="font-medium">Rejection reason:</span> {{ $businessProfile->confident_seller_rejection_reason }}
                    </p>
                @endif
            </div>

            <div class="flex gap-2">
                @if(!$businessProfile->isConfidentSellerApproved())
                    <form action="{{ route('admin.business-profiles.confident-seller.approve', $businessProfile) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                            <i class="fa-solid fa-check text-xs"></i> Approve
                        </button>
                    </form>
                @endif
                @if(!$businessProfile->isConfidentSellerRejected())
                    <button type="button" @click="rejectOpen = true"
                            class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600 transition">
                        <i class="fa-solid fa-xmark text-xs"></i> Reject
                    </button>
                @endif
            </div>
        </div>

        @if($businessProfile->isConfidentSellerRejected() && !$businessProfile->isConfidentSellerApproved())
            <p class="mt-3 text-[13px] text-yellow-700">User can reapply by updating their business profile.</p>
        @endif

        <!-- Rejection Modal -->
        <div x-show="rejectOpen" x-cloak style="display:none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="rejectOpen = false"></div>
            <div class="relative bg-white border border-[#E0E0E0] rounded-[6px] shadow-xl max-w-md w-full p-6 z-10" @click.stop>
                <h3 class="text-[15px] font-semibold text-[#1A1A1A] mb-1">Reject Application</h3>
                <p class="text-[13px] text-[#37474F] mb-4">This will notify the user with your reason.</p>
                <form action="{{ route('admin.business-profiles.confident-seller.reject', $businessProfile) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[13px] font-medium text-[#1A1A1A] mb-1.5">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" rows="4" required
                                  class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]"
                                  placeholder="Explain why this application is being rejected…"></textarea>
                        @error('rejection_reason')
                            <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="rejectOpen = false"
                                class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600 transition">
                            Submit Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Branding -->
            @if($businessProfile->banner || $businessProfile->logo)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                @if($businessProfile->banner)
                    <div class="h-40 bg-gray-100">
                        <img src="{{ asset('storage/' . $businessProfile->banner) }}" alt="Banner" class="w-full h-full object-cover">
                    </div>
                @endif
                @if($businessProfile->logo)
                    <div class="px-5 py-4 {{ $businessProfile->banner ? '-mt-10' : '' }}">
                        <img src="{{ asset('storage/' . $businessProfile->logo) }}" alt="Logo"
                             class="w-20 h-20 rounded-[6px] object-cover border-4 border-white shadow-[0_1px_4px_rgba(0,0,0,0.15)]">
                    </div>
                @endif
            </div>
            @endif

            <!-- Business Information -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-[#D4AF37]"></i>
                        Business Information
                    </h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Business Name</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->business_name }}</dd>
                        </div>
                        @if($businessProfile->legal_name)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Legal Name</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->legal_name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Slug</dt>
                            <dd class="mt-0.5"><code class="bg-gray-100 px-2 py-0.5 rounded text-[12px] text-[#37474F]">{{ $businessProfile->slug }}</code></dd>
                        </div>
                        @if($businessProfile->industry)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Industry</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ ucfirst($businessProfile->industry) }}</dd>
                        </div>
                        @endif
                        @if($businessProfile->business_type)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Business Type</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ str_replace('_', ' ', ucfirst($businessProfile->business_type)) }}</dd>
                        </div>
                        @endif
                    </dl>
                    @if($businessProfile->description)
                    <div class="mt-4 pt-4 border-t border-[#E0E0E0]">
                        <dt class="text-[12px] font-medium text-[#37474F] mb-1">Description</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{!! nl2br(e($businessProfile->description)) !!}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sensitive Identity & Tax Data -->
            <div class="bg-white border border-red-200 rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-red-100 bg-red-50 flex items-center justify-between">
                    <h3 class="text-[14px] font-semibold text-red-800 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-red-600"></i>
                        Sensitive Identity & Tax Data
                    </h3>
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-red-100 text-red-700 border border-red-300">Confidential</span>
                </div>
                <div class="p-5">
                    <p class="text-[12px] text-[#37474F] mb-4">These fields are stored encrypted (AES-256-GCM) and are only visible to administrators.</p>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <dt class="text-[12px] font-medium text-[#37474F]">Full Name (as on ID)</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-mono">{{ $sensitiveData['id_full_name'] ?? '— Not provided —' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">ID / Passport Number</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-mono">{{ $sensitiveData['id_number'] ?? '— Not provided —' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">FIN (Financial ID Number)</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-mono">{{ $sensitiveData['fin'] ?? '— Not provided —' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Registration Number</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-mono">{{ $sensitiveData['registration_number'] ?? '— Not provided —' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Tax ID (VAT/GST)</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-mono">{{ $sensitiveData['tax_id'] ?? '— Not provided —' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Contact Information -->
            @if($businessProfile->business_email || $businessProfile->business_phone || $businessProfile->website)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-[#D4AF37]"></i>
                        Contact Information
                    </h3>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($businessProfile->business_email)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Email</dt>
                            <dd class="mt-0.5 text-[13px]">
                                <a href="mailto:{{ $businessProfile->business_email }}" class="text-[#D4AF37] hover:underline">{{ $businessProfile->business_email }}</a>
                            </dd>
                        </div>
                        @endif
                        @if($businessProfile->business_phone)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Phone</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->business_phone }}</dd>
                        </div>
                        @endif
                        @if($businessProfile->website)
                        <div class="sm:col-span-2">
                            <dt class="text-[12px] font-medium text-[#37474F]">Website</dt>
                            <dd class="mt-0.5 text-[13px]">
                                <a href="{{ $businessProfile->website }}" target="_blank" class="text-[#D4AF37] hover:underline">{{ $businessProfile->website }}</a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Policies -->
            @if($businessProfile->return_policy || $businessProfile->shipping_policy)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-[#D4AF37]"></i>
                        Policies
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @if($businessProfile->return_policy)
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F] mb-1">Return Policy</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{!! nl2br(e($businessProfile->return_policy)) !!}</dd>
                    </div>
                    @endif
                    @if($businessProfile->shipping_policy)
                    <div class="pt-4 border-t border-[#E0E0E0]">
                        <dt class="text-[12px] font-medium text-[#37474F] mb-1">Shipping Policy</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{!! nl2br(e($businessProfile->shipping_policy)) !!}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Account Owner -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-user text-[#D4AF37]"></i>
                        Account Owner
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Name</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-medium">{{ $businessProfile->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Email</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->user->email }}</dd>
                    </div>
                    <div class="pt-3 border-t border-[#E0E0E0]">
                        <a href="{{ route('admin.users.edit', $businessProfile->user) }}"
                           class="text-[13px] text-[#D4AF37] hover:underline inline-flex items-center gap-1">
                            View User Profile <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Address -->
            @if($businessProfile->address_line_1 || $businessProfile->city || $businessProfile->country)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[#D4AF37]"></i>
                        Address
                    </h3>
                </div>
                <div class="p-4 text-[13px] text-[#1A1A1A] leading-relaxed">
                    @if($businessProfile->address_line_1){{ $businessProfile->address_line_1 }}<br>@endif
                    @if($businessProfile->address_line_2){{ $businessProfile->address_line_2 }}<br>@endif
                    @if($businessProfile->city || $businessProfile->state_province || $businessProfile->postal_code)
                        {{ $businessProfile->city }}{{ $businessProfile->city && $businessProfile->state_province ? ', ' : '' }}{{ $businessProfile->state_province }} {{ $businessProfile->postal_code }}<br>
                    @endif
                    @if($businessProfile->country){{ $businessProfile->country->name }}@endif
                </div>
            </div>
            @endif

            <!-- Settings -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-gear text-[#D4AF37]"></i>
                        Settings
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">Default Currency</span>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">{{ $businessProfile->default_currency }}</span>
                    </div>
                    @if($businessProfile->timezone)
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">Timezone</span>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">{{ $businessProfile->timezone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-clock text-[#D4AF37]"></i>
                        Timestamps
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Created</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Updated</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Verified</dt>
                        <dd class="mt-0.5 text-[13px]">
                            @if($businessProfile->isApproved())
                                <span class="text-[#D4AF37] font-medium">{{ $businessProfile->approved_at->format('M d, Y H:i') }}</span>
                            @else
                                <span class="text-[#37474F]">Not verified</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
