<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-success-100 to-primary-100">
        <div class="w-full max-w-md">
            <!-- Logo/Brand -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <h1 class="text-4xl font-bold text-primary-600">{{ config('app.name', 'Shopenhood') }}</h1>
                </a>
                <p class="mt-2 text-gray-600">Create your account and start shopping</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone Number (Optional) -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number (Optional)')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="phone" type="tel" name="phone" :value="old('phone')" autocomplete="tel" placeholder="Enter your phone number" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-semibold mb-2" />
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Register Button -->
                    <x-primary-button class="w-full mt-6">
                        {{ __('Create Account') }}
                    </x-primary-button>

                    <!-- Login Link -->
                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                                Log in
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer Links -->
            <div class="text-center mt-8 text-sm text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-primary-600">Back to Home</a>
            </div>
        </div>
    </div>
</x-guest-layout>
