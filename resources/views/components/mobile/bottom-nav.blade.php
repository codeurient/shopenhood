<!-- Bottom Navigation (Mobile Only) -->
<nav class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-[#E0E0E0] shadow-lg md:hidden">
    <div class="flex items-center justify-around h-16">

        <!-- Home -->
        <a href="{{ route('home') }}"
           class="flex flex-col items-center justify-center flex-1 h-full gap-1 {{ request()->routeIs('home') ? 'text-[#D4AF37]' : 'text-[#37474F]' }} hover:text-[#D4AF37] transition-colors">
            <i class="fa-solid fa-house text-xl"></i>
            <span class="text-[10px] font-semibold uppercase tracking-wide">Home</span>
        </a>

        <!-- Favorites -->
        @auth
            <a href="{{ route('user.favorites.index') }}"
               class="flex flex-col items-center justify-center flex-1 h-full gap-1 {{ request()->routeIs('user.favorites.index') ? 'text-[#D4AF37]' : 'text-[#37474F]' }} hover:text-[#D4AF37] transition-colors">
                <i class="fa-{{ request()->routeIs('user.favorites.index') ? 'solid' : 'regular' }} fa-heart text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Saved</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full gap-1 text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-regular fa-heart text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Saved</span>
            </a>
        @endauth

        <!-- Add Listing (Centre CTA) -->
        @auth
            <a href="{{ route('user.listings.create') }}"
               class="flex flex-col items-center justify-center flex-1 h-full -mt-6 gap-1">
                <div class="flex items-center justify-center w-14 h-14 bg-[#D4AF37] rounded shadow-lg hover:brightness-110 transition">
                    <i class="fa-solid fa-plus text-xl text-[#000000]"></i>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wide text-[#D4AF37]">Add</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full -mt-6 gap-1">
                <div class="flex items-center justify-center w-14 h-14 bg-[#D4AF37] rounded shadow-lg hover:brightness-110 transition">
                    <i class="fa-solid fa-plus text-xl text-[#000000]"></i>
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-wide text-[#D4AF37]">Add</span>
            </a>
        @endauth

        <!-- Messages -->
        @auth
            <a href="#messages"
               class="flex flex-col items-center justify-center flex-1 h-full gap-1 text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-regular fa-comment text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Messages</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full gap-1 text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-regular fa-comment text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Messages</span>
            </a>
        @endauth

        <!-- Account -->
        @auth
            <button type="button"
                    @click="accountPanelOpen = true"
                    class="flex flex-col items-center justify-center flex-1 h-full gap-1 text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-regular fa-user text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Account</span>
            </button>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center flex-1 h-full gap-1 text-[#37474F] hover:text-[#D4AF37] transition-colors">
                <i class="fa-solid fa-right-to-bracket text-xl"></i>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Login</span>
            </a>
        @endauth

    </div>
</nav>
