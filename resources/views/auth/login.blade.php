<x-auth-layout>
    <div class="flex items-stretch h-full">

        {{-- Left brand panel (hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between bg-gradient-to-br from-primary-600 to-primary-800 p-10 text-white">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight">{{ config('app.name', 'Shopenhood') }}</span>
            </a>

            <div>
                <h2 class="text-2xl font-bold leading-snug mb-3">Your marketplace for<br>buying &amp; selling anything.</h2>
                <p class="text-sm text-primary-100 leading-relaxed mb-6">Thousands of listings updated daily. Sign in to manage your account, track orders, and discover new deals.</p>

                <ul class="space-y-2 text-sm text-primary-100">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-primary-300 text-xs"></i>
                        Secure checkout &amp; buyer protection
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-primary-300 text-xs"></i>
                        Sell in minutes — free listings available
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-primary-300 text-xs"></i>
                        Track orders and manage sales in one place
                    </li>
                </ul>
            </div>

            <p class="text-xs text-primary-200">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-8 bg-gray-50">
            <div class="w-full max-w-sm">

                {{-- Mobile brand --}}
                <div class="lg:hidden text-center mb-6">
                    <a href="{{ route('home') }}" class="text-lg font-bold text-primary-600">
                        {{ config('app.name', 'Shopenhood') }}
                    </a>
                </div>

                <h1 class="text-lg font-semibold text-gray-800 mb-1">Welcome back</h1>
                <p class="text-xs text-gray-500 mb-5">Sign in to your account to continue</p>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                    <x-auth-session-status class="mb-3 text-xs" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-xs font-medium text-gray-600 mb-1" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required autofocus autocomplete="username"
                                placeholder="you@example.com"
                                class="py-2 text-sm"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-xs font-medium text-gray-600 mb-1" />
                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••"
                                class="py-2 text-sm"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-3.5 h-3.5">
                                <span class="text-xs text-gray-600">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <x-primary-button class="w-full justify-center text-sm py-2">
                            {{ __('Sign In') }}
                        </x-primary-button>

                        <p class="text-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-semibold">Create one</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-auth-layout>
