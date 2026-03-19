@extends('admin.layouts.app')

@section('title', 'Edit Business Profile: ' . $businessProfile->business_name)
@section('page-title', 'Edit Business Profile')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Business Profile</h2>
            <p class="text-[#37474F] text-sm mt-1">{{ $businessProfile->business_name }}</p>
        </div>
        <a href="{{ route('admin.business-profiles.show', $businessProfile) }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Profile
        </a>
    </div>

    <!-- Owner Info -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-user text-[#D4AF37]"></i>
                Account Owner
            </h3>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4">
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Name</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-medium">{{ $businessProfile->user->name }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium text-[#37474F]">Email</dt>
                <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $businessProfile->user->email }}</dd>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.business-profiles.update', $businessProfile) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-[#D4AF37]"></i>
                    Basic Information
                </h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                <div>
                    <label for="business_name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Business Name <span class="text-red-500">*</span></label>
                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $businessProfile->business_name) }}" required
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('business_name')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="legal_name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Legal Name</label>
                    <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $businessProfile->legal_name) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('legal_name')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="industry" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Industry</label>
                    <input type="text" name="industry" id="industry" value="{{ old('industry', $businessProfile->industry) }}" placeholder="e.g. retail, wholesale, services"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('industry')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="business_type" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Business Type</label>
                    <select name="business_type" id="business_type"
                            class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <option value="">Select type...</option>
                        <option value="sole_proprietor" {{ old('business_type', $businessProfile->business_type) === 'sole_proprietor' ? 'selected' : '' }}>Sole Proprietor</option>
                        <option value="partnership" {{ old('business_type', $businessProfile->business_type) === 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="llc" {{ old('business_type', $businessProfile->business_type) === 'llc' ? 'selected' : '' }}>LLC</option>
                        <option value="corporation" {{ old('business_type', $businessProfile->business_type) === 'corporation' ? 'selected' : '' }}>Corporation</option>
                        <option value="other" {{ old('business_type', $businessProfile->business_type) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('business_type')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2">
                    <label for="description" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]">{{ old('description', $businessProfile->description) }}</textarea>
                    @error('description')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Registration & Tax -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-[#D4AF37]"></i>
                    Registration & Tax
                </h3>
            </div>
            <div class="p-5">
                <p class="text-[12px] text-[#37474F] mb-4">These values will be re-encrypted on save.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="registration_number" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Registration Number</label>
                        <input type="text" name="registration_number" id="registration_number"
                               value="{{ old('registration_number', $sensitiveData['registration_number']) }}"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        @error('registration_number')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="tax_id" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Tax ID (VAT/GST)</label>
                        <input type="text" name="tax_id" id="tax_id"
                               value="{{ old('tax_id', $sensitiveData['tax_id']) }}"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        @error('tax_id')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- User-Submitted Identity Data (read-only) -->
        <div class="bg-white border border-red-200 rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-red-100 bg-red-50 flex items-center justify-between">
                <h3 class="text-[14px] font-semibold text-red-800 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-red-600"></i>
                    User-Submitted Identity Data
                </h3>
                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-red-100 text-red-700 border border-red-300">Read-only</span>
            </div>
            <div class="p-5">
                <p class="text-[12px] text-[#37474F] mb-4">These fields were submitted once by the user and cannot be edited here. Contact the user for corrections.</p>
                <dl class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <dt class="text-[12px] font-medium text-[#37474F]">Full Name (as on ID)</dt>
                        <dd class="mt-0.5 text-[13px] font-mono text-[#1A1A1A]">{{ $sensitiveData['id_full_name'] ?? '— Not provided —' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">ID / Passport Number</dt>
                        <dd class="mt-0.5 text-[13px] font-mono text-[#1A1A1A]">{{ $sensitiveData['id_number'] ?? '— Not provided —' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">FIN (Financial ID Number)</dt>
                        <dd class="mt-0.5 text-[13px] font-mono text-[#1A1A1A]">{{ $sensitiveData['fin'] ?? '— Not provided —' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-[#D4AF37]"></i>
                    Contact Information
                </h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                <div>
                    <label for="business_email" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Business Email</label>
                    <input type="email" name="business_email" id="business_email" value="{{ old('business_email', $businessProfile->business_email) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('business_email')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="business_phone" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Business Phone</label>
                    <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone', $businessProfile->business_phone) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('business_phone')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2">
                    <label for="website" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $businessProfile->website) }}" placeholder="https://"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('website')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Business Address -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-[#D4AF37]"></i>
                    Business Address
                </h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="address_line_1" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Address Line 1</label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $businessProfile->address_line_1) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('address_line_1')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2">
                    <label for="address_line_2" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $businessProfile->address_line_2) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('address_line_2')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="city" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $businessProfile->city) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('city')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="state_province" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">State/Province</label>
                    <input type="text" name="state_province" id="state_province" value="{{ old('state_province', $businessProfile->state_province) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('state_province')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="postal_code" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $businessProfile->postal_code) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('postal_code')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="country_id" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Country</label>
                    <select name="country_id" id="country_id"
                            class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <option value="">Select country...</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $businessProfile->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Branding -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-palette text-[#D4AF37]"></i>
                    Branding
                </h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                <div>
                    <label for="logo" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Logo</label>
                    @if($businessProfile->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $businessProfile->logo) }}" alt="Current logo" class="w-16 h-16 rounded-[6px] object-cover border border-[#E0E0E0]">
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-[12px] text-[#37474F]">Max 2MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                    @error('logo')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="banner" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Banner</label>
                    @if($businessProfile->banner)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $businessProfile->banner) }}" alt="Current banner" class="w-full h-16 rounded-[6px] object-cover border border-[#E0E0E0]">
                        </div>
                    @endif
                    <input type="file" name="banner" id="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-[12px] text-[#37474F]">Max 4MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                    @error('banner')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-gear text-[#D4AF37]"></i>
                    Settings
                </h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                <div>
                    <label for="default_currency" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Default Currency</label>
                    <select name="default_currency" id="default_currency"
                            class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <option value="USD" {{ old('default_currency', $businessProfile->default_currency) === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('default_currency', $businessProfile->default_currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('default_currency', $businessProfile->default_currency) === 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="TRY" {{ old('default_currency', $businessProfile->default_currency) === 'TRY' ? 'selected' : '' }}>TRY</option>
                    </select>
                    @error('default_currency')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="timezone" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Timezone</label>
                    <input type="text" name="timezone" id="timezone" value="{{ old('timezone', $businessProfile->timezone) }}" placeholder="e.g. Europe/Istanbul"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('timezone')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Policies -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-[#D4AF37]"></i>
                    Policies
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label for="return_policy" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Return Policy</label>
                    <textarea name="return_policy" id="return_policy" rows="4"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]">{{ old('return_policy', $businessProfile->return_policy) }}</textarea>
                    @error('return_policy')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="shipping_policy" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Shipping Policy</label>
                    <textarea name="shipping_policy" id="shipping_policy" rows="4"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]">{{ old('shipping_policy', $businessProfile->shipping_policy) }}</textarea>
                    @error('shipping_policy')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.business-profiles.show', $businessProfile) }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                <i class="fa-solid fa-check text-xs"></i>
                Update Business Profile
            </button>
        </div>
    </form>

</div>
@endsection
