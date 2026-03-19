<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Set Up Your Business Profile</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Complete your business profile to start selling</p>
        </div>
    </div>

    {{-- ── Flash Messages ────────────────────────────────────────────────── --}}
    @if(session('info'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-info flex-shrink-0 mt-0.5"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── Validation Errors ────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('business.profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-5 max-w-3xl">

            {{-- ── Business Information ─────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-briefcase text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Business Information</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Business Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}" required
                                   placeholder="Your business name"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('business_name') border-red-400 @enderror">
                            @error('business_name')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Legal Name</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name') }}"
                                   placeholder="Registered legal name"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('legal_name') border-red-400 @enderror">
                            @error('legal_name')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Industry</label>
                            <input type="text" name="industry" value="{{ old('industry') }}"
                                   placeholder="e.g. retail, wholesale, services"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('industry') border-red-400 @enderror">
                            @error('industry')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Business Type</label>
                            <select name="business_type"
                                    class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('business_type') border-red-400 @enderror">
                                <option value="">— Select type —</option>
                                <option value="sole_proprietor" {{ old('business_type') === 'sole_proprietor' ? 'selected' : '' }}>Sole Proprietor</option>
                                <option value="partnership"     {{ old('business_type') === 'partnership'     ? 'selected' : '' }}>Partnership</option>
                                <option value="llc"             {{ old('business_type') === 'llc'             ? 'selected' : '' }}>LLC</option>
                                <option value="corporation"     {{ old('business_type') === 'corporation'     ? 'selected' : '' }}>Corporation</option>
                                <option value="other"           {{ old('business_type') === 'other'           ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Description</label>
                            <textarea name="description" rows="3"
                                      placeholder="Tell customers about your business..."
                                      class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Contact Information ──────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-address-book text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Contact Information</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Business Email</label>
                            <input type="email" name="business_email" value="{{ old('business_email') }}"
                                   placeholder="contact@yourbusiness.com"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('business_email') border-red-400 @enderror">
                            @error('business_email')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Business Phone</label>
                            <input type="text" name="business_phone" value="{{ old('business_phone') }}"
                                   placeholder="+1 234 567 8900"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('business_phone') border-red-400 @enderror">
                            @error('business_phone')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}"
                                   placeholder="https://yourbusiness.com"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('website') border-red-400 @enderror">
                            @error('website')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Business Address ─────────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-location-dot text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Business Address</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Address Line 1</label>
                            <input type="text" name="address_line_1" value="{{ old('address_line_1') }}"
                                   placeholder="Street name and number"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('address_line_1') border-red-400 @enderror">
                            @error('address_line_1')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Address Line 2</label>
                            <input type="text" name="address_line_2" value="{{ old('address_line_2') }}"
                                   placeholder="Suite, floor, building, etc."
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('address_line_2') border-red-400 @enderror">
                            @error('address_line_2')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   placeholder="City"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('city') border-red-400 @enderror">
                            @error('city')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">State / Province</label>
                            <input type="text" name="state_province" value="{{ old('state_province') }}"
                                   placeholder="State or province"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('state_province') border-red-400 @enderror">
                            @error('state_province')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                   placeholder="ZIP / postal code"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('postal_code') border-red-400 @enderror">
                            @error('postal_code')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Country</label>
                            <select name="country_id"
                                    class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('country_id') border-red-400 @enderror">
                                <option value="">— Select Country —</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Identity & Tax Verification ─────────────────────────── --}}
            <div class="bg-white border border-[#D4AF37]/40 rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-shield-halved text-[#D4AF37] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Identity & Tax Verification</h2>
                </div>

                {{-- Notice banner --}}
                <div class="mx-5 mt-4 flex items-start gap-3 px-4 py-3 bg-[#D4AF37]/8 border border-[#D4AF37]/30 rounded text-[13px] text-[#37474F]">
                    <i class="fa-solid fa-lock text-[#D4AF37] flex-shrink-0 mt-0.5"></i>
                    <span>
                        This information is <strong class="text-[#1A1A1A]">encrypted</strong> and only visible to administrators for verification purposes.
                        It cannot be changed after submission — contact support if a correction is needed.
                    </span>
                </div>

                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Registration Number</label>
                            <input type="text" name="registration_number" value="{{ old('registration_number') }}"
                                   placeholder="Business registration number"
                                   autocomplete="off"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('registration_number') border-red-400 @enderror">
                            @error('registration_number')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Tax ID (VAT / GST)</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id') }}"
                                   placeholder="VAT or GST number"
                                   autocomplete="off"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('tax_id') border-red-400 @enderror">
                            @error('tax_id')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Full Name (as on ID card)</label>
                            <input type="text" name="id_full_name" value="{{ old('id_full_name') }}"
                                   placeholder="Exactly as it appears on your official ID"
                                   autocomplete="off"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('id_full_name') border-red-400 @enderror">
                            @error('id_full_name')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">ID / Passport Number</label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}"
                                   placeholder="National ID or passport number"
                                   autocomplete="off"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('id_number') border-red-400 @enderror">
                            @error('id_number')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">FIN (Financial Identification Number)</label>
                            <input type="text" name="fin" value="{{ old('fin') }}"
                                   autocomplete="off"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('fin') border-red-400 @enderror">
                            @error('fin')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Actions ──────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                    <i class="fa-solid fa-forward-step text-xs"></i>
                    Skip for Now
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Create Business Profile
                </button>
            </div>

        </div>
    </form>

</x-app-layout>
