<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Shopenhood') }}</title>

    <!-- SEO Meta Tags -->
    @if(isset($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-white antialiased font-sans">
    <!-- Mobile-First Layout -->
    <div x-data="{ sidebarOpen: false, accountPanelOpen: false }"
         x-on:keydown.escape.window="sidebarOpen = false; accountPanelOpen = false"
         x-init="
    $watch('sidebarOpen', val => document.body.style.overflow = (val || accountPanelOpen) ? 'hidden' : '');
    $watch('accountPanelOpen', val => document.body.style.overflow = (val || sidebarOpen) ? 'hidden' : '');
"
         class="min-h-screen">

        <!-- Sidebar Component -->
        <x-mobile.sidebar />

        <!-- Account Panel Component -->
        <x-mobile.account-panel />

        <!-- Main Content Area -->
        <div class="flex flex-col min-h-screen pb-16 md:pb-0">
            <!-- Header Component -->
            <x-mobile.header />

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-[#37474F] text-[#E0E0E0]/60 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

                        <!-- Brand -->
                        <div class="col-span-2 md:col-span-1">
                            <a href="{{ route('home') }}" class="text-[#D4AF37] font-bold text-base tracking-tight hover:brightness-110 transition">
                                {{ config('app.name', 'Shopenhood') }}
                            </a>
                            <p class="mt-2 text-xs leading-relaxed text-[#E0E0E0]/40">
                                Your local marketplace for buying and selling anything — fast, safe, and free.
                            </p>
                            <div class="flex gap-3 mt-4">
                                <a href="#" class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                                    <i class="fa-brands fa-facebook-f text-xs"></i>
                                </a>
                                <a href="#" class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                                    <i class="fa-brands fa-instagram text-xs"></i>
                                </a>
                                <a href="#" class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                                    <i class="fa-brands fa-x-twitter text-xs"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Shop -->
                        <div>
                            <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Shop</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('home') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Browse Listings</a></li>
                                <li><a href="{{ route('home') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">New Arrivals</a></li>
                                <li><a href="{{ route('home') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Categories</a></li>
                                <li><a href="{{ route('home') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Deals &amp; Offers</a></li>
                            </ul>
                        </div>

                        <!-- Sell -->
                        <div>
                            <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Sell</h4>
                            <ul class="space-y-2">
                                @auth
                                    <li><a href="{{ route('user.listings.create') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Post a Listing</a></li>
                                    <li><a href="{{ route('user.listings.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">My Listings</a></li>
                                @else
                                    <li><a href="{{ route('register') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Start Selling</a></li>
                                    <li><a href="{{ route('login') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Sign In</a></li>
                                @endauth
                                <li><a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Seller Guide</a></li>
                                <li><a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Business Accounts</a></li>
                            </ul>
                        </div>

                        <!-- Support -->
                        <div>
                            <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Support</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('pages.show', 'help') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Help Center</a></li>
                                <li><a href="{{ route('pages.show', 'contact') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Contact Us</a></li>
                                <li><a href="{{ route('pages.show', 'privacy-policy') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Privacy Policy</a></li>
                                <li><a href="{{ route('legal.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Terms of Service</a></li>
                            </ul>
                        </div>

                    </div>
                </div>

                <!-- Bottom bar -->
                <div class="border-t border-[#000000]/20">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
                        <p class="text-xs text-[#E0E0E0]/30">&copy; {{ date('Y') }} {{ config('app.name', 'Shopenhood') }}. All rights reserved.</p>
                        <div class="flex items-center gap-1.5 text-xs text-[#E0E0E0]/30">
                            <i class="fa-solid fa-shield-halved text-[#D4AF37]/60"></i>
                            <span>Secure &amp; trusted marketplace</span>
                        </div>
                    </div>
                </div>
            </footer>

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
            pendingForm: null,
            open(message, form) {
                this.message = message;
                this.pendingForm = form;
                this.show = true;
            },
            confirm() {
                this.show = false;
                if (this.pendingForm) { this.pendingForm.submit(); }
            },
            cancel() {
                this.show = false;
                this.pendingForm = null;
            }
        }"
        @open-confirm-modal.window="open($event.detail.message, $event.detail.form)"
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
                    <h3 class="text-[15px] font-semibold text-[#1A1A1A]">Confirm Action</h3>
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

</body>
</html>
