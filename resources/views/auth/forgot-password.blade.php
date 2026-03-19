<x-auth-layout>
    <div class="flex items-stretch h-full">

        {{-- Left brand panel (hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col bg-[#000000] p-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight text-[#D4AF37]">{{ config('app.name', 'Shopenhood') }}</span>
            </a>

            <div class="flex-1 flex items-center">
            <div>
                <h2 class="text-2xl font-bold leading-snug mb-3 text-white">Reset your<br>password.</h2>
                <p class="text-sm text-[#E0E0E0]/70 leading-relaxed mb-6">Enter your email address and we'll send you a link to reset your password. It only takes a moment.</p>

                <ul class="space-y-2 text-sm text-[#E0E0E0]/70">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Secure reset link sent to your inbox
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Link expires after 60 minutes
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Your account stays protected
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

                <h1 class="text-lg font-semibold text-[#1A1A1A] mb-1">Forgot your password?</h1>
                <p class="text-xs text-[#37474F] mb-5">Enter your email and we'll send you a reset link</p>

                <div class="bg-white rounded-[6px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6">

                    <x-auth-session-status class="mb-3 text-xs" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="mb-1" />
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

                        <x-primary-button class="w-full h-[38px]">
                            {{ __('Send Reset Link') }}
                        </x-primary-button>

                        <p class="text-center text-xs text-[#37474F] pt-3 border-t border-[#E0E0E0]">
                            Remember your password?
                            <a href="{{ route('login') }}" class="text-[#D4AF37] hover:brightness-110 font-semibold transition">Sign in</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-auth-layout>
