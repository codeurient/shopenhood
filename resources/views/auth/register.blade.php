<x-auth-layout>
    <div class="flex items-stretch h-full">

        {{-- Left brand panel (hidden on small screens) --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between bg-gradient-to-br from-success-600 to-primary-700 p-10 text-white">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight">{{ config('app.name', 'Shopenhood') }}</span>
            </a>

            <div>
                <h2 class="text-2xl font-bold leading-snug mb-3">Join thousands of<br>buyers &amp; sellers today.</h2>
                <p class="text-sm text-green-100 leading-relaxed mb-6">Create your free account in seconds and start exploring listings, saving favorites, and selling your own items.</p>

                <ul class="space-y-2 text-sm text-green-100">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-300 text-xs"></i>
                        Free account — no hidden fees
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-300 text-xs"></i>
                        Post listings and reach real buyers
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-300 text-xs"></i>
                        Upgrade to Business for advanced tools
                    </li>
                </ul>
            </div>

            <p class="text-xs text-green-200">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
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

                <h1 class="text-lg font-semibold text-gray-800 mb-1">Create your account</h1>
                <p class="text-xs text-gray-500 mb-5">It's free and only takes a moment</p>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf

                        {{-- First Name & Last Name --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" class="text-xs font-medium text-gray-600 mb-1" />
                                <x-text-input
                                    id="first_name"
                                    type="text"
                                    name="first_name"
                                    :value="old('first_name')"
                                    required autofocus autocomplete="given-name"
                                    placeholder="John"
                                    class="py-2 text-sm"
                                />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" class="text-xs font-medium text-gray-600 mb-1" />
                                <x-text-input
                                    id="last_name"
                                    type="text"
                                    name="last_name"
                                    :value="old('last_name')"
                                    required autocomplete="family-name"
                                    placeholder="Doe"
                                    class="py-2 text-sm"
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
                            <x-input-label :value="__('Phone Number')" class="text-xs font-medium text-gray-600 mb-1" />
                            <input type="hidden" name="phone_code" :value="selected ? selected.phone_code : ''" />
                            <div class="flex gap-1.5">
                                {{-- Country code picker --}}
                                <div class="relative flex-shrink-0">
                                    <button type="button"
                                            @click="open = !open"
                                            class="flex items-center gap-1 h-full px-2.5 py-2 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 whitespace-nowrap">
                                        <template x-if="selected && selected.flag">
                                            <img :src="selected.flag" :alt="selected.code" class="w-5 h-3.5 object-cover rounded-sm flex-shrink-0">
                                        </template>
                                        <template x-if="!selected || !selected.flag">
                                            <span class="text-gray-400 text-xs">🌐</span>
                                        </template>
                                        <span x-text="selected ? '+' + selected.phone_code : '—'" class="text-xs font-medium"></span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    {{-- Dropdown --}}
                                    <div x-show="open" x-cloak
                                         class="absolute left-0 top-full mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
                                        <div class="p-2 border-b border-gray-100">
                                            <input type="text" x-model="search" @click.stop placeholder="Search country..."
                                                   class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto">
                                            <template x-for="code in filtered" :key="code.code">
                                                <li @click="select(code)"
                                                    class="flex items-center gap-2 px-3 py-2 text-xs cursor-pointer hover:bg-gray-50"
                                                    :class="selected && selected.code === code.code ? 'bg-primary-50 text-primary-700' : 'text-gray-700'">
                                                    <template x-if="code.flag">
                                                        <img :src="code.flag" :alt="code.code" class="w-5 h-3.5 object-cover rounded-sm flex-shrink-0">
                                                    </template>
                                                    <span x-text="code.name" class="flex-1 truncate"></span>
                                                    <span x-text="'+' + code.phone_code" class="text-gray-400 flex-shrink-0"></span>
                                                </li>
                                            </template>
                                            <li x-show="filtered.length === 0" class="px-3 py-2 text-xs text-gray-400">No results</li>
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
                                    class="py-2 text-sm flex-1 min-w-0"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('phone_code')" class="mt-1 text-xs" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-xs" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="text-xs font-medium text-gray-600 mb-1" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required autocomplete="username"
                                placeholder="you@example.com"
                                class="py-2 text-sm"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        {{-- Password & Confirm row --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-xs font-medium text-gray-600 mb-1" />
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="py-2 text-sm"
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm')" class="text-xs font-medium text-gray-600 mb-1" />
                                <x-text-input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required autocomplete="new-password"
                                    placeholder="••••••••"
                                    class="py-2 text-sm"
                                />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        {{-- Terms acceptance --}}
                        <div class="pt-1">
                            <label class="flex items-start gap-2.5 cursor-pointer group">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    id="terms"
                                    class="mt-0.5 w-3.5 h-3.5 flex-shrink-0 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 @error('terms') border-red-400 @enderror"
                                    {{ old('terms') ? 'checked' : '' }}
                                >
                                <span class="text-xs text-gray-600 leading-relaxed">
                                    I have read and agree to the
                                    <a href="{{ route('legal.index') }}" target="_blank"
                                       class="text-primary-600 hover:text-primary-700 font-medium underline underline-offset-2">
                                        Terms &amp; Conditions
                                    </a>,
                                    <a href="{{ route('legal.show', 'privacy-policy') }}" target="_blank"
                                       class="text-primary-600 hover:text-primary-700 font-medium underline underline-offset-2">
                                        Privacy Policy
                                    </a>, and
                                    <a href="{{ route('legal.show', 'cookie-policy') }}" target="_blank"
                                       class="text-primary-600 hover:text-primary-700 font-medium underline underline-offset-2">
                                        Cookie Policy
                                    </a>.
                                </span>
                            </label>
                            @error('terms')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-primary-button class="w-full justify-center text-sm py-2">
                            {{ __('Create Account') }}
                        </x-primary-button>

                        <p class="text-center text-xs text-gray-500 pt-3 border-t border-gray-100">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">Sign in</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-auth-layout>
