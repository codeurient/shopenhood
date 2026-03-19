{{-- Overlay --}}
<div x-show="accountPanelOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="accountPanelOpen = false"
     class="fixed inset-0 z-[60] bg-black bg-opacity-50"
     style="display: none;">
</div>

{{-- Panel --}}
<aside x-show="accountPanelOpen"
       x-trap.inert="accountPanelOpen"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-[61] w-72 bg-white shadow-xl overflow-y-auto scrollbar-hide flex flex-col"
       role="dialog"
       aria-modal="true"
       aria-label="Menu"
       style="display: none;">

    @auth
        {{-- Authenticated: Avatar + name + email --}}
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-[#E0E0E0] bg-white">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#D4AF37] flex items-center justify-center text-[#000000] font-bold text-base select-none">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-[#1A1A1A] truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-[#37474F] truncate">{{ auth()->user()->email }}</p>
            </div>
            <button type="button"
                    @click="accountPanelOpen = false"
                    class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-xmark text-[#37474F] text-sm"></i>
            </button>
        </div>

        {{-- Dashboard CTA --}}
        <div class="px-3 py-3">
            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-center gap-2 w-full h-[38px] bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-gauge text-xs"></i>
                Go to Dashboard
            </a>
        </div>

        <div class="mx-2 border-t border-[#E0E0E0]"></div>
    @else
        {{-- Guest: Login / Register header --}}
        <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E0E0E0] bg-white">
            <p class="text-sm font-semibold text-[#1A1A1A]">Welcome to {{ config('app.name') }}</p>
            <button type="button"
                    @click="accountPanelOpen = false"
                    class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-xmark text-[#37474F] text-sm"></i>
            </button>
        </div>

        <div class="px-3 py-3 flex gap-2">
            <a href="{{ route('login') }}"
               class="flex-1 flex items-center justify-center gap-1.5 px-3 h-[34px] text-[13px] font-semibold text-[#000000] bg-[#D4AF37] hover:brightness-110 rounded transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}"
               class="flex-1 flex items-center justify-center gap-1.5 px-3 h-[34px] text-[13px] font-semibold text-[#37474F] bg-white hover:bg-gray-50 rounded border border-[#E0E0E0] transition-colors">
                Register
            </a>
        </div>

        <div class="mx-2 border-t border-[#E0E0E0]"></div>
    @endauth

    {{-- Site Navigation (visible to all users) --}}
    @php
        $panelLegalLinks = \App\Models\Policy::showInFooter()
            ->whereIn('slug', ['terms-of-service', 'privacy-policy', 'terms-of-use'])
            ->orderBy('title')
            ->get(['title', 'slug']);
    @endphp

    <nav class="px-2 py-2 space-y-0.5">
        <p class="px-3 pt-1 pb-1 text-[10px] font-semibold text-[#37474F] uppercase tracking-wider">Explore</p>

        <a href="#"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors">
            <i class="fa-solid fa-store w-4 flex-shrink-0 text-[#37474F]"></i>
            Stores
        </a>

        <a href="{{ route('pages.show', 'help') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] transition-colors {{ request()->routeIs('pages.show') && request()->route('page')?->key === 'help' ? 'bg-[#D4AF37]/10 text-[#D4AF37] font-semibold' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]' }}">
            <i class="fa-solid fa-circle-question w-4 flex-shrink-0 text-[#37474F]"></i>
            Help
        </a>

        <a href="{{ route('pages.show', 'about') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] transition-colors {{ request()->routeIs('pages.show') && request()->route('page')?->key === 'about' ? 'bg-[#D4AF37]/10 text-[#D4AF37] font-semibold' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]' }}">
            <i class="fa-solid fa-circle-info w-4 flex-shrink-0 text-[#37474F]"></i>
            About
        </a>

        <a href="{{ route('legal.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] transition-colors {{ request()->routeIs('legal.*') ? 'bg-[#D4AF37]/10 text-[#D4AF37] font-semibold' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]' }}">
            <i class="fa-solid fa-scale-balanced w-4 flex-shrink-0 text-[#37474F]"></i>
            Legal Center
        </a>

        @foreach($panelLegalLinks as $legalPolicy)
        <a href="{{ route('legal.show', $legalPolicy) }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors pl-10">
            <span class="w-1 h-1 rounded-full bg-[#37474F] flex-shrink-0 -ml-4"></span>
            {{ $legalPolicy->title }}
        </a>
        @endforeach

        <a href="{{ route('blog.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] transition-colors {{ request()->routeIs('blog.*') ? 'bg-[#D4AF37]/10 text-[#D4AF37] font-semibold' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]' }}">
            <i class="fa-solid fa-newspaper w-4 flex-shrink-0 text-[#37474F]"></i>
            Blog
        </a>

        <a href="{{ route('pages.show', 'advertising-services') }}"
           class="flex items-center gap-3 px-3 py-2 rounded text-[13px] transition-colors {{ request()->routeIs('pages.show') && request()->route('page')?->key === 'advertising-services' ? 'bg-[#D4AF37]/10 text-[#D4AF37] font-semibold' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]' }}">
            <i class="fa-solid fa-bullhorn w-4 flex-shrink-0 text-[#37474F]"></i>
            Advertise
        </a>

        @auth
            @if(auth()->user()->isBusinessUser())
            <a href="{{ route('business.profile') }}"
               class="flex items-center justify-between px-3 py-2 rounded text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors">
                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-briefcase w-4 flex-shrink-0 text-[#37474F]"></i>
                    For Business
                </span>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-[#000000] bg-[#D4AF37] rounded-full">New</span>
            </a>
            @endif
        @endauth
    </nav>

    {{-- Language + Contact + Social footer (sticky to bottom) --}}
    <div class="mt-auto px-3 py-3 border-t border-[#E0E0E0] space-y-3">

        {{-- Language --}}
        <div class="flex items-center gap-2 text-[13px] text-[#37474F]">
            <i class="fa-solid fa-globe w-4 text-[#37474F]"></i>
            <span>English</span>
        </div>

        {{-- Contact --}}
        <div class="space-y-1.5">
            <p class="text-[10px] font-semibold text-[#37474F] uppercase tracking-wider">Contact Us</p>
            <a href="tel:+012526-19-19" class="flex items-center gap-2 text-[13px] text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-phone w-3.5 flex-shrink-0 text-[#37474F]"></i>
                (012) 526-19-19
            </a>
            <a href="mailto:info@shopenhood.com" class="flex items-center gap-2 text-[13px] text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-envelope w-3.5 flex-shrink-0 text-[#37474F]"></i>
                info@shopenhood.com
            </a>
        </div>

        {{-- Social Media --}}
        <div class="flex items-center gap-3">
            <a href="#" class="w-7 h-7 rounded border border-[#E0E0E0] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                <i class="fa-brands fa-facebook-f text-[#37474F] text-xs"></i>
            </a>
            <a href="#" class="w-7 h-7 rounded border border-[#E0E0E0] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                <i class="fa-brands fa-instagram text-[#37474F] text-xs"></i>
            </a>
            <a href="#" class="w-7 h-7 rounded border border-[#E0E0E0] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                <i class="fa-brands fa-x-twitter text-[#37474F] text-xs"></i>
            </a>
        </div>

    </div>

    @auth
    {{-- Logout --}}
    <div class="px-2 pb-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded text-[13px] text-red-600 hover:bg-red-50 transition-colors font-medium">
                <i class="fa-solid fa-right-from-bracket w-4 flex-shrink-0"></i>
                Log Out
            </button>
        </form>
    </div>
    @endauth

</aside>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
