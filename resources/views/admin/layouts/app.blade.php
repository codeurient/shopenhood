<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
                        danger: {
                            500: '#C0392B',
                            600: '#a93226',
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

    <style>.sidebar-nav::-webkit-scrollbar{display:none}.sidebar-nav{scrollbar-width:none;-ms-overflow-style:none}</style>
    @stack('styles')
</head>
<body class="bg-[#FFFFFF] antialiased font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-[220px] bg-[#37474F] flex flex-col transform transition-transform duration-300 ease-in-out md:sticky md:top-0 md:h-screen md:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               @click.away="sidebarOpen = false">

            <!-- Logo Bar -->
            <div class="bg-[#37474F] h-[52px] flex items-center px-3 gap-2 flex-shrink-0 border-b border-[#000000]/20">
                <a href="{{ route('home') }}" class="text-[#D4AF37] font-bold text-sm tracking-tight hover:brightness-110 transition truncate">
                    {{ config('app.name', 'Shopenhood') }}
                </a>
                <span class="text-[10px] font-semibold text-[#E0E0E0]/40 uppercase tracking-wider whitespace-nowrap">Admin</span>
            </div>

            <!-- Navigation -->
            <nav class="px-2 py-3 space-y-0.5 flex-1 overflow-y-auto sidebar-nav">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.dashboard') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-gauge-high w-4 text-center flex-shrink-0"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.categories.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-folder w-4 text-center flex-shrink-0"></i>
                    Categories
                </a>

                <a href="{{ route('admin.variants.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.variants.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-sliders w-4 text-center flex-shrink-0"></i>
                    Variants
                </a>

                <a href="{{ route('admin.locations.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.locations.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-location-dot w-4 text-center flex-shrink-0"></i>
                    Locations
                </a>

                <a href="{{ route('admin.listings.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.listings.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-list w-4 text-center flex-shrink-0"></i>
                    Listings
                </a>

                <a href="{{ route('admin.listing-types.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.listing-types.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-tag w-4 text-center flex-shrink-0"></i>
                    Listing Types
                </a>

                <a href="{{ route('admin.sliders.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.sliders.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-images w-4 text-center flex-shrink-0"></i>
                    Sliders &amp; Banners
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.users.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-users w-4 text-center flex-shrink-0"></i>
                    Users
                </a>

                <a href="{{ route('admin.business-profiles.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.business-profiles.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-building w-4 text-center flex-shrink-0"></i>
                    Business Profiles
                </a>

                <a href="{{ route('admin.stock.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.stock.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-boxes-stacked w-4 text-center flex-shrink-0"></i>
                    Stock Management
                </a>

                <a href="{{ route('admin.coupons.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.coupons.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-ticket w-4 text-center flex-shrink-0"></i>
                    Coupons
                </a>

                <a href="{{ route('admin.activity-logs.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.activity-logs.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-clipboard-list w-4 text-center flex-shrink-0"></i>
                    Activity Logs
                </a>

                <a href="{{ route('admin.login-histories.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.login-histories.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-lock w-4 text-center flex-shrink-0"></i>
                    Login Histories
                </a>

                <a href="{{ route('admin.blog-posts.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.blog-posts.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-pen-nib w-4 text-center flex-shrink-0"></i>
                    Blog Posts
                </a>

                <a href="{{ route('admin.pages.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.pages.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-file-lines w-4 text-center flex-shrink-0"></i>
                    Pages
                </a>

                <a href="{{ route('admin.legal.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.legal.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-scale-balanced w-4 text-center flex-shrink-0"></i>
                    Legal &amp; Policies
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium transition-colors pl-[9px] pr-3 border-l-[3px] {{ request()->routeIs('admin.settings.*') ? 'bg-[#000000]/30 text-[#D4AF37] border-[#D4AF37]' : 'text-[#E0E0E0] hover:bg-[#000000]/20 border-transparent' }}">
                    <i class="fa-solid fa-gear w-4 text-center flex-shrink-0"></i>
                    Settings
                </a>

                <div class="pt-2 mt-1 border-t border-[#E0E0E0]/10">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2.5 h-9 rounded text-[13px] font-medium text-[#E0E0E0]/50 hover:bg-[#000000]/20 hover:text-[#E0E0E0] transition-colors pl-[9px] pr-3 border-l-[3px] border-transparent">
                        <i class="fa-solid fa-arrow-left w-4 text-center flex-shrink-0"></i>
                        Back to Site
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 z-40 md:hidden"
             style="display: none;"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:ml-0 min-w-0">

            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-[#37474F] border-b border-[#000000]/30">
                <div class="flex items-center justify-between px-4 md:px-5 h-[52px]">
                    <!-- Mobile Menu Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="md:hidden p-2 rounded text-[#E0E0E0] hover:bg-[#37474F]/40 transition">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>

                    <!-- Page Title -->
                    <h1 class="text-[clamp(1rem,2vw,1.25rem)] font-semibold text-[#E0E0E0]">@yield('page-title', 'Dashboard')</h1>

                    <!-- Admin Info + Logout -->
                    <div class="flex items-center gap-3">
                        <span class="hidden md:block text-[13px] text-[#E0E0E0]/60">{{ auth()->guard('admin')->user()->name }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 h-[34px] px-3 bg-[#C0392B] text-white text-[13px] font-semibold rounded hover:bg-[#a93226] transition">
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-4 md:p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

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

    @stack('scripts')

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
        <div class="absolute inset-0 bg-black/70" @click="cancel()"></div>
        <div class="relative bg-[#1A1A1A] border border-[#37474F] rounded-[6px] shadow-xl max-w-md w-full p-6 z-10" @click.stop>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#C0392B]/20 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-[#E74C3C]"></i>
                </div>
                <div>
                    <h3 class="text-[15px] font-semibold text-[#E0E0E0]">Confirm Action</h3>
                    <p class="mt-1 text-[13px] text-[#E0E0E0]/60" x-text="message"></p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" @click="cancel()"
                    class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-semibold text-[#E0E0E0] bg-transparent border border-[#37474F] rounded hover:bg-[#37474F]/30 transition">
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
