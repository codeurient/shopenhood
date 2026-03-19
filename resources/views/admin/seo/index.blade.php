@extends('admin.layouts.app')

@section('title', 'SEO / AEO / GEO Settings')
@section('page-title', 'SEO / AEO / GEO Settings')

@section('content')
<div>

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">SEO / AEO / GEO Settings</h2>
            <p class="text-[#37474F] text-sm mt-1">Search, Answer Engine &amp; Generative Engine Optimization</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ url('/sitemap.xml') }}" target="_blank"
               class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-medium rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                <i class="fa-solid fa-sitemap text-[10px]"></i> sitemap.xml
            </a>
            <a href="{{ url('/robots.txt') }}" target="_blank"
               class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-medium rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                <i class="fa-solid fa-robot text-[10px]"></i> robots.txt
            </a>
            <a href="{{ url('/llms.txt') }}" target="_blank"
               class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-medium rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                <i class="fa-solid fa-brain text-[10px]"></i> llms.txt
            </a>
        </div>
    </div>


    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── 1. Organization ────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-building text-[#D4AF37]"></i>
                    Organization
                    <span class="ml-1 text-[11px] font-normal text-[#37474F]">— Used in JSON-LD structured data across all pages</span>
                </h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="organization_name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Organization Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="organization_name" name="organization_name"
                           value="{{ old('organization_name', $settings['organization_name']) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('organization_name')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="organization_logo" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Logo URL
                    </label>
                    <input type="url" id="organization_logo" name="organization_logo"
                           value="{{ old('organization_logo', $settings['organization_logo']) }}"
                           placeholder="https://yourdomain.com/logo.png"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('organization_logo')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="organization_description" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Description
                    </label>
                    <textarea id="organization_description" name="organization_description" rows="2"
                              placeholder="A short description of your organization for search engines and AI assistants."
                              class="border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 py-2 resize-none">{{ old('organization_description', $settings['organization_description']) }}</textarea>
                    @error('organization_description')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="organization_social_profiles" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Social Profiles
                    </label>
                    <textarea id="organization_social_profiles" name="organization_social_profiles" rows="3"
                              placeholder='["https://twitter.com/yoursite", "https://facebook.com/yoursite"]'
                              class="border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 py-2 resize-none font-mono text-[12px]">{{ old('organization_social_profiles', $settings['organization_social_profiles']) }}</textarea>
                    <p class="mt-1 text-[12px] text-[#37474F]">JSON array of social profile URLs. Used in Organization schema to help search engines link your profiles.</p>
                    @error('organization_social_profiles')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── 2. Default Meta Tags ────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-[#D4AF37]"></i>
                    Default Meta Tags
                    <span class="ml-1 text-[11px] font-normal text-[#37474F]">— Fallback for pages without specific meta tags</span>
                </h3>
            </div>
            <div class="p-5 space-y-5">
                <div x-data="{ len: {{ strlen(old('default_meta_title', $settings['default_meta_title'])) }} }">
                    <label for="default_meta_title" class="flex items-center gap-2 mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Default Meta Title
                        <span class="font-normal text-[12px]" :class="len > 60 ? 'text-red-500' : 'text-[#37474F]'">
                            (<span x-text="len"></span>/60)
                        </span>
                    </label>
                    <input type="text" id="default_meta_title" name="default_meta_title" maxlength="60"
                           value="{{ old('default_meta_title', $settings['default_meta_title']) }}"
                           x-on:input="len = $event.target.value.length"
                           placeholder="{{ config('app.name') }} — Buy &amp; Sell Everything Online"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('default_meta_title')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div x-data="{ len: {{ strlen(old('default_meta_description', $settings['default_meta_description'])) }} }">
                    <label for="default_meta_description" class="flex items-center gap-2 mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Default Meta Description
                        <span class="font-normal text-[12px]" :class="len > 160 ? 'text-red-500' : (len > 120 ? 'text-yellow-600' : 'text-[#37474F]')">
                            (<span x-text="len"></span>/160)
                        </span>
                    </label>
                    <textarea id="default_meta_description" name="default_meta_description" rows="2" maxlength="160"
                              x-on:input="len = $event.target.value.length"
                              placeholder="Discover millions of listings on {{ config('app.name') }}. Buy and sell with secure payments and fast delivery."
                              class="border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 py-2 resize-none">{{ old('default_meta_description', $settings['default_meta_description']) }}</textarea>
                    @error('default_meta_description')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── 3. Search Engine Verification ──────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-[#D4AF37]"></i>
                    Search Engine Verification
                </h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="google_verification" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        <i class="fa-brands fa-google mr-1 text-[#37474F]"></i> Google Verification Code
                    </label>
                    <input type="text" id="google_verification" name="google_verification"
                           value="{{ old('google_verification', $settings['google_verification']) }}"
                           placeholder="e.g. abc123def456"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <p class="mt-1 text-[12px] text-[#37474F]">Content value of <code class="text-[#1A1A1A] bg-[#F5F5F5] px-1 rounded">&lt;meta name="google-site-verification"&gt;</code></p>
                    @error('google_verification')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="bing_verification" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        <i class="fa-brands fa-microsoft mr-1 text-[#37474F]"></i> Bing Verification Code
                    </label>
                    <input type="text" id="bing_verification" name="bing_verification"
                           value="{{ old('bing_verification', $settings['bing_verification']) }}"
                           placeholder="e.g. ABC123DEF456"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <p class="mt-1 text-[12px] text-[#37474F]">Content value of <code class="text-[#1A1A1A] bg-[#F5F5F5] px-1 rounded">&lt;meta name="msvalidate.01"&gt;</code></p>
                    @error('bing_verification')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── 4. Sitemap Settings ─────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-sitemap text-[#D4AF37]"></i>
                    Sitemap
                </h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="sitemap_changefreq_listings" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Listings Change Frequency
                    </label>
                    <select id="sitemap_changefreq_listings" name="sitemap_changefreq_listings"
                            class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 bg-white">
                        @foreach(['always','hourly','daily','weekly','monthly','yearly','never'] as $freq)
                            <option value="{{ $freq }}" {{ old('sitemap_changefreq_listings', $settings['sitemap_changefreq_listings']) === $freq ? 'selected' : '' }}>
                                {{ ucfirst($freq) }}
                            </option>
                        @endforeach
                    </select>
                    @error('sitemap_changefreq_listings')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sitemap_priority_listings" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Listings Priority <span class="font-normal text-[#37474F]">(0.0 – 1.0)</span>
                    </label>
                    <input type="number" id="sitemap_priority_listings" name="sitemap_priority_listings"
                           step="0.1" min="0" max="1"
                           value="{{ old('sitemap_priority_listings', $settings['sitemap_priority_listings']) }}"
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    @error('sitemap_priority_listings')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/sitemap.xml') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-[12px] text-[#D4AF37] hover:underline">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            Preview sitemap.xml
                        </a>
                        <span class="text-[#E0E0E0]">·</span>
                        <a href="{{ url('/sitemap-listings.xml') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-[12px] text-[#D4AF37] hover:underline">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            sitemap-listings.xml
                        </a>
                        <span class="text-[#E0E0E0]">·</span>
                        <a href="{{ url('/sitemap-categories.xml') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-[12px] text-[#D4AF37] hover:underline">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            sitemap-categories.xml
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. robots.txt ──────────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-robot text-[#D4AF37]"></i>
                    robots.txt — Extra Rules
                    <span class="ml-1 text-[11px] font-normal text-[#37474F]">— Appended after built-in disallow rules</span>
                </h3>
            </div>
            <div class="p-5">
                <div class="mb-3 flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded text-[12px] text-blue-700">
                    <i class="fa-solid fa-circle-info flex-shrink-0 mt-0.5"></i>
                    <span>Standard disallow rules (admin, dashboard, API) and all AI crawler directives are auto-generated. Add only additional custom rules here.</span>
                </div>
                <textarea name="robots_txt_extra" rows="5"
                          placeholder="# Custom rules&#10;Disallow: /private-page&#10;Crawl-delay: 10"
                          class="border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 py-2 resize-y font-mono">{{ old('robots_txt_extra', $settings['robots_txt_extra']) }}</textarea>
                <div class="mt-2">
                    <a href="{{ url('/robots.txt') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-[12px] text-[#D4AF37] hover:underline">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        Preview robots.txt
                    </a>
                </div>
                @error('robots_txt_extra')
                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── 6. llms.txt (GEO) ──────────────────────────────────────── --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-brain text-[#D4AF37]"></i>
                    llms.txt — GEO Content
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#D4AF37]/10 text-[#B8960C] border border-[#D4AF37]/30">GEO</span>
                    <span class="ml-1 text-[11px] font-normal text-[#37474F]">— Appended to the auto-generated llms.txt</span>
                </h3>
            </div>
            <div class="p-5">
                <div class="mb-3 flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded text-[12px] text-amber-800">
                    <i class="fa-solid fa-lightbulb flex-shrink-0 mt-0.5"></i>
                    <div>
                        <strong>What is llms.txt?</strong> An emerging standard (like robots.txt but for AI) that provides LLMs (ChatGPT, Gemini, Claude, Perplexity) with a structured, authoritative overview of your site.
                        It improves how AI assistants describe and reference your marketplace in conversations.
                    </div>
                </div>
                <textarea name="llms_txt_custom" rows="8"
                          placeholder="## Additional Context&#10;&#10;Add extra information you want AI systems to know — unique selling points, policies, supported regions, etc."
                          class="border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 py-2 resize-y font-mono">{{ old('llms_txt_custom', $settings['llms_txt_custom']) }}</textarea>
                <div class="mt-2">
                    <a href="{{ url('/llms.txt') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-[12px] text-[#D4AF37] hover:underline">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        Preview llms.txt
                    </a>
                </div>
                @error('llms_txt_custom')
                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── Submit ─────────────────────────────────────────────────── --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Save SEO Settings
            </button>
        </div>

    </form>

</div>
@endsection
