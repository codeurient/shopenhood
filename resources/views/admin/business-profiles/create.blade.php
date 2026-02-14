@extends('admin.layouts.app')

@section('title', 'Create Business Profile for ' . $user->name)
@section('page-title', 'Create Business Profile')

@section('content')
<div class="max-w-4xl">
    <div class="mb-4">
        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">&larr; Back to User</a>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- User Info --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Creating Profile For</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Name:</span>
                <span class="font-medium ml-2">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-gray-500">Email:</span>
                <span class="font-medium ml-2">{{ $user->email }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.business-profiles.store', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Basic Information</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name <span class="text-red-500">*</span></label>
                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="legal_name" class="block text-sm font-medium text-gray-700 mb-1">Legal Name</label>
                    <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('legal_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                    <input type="text" name="industry" id="industry" value="{{ old('industry') }}" placeholder="e.g. retail, wholesale, services"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('industry')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                    <select name="business_type" id="business_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select type...</option>
                        <option value="sole_proprietor" {{ old('business_type') === 'sole_proprietor' ? 'selected' : '' }}>Sole Proprietor</option>
                        <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="llc" {{ old('business_type') === 'llc' ? 'selected' : '' }}>LLC</option>
                        <option value="corporation" {{ old('business_type') === 'corporation' ? 'selected' : '' }}>Corporation</option>
                        <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('business_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Registration & Tax --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Registration & Tax</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('registration_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID (VAT/GST)</label>
                    <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tax_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Contact Information</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="business_email" class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                    <input type="email" name="business_email" id="business_email" value="{{ old('business_email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('business_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                    <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('business_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Business Address</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('address_line_1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('address_line_2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="state_province" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                    <input type="text" name="state_province" id="state_province" value="{{ old('state_province') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('state_province')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <select name="country_id" id="country_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select country...</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Branding</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Max 2MB. JPEG, PNG, WEBP</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-1">Banner</label>
                    <input type="file" name="banner" id="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Max 4MB. JPEG, PNG, WEBP</p>
                    @error('banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Settings</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="default_currency" class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                    <select name="default_currency" id="default_currency"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="USD" {{ old('default_currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('default_currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('default_currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="TRY" {{ old('default_currency') === 'TRY' ? 'selected' : '' }}>TRY</option>
                    </select>
                    @error('default_currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                    <input type="text" name="timezone" id="timezone" value="{{ old('timezone') }}" placeholder="e.g. Europe/Istanbul"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('timezone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Policies --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Policies</h2>

            <div class="space-y-4">
                <div>
                    <label for="return_policy" class="block text-sm font-medium text-gray-700 mb-1">Return Policy</label>
                    <textarea name="return_policy" id="return_policy" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('return_policy') }}</textarea>
                    @error('return_policy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_policy" class="block text-sm font-medium text-gray-700 mb-1">Shipping Policy</label>
                    <textarea name="shipping_policy" id="shipping_policy" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('shipping_policy') }}</textarea>
                    @error('shipping_policy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Create Business Profile</button>
        </div>
    </form>
</div>
@endsection
