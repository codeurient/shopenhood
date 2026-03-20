<footer class="bg-[#37474F] text-[#E0E0E0]/60 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1">
                <a href="{{ route('home') }}" class="text-[#D4AF37] font-bold text-base tracking-tight hover:brightness-110 transition">
                    {{ $appName }}
                </a>
                <p class="mt-2 text-xs leading-relaxed text-[#E0E0E0]/40">
                    Your local marketplace for buying and selling anything — fast, safe, and free.
                </p>

                {{-- Social links --}}
                <div class="flex gap-3 mt-4">
                    @if($socialFacebook)
                        <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" aria-label="Facebook"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                    @endif
                    @if($socialInstagram)
                        <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" aria-label="Instagram"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                    @endif
                    @if($socialTwitter)
                        <a href="{{ $socialTwitter }}" target="_blank" rel="noopener" aria-label="X / Twitter"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                    @endif
                    @if($socialYoutube)
                        <a href="{{ $socialYoutube }}" target="_blank" rel="noopener" aria-label="YouTube"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                    @endif
                    @if($socialLinkedin)
                        <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                    @endif
                    {{-- Fallback: show default icons when no settings configured --}}
                    @if(!$socialFacebook && !$socialInstagram && !$socialTwitter && !$socialYoutube && !$socialLinkedin)
                        <a href="#" aria-label="Facebook"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="#" aria-label="Instagram"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="#" aria-label="X / Twitter"
                           class="w-7 h-7 rounded-full bg-[#1A1A1A] hover:bg-[#D4AF37]/20 border border-[#37474F] hover:border-[#D4AF37] flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Shop --}}
            <div>
                <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Shop</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('listings.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                            Browse Listings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('listings.index', ['sort' => 'newest']) }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                            New Arrivals
                        </a>
                    </li>
                    @foreach($shopCategories as $category)
                        <li>
                            <a href="{{ route('listings.index', ['category' => $category->slug]) }}"
                               class="text-xs hover:text-[#E0E0E0] transition-colors">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                    @if($shopCategories->isEmpty())
                        <li>
                            <a href="{{ route('listings.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('listings.index', ['sort' => 'price_asc']) }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                Deals &amp; Offers
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Sell --}}
            <div>
                <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Sell</h4>
                <ul class="space-y-2">
                    @auth
                        <li>
                            <a href="{{ route('user.listings.create') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                Post a Listing
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.listings.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                My Listings
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('register') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                Start Selling
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                                Sign In
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Seller Guide</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">
                            Business Accounts
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Support --}}
            <div>
                <h4 class="text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider mb-3">Support</h4>
                <ul class="space-y-2">
                    @forelse($supportPages as $page)
                        <li>
                            <a href="{{ route('pages.show', $page->key) }}"
                               class="text-xs hover:text-[#E0E0E0] transition-colors">
                                {{ $page->title }}
                            </a>
                        </li>
                    @empty
                        <li><a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-xs hover:text-[#E0E0E0] transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.index') }}" class="text-xs hover:text-[#E0E0E0] transition-colors">Terms of Service</a></li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-[#000000]/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-[#E0E0E0]/30">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <div class="flex items-center gap-1.5 text-xs text-[#E0E0E0]/30">
                <i class="fa-solid fa-shield-halved text-[#D4AF37]/60"></i>
                <span>Secure &amp; trusted marketplace</span>
            </div>
        </div>
    </div>
</footer>
