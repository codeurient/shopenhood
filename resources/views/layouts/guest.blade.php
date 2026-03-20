<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use App\Models\Setting;
        $seoTitle       = $title       ?? Setting::getValue('seo.default_meta_title', config('app.name', 'Shopenhood'));
        $seoDescription = $metaDescription ?? Setting::getValue('seo.default_meta_description', '');
        $seoImage       = $ogImage ?? null;
        $seoUrl         = url()->current();
        $orgName        = Setting::getValue('seo.organization_name', config('app.name'));
    @endphp

    <title>{{ $seoTitle }}</title>

    {{-- ── Core Meta ────────────────────────────────────────────────── --}}
    @if($seoDescription)
    <meta name="description" content="{{ $seoDescription }}">
    @endif
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seoUrl }}">

    {{-- ── Open Graph (Facebook / LinkedIn / WhatsApp) ─────────────── --}}
    <meta property="og:type"        content="{{ $ogType ?? 'website' }}">
    <meta property="og:url"         content="{{ $seoUrl }}">
    <meta property="og:title"       content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:site_name"   content="{{ $orgName }}">
    @if($seoImage)
    <meta property="og:image"       content="{{ $seoImage }}">
    <meta property="og:image:alt"   content="{{ $seoTitle }}">
    @endif

    {{-- ── Twitter / X Card ────────────────────────────────────────── --}}
    <meta name="twitter:card"        content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title"       content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if($seoImage)
    <meta name="twitter:image"       content="{{ $seoImage }}">
    @endif

    {{-- ── Per-page extra meta (OG overrides, etc.) ───────────────── --}}
    @stack('meta')

    {{-- ── Search Engine Verification ────────────────────────────── --}}
    @if(Setting::getValue('seo.google_verification'))
    <meta name="google-site-verification" content="{{ Setting::getValue('seo.google_verification') }}">
    @endif
    @if(Setting::getValue('seo.bing_verification'))
    <meta name="msvalidate.01" content="{{ Setting::getValue('seo.bing_verification') }}">
    @endif

    {{-- ── OpenSearch ──────────────────────────────────────────────── --}}
    <link rel="search" type="application/opensearchdescription+xml"
          title="{{ $orgName }}" href="{{ route('opensearch') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        luxury: {
                            black:    '#000000',
                            surface:  '#1A1A1A',
                            charcoal: '#37474F',
                            gold:     '#D4AF37',
                            light:    '#E0E0E0',
                            white:    '#FFFFFF',
                            text:     '#1A1A1A',
                        },
                        primary: {
                            50:  '#f0f8ff',
                            100: '#BDDDFC',
                            200: '#a8d3fb',
                            300: '#88BDF2',
                            400: '#6db1ef',
                            500: '#88BDF2',
                            600: '#5a9dd9',
                            700: '#4682b4',
                            800: '#2e5f7d',
                            900: '#1e4159',
                        },
                        success: {
                            50:  '#f0fdf4',
                            100: '#CFFFDC',
                            500: '#68BA7F',
                            600: '#2E6F40',
                        },
                        danger: {
                            500: '#CD1C18',
                            600: '#a81614',
                        },
                        warning: {
                            500: '#C05800',
                            600: '#9a4600',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js -->
    <script>
        function toggleFilterPanel() {
            window.dispatchEvent(new CustomEvent('toggle-filter-panel'));
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    {{-- ── Global Organization + WebSite JSON-LD ─────────────────── --}}
    @php
        $socialProfiles = Setting::getValue('seo.organization_social_profiles', '');
        $sameAs = [];
        if ($socialProfiles) {
            $decoded = json_decode($socialProfiles, true);
            if (is_array($decoded)) { $sameAs = $decoded; }
        }
        $orgLogo = Setting::getValue('seo.organization_logo', '');
        $globalSchema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'Organization',
                    '@id'         => url('/') . '#organization',
                    'name'        => $orgName,
                    'url'         => url('/'),
                    'description' => Setting::getValue('seo.organization_description', ''),
                    'logo'        => $orgLogo ? ['@type' => 'ImageObject', 'url' => $orgLogo] : null,
                    'sameAs'      => $sameAs ?: null,
                ],
                [
                    '@type'            => 'WebSite',
                    '@id'              => url('/') . '#website',
                    'name'             => $orgName,
                    'url'              => url('/'),
                    'publisher'        => ['@id' => url('/') . '#organization'],
                    'potentialAction'  => [
                        '@type'       => 'SearchAction',
                        'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => url('/listings') . '?q={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];
        // Remove null values
        $globalSchema['@graph'][0] = array_filter($globalSchema['@graph'][0]);
    @endphp
    <script type="application/ld+json">{!! json_encode($globalSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    {{-- ── Per-page structured data ────────────────────────────────── --}}
    @stack('schema')
</head>
<body class="bg-white antialiased font-sans">
    <!-- Mobile-First Layout -->
    <div x-data="{ sidebarOpen: false, accountPanelOpen: false }"
         x-on:keydown.escape.window="sidebarOpen = false; accountPanelOpen = false; window.dispatchEvent(new CustomEvent('close-filter-panel'));"
         x-init="
    $watch('sidebarOpen', val => document.body.style.overflow = (val || accountPanelOpen) ? 'hidden' : '');
    $watch('accountPanelOpen', val => document.body.style.overflow = (val || sidebarOpen) ? 'hidden' : '');
"
         class="min-h-screen">

        <!-- Sidebar Component -->
        <x-mobile.sidebar />

        <!-- Account Panel Component -->
        <x-mobile.account-panel />

        <!-- Filter Panel Component -->
        <x-mobile.filter-panel />

        <!-- Main Content Area -->
        <div class="flex flex-col min-h-screen pb-16 md:pb-0">
            <!-- Header Component -->
            <x-mobile.header />

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-footer />

            <!-- Bottom Navigation (Mobile Only) -->
            <x-mobile.bottom-nav />
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

    @stack('scripts')

    {{-- Double-submit prevention --}}
    <script>
    (function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-allow-resubmit')) { return; }
            if (form.hasAttribute('data-submitting')) {
                e.preventDefault();
                return;
            }
            form.setAttribute('data-submitting', 'true');
            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            buttons.forEach(function(btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                if (btn.tagName === 'BUTTON') {
                    btn.setAttribute('data-original-text', btn.innerHTML);
                    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                }
            });
            setTimeout(function() {
                form.removeAttribute('data-submitting');
                buttons.forEach(function(btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    if (btn.tagName === 'BUTTON' && btn.hasAttribute('data-original-text')) {
                        btn.innerHTML = btn.getAttribute('data-original-text');
                    }
                });
            }, 10000);
        });
    })();
    </script>

    <!-- Global Confirm Modal -->
    <div x-data="{
            show: false,
            message: '',
            confirmTitle: 'Confirm Action',
            pendingForm: null,
            pendingResolve: null,
            open(msg, form, title, resolve) {
                this.message = msg;
                this.confirmTitle = title || 'Confirm Action';
                this.pendingForm = form || null;
                this.pendingResolve = resolve || null;
                this.show = true;
            },
            confirm() {
                this.show = false;
                if (this.pendingForm) { this.pendingForm.submit(); }
                if (this.pendingResolve) { this.pendingResolve(true); }
                this.pendingForm = null;
                this.pendingResolve = null;
            },
            cancel() {
                this.show = false;
                if (this.pendingResolve) { this.pendingResolve(false); }
                this.pendingForm = null;
                this.pendingResolve = null;
            }
        }"
        @open-confirm-modal.window="open($event.detail.message, $event.detail.form, $event.detail.title, $event.detail.resolve)"
        x-show="show"
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true">
        <div class="absolute inset-0 bg-black/50" @click="cancel()"></div>
        <div class="relative bg-white border border-[#E0E0E0] rounded-lg shadow-xl max-w-md w-full p-6 z-10" @click.stop>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-[15px] font-semibold text-[#1A1A1A]" x-text="confirmTitle"></h3>
                    <p class="mt-1 text-[13px] text-gray-600" x-text="message"></p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" @click="cancel()"
                    class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-gray-700 bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" @click="confirm()"
                    class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
    window.luxuryConfirm = function(message, title) {
        return new Promise(function(resolve) {
            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: { message: message, title: title || 'Confirm Action', form: null, resolve: resolve }
            }));
        });
    };
    </script>

</body>
</html>
