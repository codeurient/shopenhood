<x-guest-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Set Up Your Business Profile</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Complete your business profile to start selling</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 px-4 sm:px-0">
                <h1 class="text-xl font-semibold text-gray-800">Set Up Your Business Profile</h1>
                <p class="text-sm text-gray-500 mt-1">Complete your business profile to start selling</p>
            </div>
            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-800 dark:text-blue-200 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('business.profile.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Business Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Business Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="legal_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Legal Name</label>
                            <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('legal_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Industry</label>
                            <input type="text" name="industry" id="industry" value="{{ old('industry') }}" placeholder="e.g. retail, wholesale, services"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('industry')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Type</label>
                            <select name="business_type" id="business_type"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select type...</option>
                                <option value="sole_proprietor" {{ old('business_type') === 'sole_proprietor' ? 'selected' : '' }}>Sole Proprietor</option>
                                <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="llc" {{ old('business_type') === 'llc' ? 'selected' : '' }}>LLC</option>
                                <option value="corporation" {{ old('business_type') === 'corporation' ? 'selected' : '' }}>Corporation</option>
                                <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('business_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3" placeholder="Tell customers about your business..."
                                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Contact Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="business_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Email</label>
                            <input type="email" name="business_email" id="business_email" value="{{ old('business_email') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="business_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Phone</label>
                            <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone') }}" placeholder="+1234567890"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                            <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Business Address</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Line 1</label>
                            <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('address_line_1')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Line 2</label>
                            <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('address_line_2')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State/Province</label>
                            <input type="text" name="state_province" id="state_province" value="{{ old('state_province') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('state_province')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                            <select name="country_id" id="country_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select country...</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Identity & Tax Verification -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Identity & Tax Verification</h3>
                    </div>

                    <div class="mb-4 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg text-sm text-amber-800 dark:text-amber-300">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>This information is <strong>encrypted</strong> and only visible to administrators for verification purposes. It cannot be changed after submission — contact support if a correction is needed.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" placeholder="Business registration number"
                                   autocomplete="off"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('registration_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax ID (VAT/GST)</label>
                            <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" placeholder="VAT or GST number"
                                   autocomplete="off"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('tax_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="id_full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name (as on ID card)</label>
                            <input type="text" name="id_full_name" id="id_full_name" value="{{ old('id_full_name') }}" placeholder="Exactly as it appears on your official ID"
                                   autocomplete="off"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('id_full_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID / Passport Number</label>
                            <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" placeholder="National ID or passport number"
                                   autocomplete="off"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('id_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">FIN (Financial Identification Number)</label>
                            <input type="text" name="fin" id="fin" value="{{ old('fin') }}"
                                   autocomplete="off"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('fin')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">Skip for Now</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">Create Business Profile</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
