<x-auth-layout>
    <div class="flex items-stretch h-full">

        {{-- Left brand panel (hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col bg-[#000000] p-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight text-[#D4AF37]">{{ config('app.name', 'Shopenhood') }}</span>
            </a>

            <div class="flex-1 flex items-center">
            <div>
                <h2 class="text-2xl font-bold leading-snug mb-3 text-white">Your marketplace for<br>buying &amp; selling anything.</h2>
                <p class="text-sm text-[#E0E0E0]/70 leading-relaxed mb-6">Thousands of listings updated daily. Sign in to manage your account, track orders, and discover new deals.</p>

                <ul class="space-y-2 text-sm text-[#E0E0E0]/70">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Secure checkout &amp; buyer protection
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Sell in minutes — free listings available
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Track orders and manage sales in one place
                    </li>
                </ul>
            </div>
            </div>

        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-8 bg-white">
            <div class="w-full max-w-sm">

                {{-- Mobile brand --}}
                <div class="lg:hidden text-center mb-6">
                    <a href="{{ route('home') }}" class="text-lg font-bold text-[#D4AF37]">
                        {{ config('app.name', 'Shopenhood') }}
                    </a>
                </div>

                <h1 class="text-lg font-semibold text-[#1A1A1A] mb-1">Welcome back</h1>
                <p class="text-xs text-[#37474F] mb-5">Sign in to your account to continue</p>

                <div class="bg-white rounded-[6px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6">

                    <x-auth-session-status class="mb-3 text-xs" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="mb-1" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required autofocus autocomplete="username"
                                placeholder="you@example.com"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" class="mb-1" />
                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0 w-3.5 h-3.5">
                                <span class="text-xs text-[#37474F]">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-[#D4AF37] hover:brightness-110 font-medium transition">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <x-primary-button class="w-full h-[38px]">
                            {{ __('Sign In') }}
                        </x-primary-button>

                        <p class="text-center text-xs text-[#37474F] pt-3 border-t border-[#E0E0E0]">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-[#D4AF37] hover:brightness-110 font-semibold transition">Create one</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-auth-layout>
