<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Profile Information</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update your account's name, email address, and contact details.
                </p>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                    Your email address is unverified.
                                    <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 ">
                                        Click here to re-send the verification email.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div>
                        <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
                        <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="block mt-1 w-full" :value="old('whatsapp_number', $user->whatsapp_number)" autocomplete="tel" placeholder="+1234567890" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Include country code (e.g. +1234567890). Used so buyers can contact you via WhatsApp.</p>
                        <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="default_currency" :value="__('Default Currency')" />
                        <select id="default_currency" name="default_currency"
                                class="w-full px-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-500 rounded-lg shadow-sm transition block mt-1 w-full">
                            <option value="">— Select currency —</option>
                            <option value="USD" {{ old('default_currency', $user->default_currency) === 'USD' ? 'selected' : '' }}>USD – US Dollar</option>
                            <option value="EUR" {{ old('default_currency', $user->default_currency) === 'EUR' ? 'selected' : '' }}>EUR – Euro</option>
                            <option value="GBP" {{ old('default_currency', $user->default_currency) === 'GBP' ? 'selected' : '' }}>GBP – British Pound</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pre-selects the currency when you create a new listing.</p>
                        <x-input-error :messages="$errors->get('default_currency')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-green-600 dark:text-green-400">Saved.</p>
                        @endif
                    </div>
                </form>

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>

            @if($user->isBusinessUser() && $user->businessProfile)
            <!-- Branding -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Branding</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update your business logo and banner image.
                </p>

                <form method="POST" action="{{ route('profile.branding') }}" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="logo" :value="__('Logo')" />
                            @if($user->businessProfile->logo)
                                <div class="mt-1 mb-2">
                                    <img src="{{ asset('storage/' . $user->businessProfile->logo) }}" alt="Current logo" class="w-20 h-20 rounded-lg object-cover">
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block mt-1 w-full text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max 2MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="banner" :value="__('Banner')" />
                            @if($user->businessProfile->banner)
                                <div class="mt-1 mb-2">
                                    <img src="{{ asset('storage/' . $user->businessProfile->banner) }}" alt="Current banner" class="w-full h-20 rounded-lg object-cover">
                                </div>
                            @endif
                            <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block mt-1 w-full text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max 4MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                            <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <x-primary-button>{{ __('Save Branding') }}</x-primary-button>

                        @if (session('status') === 'branding-updated')
                            <p class="text-sm text-green-600 dark:text-green-400">Saved.</p>
                        @endif
                    </div>
                </form>
            </div>
            @endif

            <!-- Update Password -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Update Password</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ensure your account is using a long, random password to stay secure.
                </p>

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="current_password" :value="__('Current Password')" />
                        <x-text-input id="current_password" name="current_password" type="password" class="block mt-1 w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('New Password')" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-green-600 dark:text-green-400">Saved.</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Delete Account -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="{ confirmingDeletion: false }">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete Account</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>

                <div class="mt-6">
                    <button @click="confirmingDeletion = true"
                            class="px-4 py-2 bg-red-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 transition">
                        Delete Account
                    </button>
                </div>

                <!-- Delete Confirmation -->
                <div x-show="confirmingDeletion" x-cloak style="display: none" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Are you sure you want to delete your account? Please enter your password to confirm.
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div>
                            <x-input-label for="delete_password" :value="__('Password')" class="sr-only" />
                            <x-text-input id="delete_password" name="password" type="password" class="block w-full sm:w-3/4" placeholder="Password" />
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-4 flex gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 transition">
                                Confirm Delete
                            </button>
                            <button type="button" @click="confirmingDeletion = false"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
