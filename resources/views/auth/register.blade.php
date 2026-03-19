<x-auth-layout>
    <div class="flex items-stretch h-full">

        {{-- Left brand panel (hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col bg-[#000000] p-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight text-[#D4AF37]">{{ config('app.name', 'Shopenhood') }}</span>
            </a>

            <div class="flex-1 flex items-center">
            <div>
                <h2 class="text-2xl font-bold leading-snug mb-3 text-white">Join thousands of<br>buyers &amp; sellers today.</h2>
                <p class="text-sm text-[#E0E0E0]/70 leading-relaxed mb-6">Create your free account in seconds and start exploring listings, saving favorites, and selling your own items.</p>

                <ul class="space-y-2 text-sm text-[#E0E0E0]/70">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Free account — no hidden fees
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Post listings and reach real buyers
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-[#D4AF37] text-xs"></i>
                        Upgrade to Business for advanced tools
                    </li>
                </ul>
            </div>
            </div>

        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-8 bg-white overflow-y-auto">
            <div class="w-full max-w-sm">

                {{-- Mobile brand --}}
                <div class="lg:hidden text-center mb-6">
                    <a href="{{ route('home') }}" class="text-lg font-bold text-[#D4AF37]">
                        {{ config('app.name', 'Shopenhood') }}
                    </a>
                </div>

                <h1 class="text-lg font-semibold text-[#1A1A1A] mb-1">Create your account</h1>
                <p class="text-xs text-[#37474F] mb-5">It's free and only takes a moment</p>

                <div class="bg-white rounded-[6px] border border-[#E0E0E0] shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6">
                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf

                        {{-- First Name & Last Name --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" class="mb-1" />
                                <x-text-input
                                    id="first_name"
                                    type="text"
                                    name="first_name"
                                    :value="old('first_name')"
                                    required autofocus autocomplete="given-name"
                                    placeholder="John"
                                />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" class="mb-1" />
                                <x-text-input
                                    id="last_name"
                                    type="text"
                                    name="last_name"
                                    :value="old('last_name')"
                                    required autocomplete="family-name"
                                    placeholder="Doe"
                                />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div x-data="{
                            open: false,
                            search: '',
                            selected: @js($phoneCodes->first()),
                            codes: @js($phoneCodes),
                            get filtered() {
                                if (!this.search) return this.codes;
                                const s = this.search.toLowerCase();
                                return this.codes.filter(c =>
                                    c.name.toLowerCase().includes(s) ||
                                    c.phone_code.includes(s)
                                );
                            },
                            select(code) {
                                this.selected = code;
                                this.open = false;
                                this.search = '';
                            }
                        }" @click.outside="open = false">
                            <x-input-label :value="__('Phone Number')" class="mb-1" />
                            <input type="hidden" name="phone_code" :value="selected ? selected.phone_code : ''" />
                            <div class="flex gap-1.5">
                                {{-- Country code picker --}}
                                <div class="relative flex-shrink-0">
                                    <button type="button"
                                            @click="open = !open"
                                            class="flex items-center gap-1 h-[34px] px-2.5 border border-[#E0E0E0] rounded bg-white text-[13px] text-[#1A1A1A] hover:border-[#D4AF37] focus:outline-none transition whitespace-nowrap">
                                        <template x-if="selected && selected.flag">
                                            <img :src="selected.flag" :alt="selected.code" class="w-5 h-3.5 object-cover rounded-sm flex-shrink-0">
                                        </template>
                                        <template x-if="!selected || !selected.flag">
                                            <i class="fa-solid fa-globe text-[#37474F] text-xs"></i>
                                        </template>
                                        <span x-text="selected ? '+' + selected.phone_code : '—'" class="text-[12px] font-medium"></span>
                                        <i class="fa-solid fa-chevron-down text-[#37474F] text-[10px]"></i>
                                    </button>
                                    {{-- Dropdown --}}
                                    <div x-show="open" x-cloak
                                         class="absolute left-0 top-full mt-1 w-64 bg-white border border-[#E0E0E0] rounded shadow-lg z-50 overflow-hidden">
                                        <div class="p-2 border-b border-[#E0E0E0]">
                                            <input type="text" x-model="search" @click.stop placeholder="Search country..."
                                                   class="w-full px-2 py-1.5 text-[12px] border border-[#E0E0E0] rounded focus:outline-none focus:border-[#D4AF37] focus:ring-0">
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto">
                                            <template x-for="code in filtered" :key="code.code">
                                                <li @click="select(code)"
                                                    class="flex items-center gap-2 px-3 py-2 text-[12px] cursor-pointer hover:bg-[#D4AF37]/5 transition"
                                                    :class="selected && selected.code === code.code ? 'bg-[#D4AF37]/10 text-[#1A1A1A] font-medium' : 'text-[#1A1A1A]'">
                                                    <template x-if="code.flag">
                                                        <img :src="code.flag" :alt="code.code" class="w-5 h-3.5 object-cover rounded-sm flex-shrink-0">
                                                    </template>
                                                    <span x-text="code.name" class="flex-1 truncate"></span>
                                                    <span x-text="'+' + code.phone_code" class="text-[#37474F] flex-shrink-0"></span>
                                                </li>
                                            </template>
                                            <li x-show="filtered.length === 0" class="px-3 py-2 text-[12px] text-[#37474F]">No results</li>
                                        </ul>
                                    </div>
                                </div>
                                {{-- Local number input --}}
                                <x-text-input
                                    id="phone_number"
                                    type="tel"
                                    name="phone_number"
                                    :value="old('phone_number')"
                                    required autocomplete="tel"
                                    placeholder="501 234 5678"
                                    class="flex-1 min-w-0"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('phone_code')" class="mt-1 text-xs" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-xs" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="mb-1" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required autocomplete="username"
                                placeholder="you@example.com"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        {{-- Password & Confirm row --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="mb-1" />
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password"
                                    placeholder="••••••••"
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm')" class="mb-1" />
                                <x-text-input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required autocomplete="new-password"
                                    placeholder="••••••••"
                                />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        {{-- Terms acceptance --}}
                        <div class="pt-1">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    id="terms"
                                    class="mt-0.5 w-3.5 h-3.5 flex-shrink-0 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0 @error('terms') border-red-400 @enderror"
                                    {{ old('terms') ? 'checked' : '' }}
                                >
                                <span class="text-xs text-[#37474F] leading-relaxed">
                                    I have read and agree to the
                                    <a href="{{ route('legal.index') }}" target="_blank"
                                       class="text-[#D4AF37] hover:brightness-110 font-medium underline underline-offset-2 transition">
                                        Terms &amp; Conditions
                                    </a>,
                                    <a href="{{ route('legal.show', 'privacy-policy') }}" target="_blank"
                                       class="text-[#D4AF37] hover:brightness-110 font-medium underline underline-offset-2 transition">
                                        Privacy Policy
                                    </a>, and
                                    <a href="{{ route('legal.show', 'cookie-policy') }}" target="_blank"
                                       class="text-[#D4AF37] hover:brightness-110 font-medium underline underline-offset-2 transition">
                                        Cookie Policy
                                    </a>.
                                </span>
                            </label>
                            @error('terms')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-primary-button class="w-full h-[38px]">
                            {{ __('Create Account') }}
                        </x-primary-button>

                        <p class="text-center text-xs text-[#37474F] pt-3 border-t border-[#E0E0E0]">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-[#D4AF37] hover:brightness-110 font-semibold transition">Sign in</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-auth-layout>
