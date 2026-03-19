<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Profile</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Manage your account details and preferences</p>
        </div>
    </div>

    <div class="space-y-5 max-w-3xl">

        {{-- ── Profile Information ─────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-user text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Profile Information</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">Update your account's name, email address, and contact details.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required autofocus autocomplete="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" required autocomplete="username"
                               value="{{ old('email', $user->email) }}"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 flex items-start gap-2 px-3 py-2 bg-yellow-50 border border-yellow-200 rounded text-[12px] text-yellow-700">
                                <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5"></i>
                                <span>
                                    Your email address is unverified.
                                    <button form="send-verification" class="underline font-semibold hover:text-yellow-900 transition">
                                        Click here to re-send the verification email.
                                    </button>
                                </span>
                            </div>
                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-1 text-[12px] text-green-600">
                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                    A new verification link has been sent to your email address.
                                </p>
                            @endif
                        @endif
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">WhatsApp Number</label>
                        <input id="whatsapp_number" name="whatsapp_number" type="text" autocomplete="tel"
                               value="{{ old('whatsapp_number', $user->whatsapp_number) }}"
                               placeholder="+1234567890"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition @error('whatsapp_number') border-red-400 @enderror">
                        <p class="mt-1 text-[11px] text-[#37474F]">Include country code (e.g. +1234567890). Used so buyers can contact you via WhatsApp.</p>
                        @error('whatsapp_number')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_currency" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Default Currency</label>
                        <select id="default_currency" name="default_currency"
                                class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('default_currency') border-red-400 @enderror">
                            <option value="">— Select currency —</option>
                            <option value="USD" {{ old('default_currency', $user->default_currency) === 'USD' ? 'selected' : '' }}>USD – US Dollar</option>
                            <option value="EUR" {{ old('default_currency', $user->default_currency) === 'EUR' ? 'selected' : '' }}>EUR – Euro</option>
                            <option value="GBP" {{ old('default_currency', $user->default_currency) === 'GBP' ? 'selected' : '' }}>GBP – British Pound</option>
                        </select>
                        <p class="mt-1 text-[11px] text-[#37474F]">Pre-selects the currency when you create a new listing.</p>
                        @error('default_currency')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Save
                        </button>
                        @if (session('status') === 'profile-updated')
                            <p class="text-[12px] text-green-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Saved.
                            </p>
                        @endif
                    </div>
                </form>

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>
        </div>

        {{-- ── Profile Picture ──────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-circle-user text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Profile Picture</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">Upload a profile picture shown on your listings and seller profile.</p>

                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden border border-[#E0E0E0] flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile picture" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#37474F]">
                                    <span class="text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-[13px] text-[#37474F] border border-[#E0E0E0] rounded p-0 bg-white focus:border-[#D4AF37] focus:outline-none transition">
                            <p class="mt-1 text-[11px] text-[#37474F]">Max 2MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                            @error('avatar')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Save Picture
                        </button>
                        @if (session('status') === 'avatar-updated')
                            <p class="text-[12px] text-green-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Banner Image ─────────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-panorama text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Banner Image</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">Upload a banner image shown at the top of your seller profile.</p>

                <form method="POST" action="{{ route('profile.banner') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-3">
                        @if($user->banner)
                            <div class="w-full h-24 rounded border border-[#E0E0E0] overflow-hidden">
                                <img src="{{ asset('storage/' . $user->banner) }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-full h-24 rounded border border-dashed border-[#E0E0E0] bg-[#F5F5F5] flex items-center justify-center">
                                <p class="text-[12px] text-[#37474F]">
                                    <i class="fa-solid fa-panorama mr-1"></i>
                                    No banner uploaded
                                </p>
                            </div>
                        @endif

                        <div>
                            <input type="file" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-[13px] text-[#37474F] border border-[#E0E0E0] rounded p-0 bg-white focus:border-[#D4AF37] focus:outline-none transition">
                            <p class="mt-1 text-[11px] text-[#37474F]">Max 4MB. JPEG, PNG, WEBP. Recommended: 1200×300px.</p>
                            @error('banner')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Save Banner
                        </button>
                        @if (session('status') === 'banner-updated')
                            <p class="text-[12px] text-green-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Branding (business users only) ──────────────────────────── --}}
        @if($user->isBusinessUser() && $user->businessProfile)
        <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-briefcase text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Branding</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">Update your business logo and banner image.</p>

                <form method="POST" action="{{ route('profile.branding') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Logo</label>
                            @if($user->businessProfile->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->businessProfile->logo) }}" alt="Current logo"
                                         class="w-16 h-16 rounded border border-[#E0E0E0] object-cover">
                                </div>
                            @endif
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-[13px] text-[#37474F] border border-[#E0E0E0] rounded p-0 bg-white focus:border-[#D4AF37] focus:outline-none transition">
                            <p class="mt-1 text-[11px] text-[#37474F]">Max 2MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                            @error('logo')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Banner</label>
                            @if($user->businessProfile->banner)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->businessProfile->banner) }}" alt="Current banner"
                                         class="w-full h-16 rounded border border-[#E0E0E0] object-cover">
                                </div>
                            @endif
                            <input type="file" id="branding_banner" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-[13px] text-[#37474F] border border-[#E0E0E0] rounded p-0 bg-white focus:border-[#D4AF37] focus:outline-none transition">
                            <p class="mt-1 text-[11px] text-[#37474F]">Max 4MB. JPEG, PNG, WEBP. Leave empty to keep current.</p>
                            @error('banner')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Save Branding
                        </button>
                        @if (session('status') === 'branding-updated')
                            <p class="text-[12px] text-green-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- ── Update Password ──────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                <i class="fa-solid fa-lock text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Update Password</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">Ensure your account is using a long, random password to stay secure.</p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Current Password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('current_password', 'updatePassword') border-red-400 @enderror">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">New Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('password', 'updatePassword') border-red-400 @enderror">
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition @error('password_confirmation', 'updatePassword') border-red-400 @enderror">
                        @error('password_confirmation', 'updatePassword')
                            <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Save
                        </button>
                        @if (session('status') === 'password-updated')
                            <p class="text-[12px] text-green-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Delete Account ───────────────────────────────────────────── --}}
        <div class="bg-white border border-red-200 rounded-lg shadow-sm overflow-hidden"
             x-data="{ confirmingDeletion: false }">
            <div class="flex items-center gap-2 px-5 py-3 border-b border-red-200 bg-red-50">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-sm"></i>
                <h2 class="text-[13px] font-semibold text-red-700">Delete Account</h2>
            </div>
            <div class="px-5 py-4">
                <p class="text-[12px] text-[#37474F] mb-4">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>

                <button @click="confirmingDeletion = true"
                        class="inline-flex items-center gap-2 h-[34px] px-4 bg-red-600 text-white text-[13px] font-semibold rounded hover:bg-red-700 transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Delete Account
                </button>

                {{-- Confirm panel --}}
                <div x-show="confirmingDeletion" x-cloak style="display:none"
                     class="mt-5 px-4 py-4 bg-red-50 border border-red-200 rounded">
                    <p class="text-[13px] text-[#37474F] mb-4">
                        Are you sure? Please enter your password to confirm.
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Password</label>
                            <input id="delete_password" name="password" type="password"
                                   placeholder="Your password"
                                   class="w-full sm:w-2/3 h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-red-400 focus:outline-none transition @error('password', 'userDeletion') border-red-400 @enderror">
                            @error('password', 'userDeletion')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-red-600 text-white text-[13px] font-semibold rounded hover:bg-red-700 transition">
                                <i class="fa-solid fa-trash text-xs"></i>
                                Confirm Delete
                            </button>
                            <button type="button" @click="confirmingDeletion = false"
                                    class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
