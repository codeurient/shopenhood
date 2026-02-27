<x-guest-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('business.profile') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2 inline-block">
                &larr; Back to Profile
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Business Profile</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <a href="{{ route('business.profile') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2 inline-block">
                    &larr; Back to Profile
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Business Profile</h2>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('business.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information (Read-only fields) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Business Information</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Some fields can only be changed by an administrator.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Name</label>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-600 dark:text-gray-400">
                                {{ $businessProfile->business_name }}
                            </div>
                        </div>

                        @if($businessProfile->legal_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Legal Name</label>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-600 dark:text-gray-400">
                                {{ $businessProfile->legal_name }}
                            </div>
                        </div>
                        @endif

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $businessProfile->description) }}</textarea>
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
                            <input type="email" name="business_email" id="business_email" value="{{ old('business_email', $businessProfile->business_email) }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="business_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Phone</label>
                            <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone', $businessProfile->business_phone) }}" placeholder="+1234567890"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                            <input type="url" name="website" id="website" value="{{ old('website', $businessProfile->website) }}" placeholder="https://"
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
                            <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $businessProfile->address_line_1) }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('address_line_1')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Line 2</label>
                            <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $businessProfile->address_line_2) }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('address_line_2')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $businessProfile->city) }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State/Province</label>
                            <input type="text" name="state_province" id="state_province" value="{{ old('state_province', $businessProfile->state_province) }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('state_province')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $businessProfile->postal_code) }}"
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
                                    <option value="{{ $country->id }}" {{ old('country_id', $businessProfile->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Identity & Tax (read-only) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border border-amber-200 dark:border-amber-700">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-amber-200 dark:border-amber-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Identity & Tax Information</h3>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-600">Read-only</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        These fields are encrypted and cannot be changed through this form. If you need to update this information, please contact support with your request and the reason for the change.
                    </p>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name (as on ID)</dt>
                            <dd class="mt-1 w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $sensitiveData['id_full_name'] ?? '— Not provided —' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID / Passport Number</dt>
                            <dd class="mt-1 w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $sensitiveData['id_number'] ?? '— Not provided —' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">FIN (Financial ID Number)</dt>
                            <dd class="mt-1 w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $sensitiveData['fin'] ?? '— Not provided —' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration Number</dt>
                            <dd class="mt-1 w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $sensitiveData['registration_number'] ?? '— Not provided —' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tax ID (VAT/GST)</dt>
                            <dd class="mt-1 w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $sensitiveData['tax_id'] ?? '— Not provided —' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('business.profile') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
