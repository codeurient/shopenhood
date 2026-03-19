@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#1A1A1A]">Settings</h2>
        <p class="text-[#37474F] text-sm mt-1">Configure platform-wide settings</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Listing Settings -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-list text-[#D4AF37]"></i>
                    Listing Settings
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label for="listing_default_duration_days" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Default Listing Duration (days)
                    </label>
                    <input type="number"
                           id="listing_default_duration_days"
                           name="listing_default_duration_days"
                           value="{{ old('listing_default_duration_days', $settings['listing_default_duration_days']) }}"
                           min="1" max="365"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <p class="mt-1 text-[12px] text-[#37474F]">How long a listing stays active before expiring. Applies to normal users and as the default for business users.</p>
                    @error('listing_default_duration_days')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="listing_soft_delete_retention_days" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Soft Delete Retention (days)
                    </label>
                    <input type="number"
                           id="listing_soft_delete_retention_days"
                           name="listing_soft_delete_retention_days"
                           value="{{ old('listing_soft_delete_retention_days', $settings['listing_soft_delete_retention_days']) }}"
                           min="1" max="365"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <p class="mt-1 text-[12px] text-[#37474F]">How long soft-deleted listings are kept before permanent removal. Users can reshare during this period.</p>
                    @error('listing_soft_delete_retention_days')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-[#D4AF37]"></i>
                    Contact Information
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label for="contact_phone" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Phone Number</label>
                    <input type="text" id="contact_phone" name="contact_phone"
                           value="{{ old('contact_phone', $settings['contact_phone']) }}"
                           placeholder="e.g. (012) 526-19-19"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('contact_phone')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_email" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Email Address</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="{{ old('contact_email', $settings['contact_email']) }}"
                           placeholder="e.g. info@shopenhood.com"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('contact_email')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-share-nodes text-[#D4AF37]"></i>
                    Social Media Links
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label for="social_facebook" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        <i class="fa-brands fa-facebook-f mr-1"></i> Facebook URL
                    </label>
                    <input type="url" id="social_facebook" name="social_facebook"
                           value="{{ old('social_facebook', $settings['social_facebook']) }}"
                           placeholder="https://facebook.com/yourpage"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('social_facebook')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="social_instagram" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        <i class="fa-brands fa-instagram mr-1"></i> Instagram URL
                    </label>
                    <input type="url" id="social_instagram" name="social_instagram"
                           value="{{ old('social_instagram', $settings['social_instagram']) }}"
                           placeholder="https://instagram.com/yourpage"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('social_instagram')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="social_twitter" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        <i class="fa-brands fa-x-twitter mr-1"></i> X (Twitter) URL
                    </label>
                    <input type="url" id="social_twitter" name="social_twitter"
                           value="{{ old('social_twitter', $settings['social_twitter']) }}"
                           placeholder="https://x.com/yourpage"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('social_twitter')<p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-check text-xs"></i>
                Save Settings
            </button>
        </div>
    </form>

</div>
@endsection
