<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Address</h2>
            <a href="{{ route('user.addresses.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 text-sm font-medium transition">
                &larr; Back to Addresses
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.addresses.update', $address) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">

                    {{-- Label & Default --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Address Label <span class="text-red-500">*</span>
                            </label>
                            <select name="label" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                <option value="Home" {{ old('label', $address->label) === 'Home' ? 'selected' : '' }}>Home</option>
                                <option value="Work" {{ old('label', $address->label) === 'Work' ? 'selected' : '' }}>Work</option>
                                <option value="Other" {{ old('label', $address->label) === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as default address</span>
                            </label>
                        </div>
                    </div>

                    {{-- Section: Recipient Information --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recipient Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Recipient Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="recipient_name" required
                                       value="{{ old('recipient_name', $address->recipient_name) }}"
                                       placeholder="Full name of the recipient"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="phone" required
                                       value="{{ old('phone', $address->phone) }}"
                                       placeholder="+1 234 567 8900"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $address->email) }}"
                                   placeholder="Optional email for delivery notifications"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>
                    </div>

                    {{-- Section: Address Details --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Address Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="country" required
                                       value="{{ old('country', $address->country) }}"
                                       placeholder="Country"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" required
                                       value="{{ old('city', $address->city) }}"
                                       placeholder="City"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">District</label>
                                <input type="text" name="district"
                                       value="{{ old('district', $address->district) }}"
                                       placeholder="District or neighborhood"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                                <input type="text" name="postal_code"
                                       value="{{ old('postal_code', $address->postal_code) }}"
                                       placeholder="Postal / ZIP code"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="street" required
                                   value="{{ old('street', $address->street) }}"
                                   placeholder="Street name and number"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Building</label>
                                <input type="text" name="building"
                                       value="{{ old('building', $address->building) }}"
                                       placeholder="Building name or number"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Apartment / Unit</label>
                                <input type="text" name="apartment"
                                       value="{{ old('apartment', $address->apartment) }}"
                                       placeholder="Apartment, suite, unit, etc."
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>
                    </div>

                    {{-- Additional Notes --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                        <textarea name="additional_notes" rows="3"
                                  placeholder="Delivery instructions, landmarks, gate codes, etc."
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">{{ old('additional_notes', $address->additional_notes) }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('user.addresses.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Update Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
