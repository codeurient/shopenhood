<x-guest-layout>
    @php
        $bp = $user->businessProfile;
        $isVerified = $bp?->isApproved() ?? false;
        $isConfidentSeller = $bp?->isConfidentSellerApproved() ?? false;

        $bannerUrl = $bp?->banner
            ? asset('storage/' . $bp->banner)
            : ($user->banner ? asset('storage/' . $user->banner) : null);

        $avatarUrl = $bp?->logo
            ? asset('storage/' . $bp->logo)
            : ($user->avatar ? asset('storage/' . $user->avatar) : null);

        $displayName = $bp?->business_name ?? $user->name;
    @endphp

    <x-slot name="title">{{ $displayName }} — Shopenhood</x-slot>
    <x-slot name="metaDescription">View listings from {{ $displayName }} on Shopenhood.</x-slot>

    {{-- ================================================================ --}}
    {{-- BANNER + AVATAR HEADER                                           --}}
    {{-- ================================================================ --}}
    <div class="relative">
        {{-- Banner --}}
        <div class="h-40 md:h-56 bg-gradient-to-r from-primary-700 to-primary-400 overflow-hidden">
            @if($bannerUrl)
                <img src="{{ $bannerUrl }}" alt="{{ $displayName }}" class="w-full h-full object-cover">
            @endif
        </div>

        {{-- Avatar overlapping banner --}}
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex items-end gap-4 -mt-10 md:-mt-14">
                <div class="w-20 h-20 md:w-28 md:h-28 rounded-full border-4 border-white bg-gray-200 overflow-hidden flex-shrink-0 shadow-md">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-700">
                            <span class="text-2xl md:text-3xl font-bold text-white">{{ strtoupper(substr($displayName, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>

                <div class="pb-2 flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg md:text-2xl font-bold text-gray-900 truncate">{{ $displayName }}</h1>

                        @if($isVerified)
                            <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-600 font-semibold px-2 py-0.5 rounded-full border border-blue-200">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"/>
                                </svg>
                                Verified
                            </span>
                        @endif

                        @if($isConfidentSeller)
                            <span class="inline-flex items-center gap-1 text-xs bg-yellow-50 text-yellow-700 font-semibold px-2 py-0.5 rounded-full border border-yellow-200">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Confident Seller
                            </span>
                        @endif

                        @if($sellerBadge)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $sellerBadge['color'] }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $sellerBadge['label'] }}
                            </span>
                        @endif
                    </div>

                    @if($bp && $bp->business_name && $bp->business_name !== $user->name)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- STATS BAR                                                         --}}
    {{-- ================================================================ --}}
    <div class="bg-white border-b border-gray-100 mt-3">
        <div class="max-w-5xl mx-auto px-4 py-3">
            <div class="flex items-center gap-5 overflow-x-auto scrollbar-hide text-sm text-gray-600">
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span><strong class="text-gray-900">{{ number_format($listingCount) }}</strong> listing{{ $listingCount !== 1 ? 's' : '' }}</span>
                </div>

                @if($totalSold > 0)
                    <div class="w-px h-4 bg-gray-200 flex-shrink-0"></div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span><strong class="text-gray-900">{{ number_format($totalSold) }}+</strong> sold</span>
                    </div>
                @endif

                @if($avgRating > 0)
                    <div class="w-px h-4 bg-gray-200 flex-shrink-0"></div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span><strong class="text-gray-900">{{ $avgRating }}</strong> rating</span>
                    </div>
                @endif

                <div class="w-px h-4 bg-gray-200 flex-shrink-0"></div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Member since <strong class="text-gray-900">{{ $user->created_at->format('Y') }}</strong></span>
                </div>

                @if($bp?->city)
                    <div class="w-px h-4 bg-gray-200 flex-shrink-0"></div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $bp->city }}@if($bp->state_province), {{ $bp->state_province }}@endif</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-4 space-y-4">

        {{-- ================================================================ --}}
        {{-- BUSINESS INFO CARD (approved business profiles only)             --}}
        {{-- ================================================================ --}}
        @if($isVerified && $bp)
            <div class="bg-white rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">About</h2>

                @if($bp->description)
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $bp->description }}</p>
                @endif

                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600">
                    @if($bp->industry)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $bp->industry }}
                        </div>
                    @endif

                    @if($bp->website)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <a href="{{ $bp->website }}" target="_blank" rel="noopener noreferrer"
                               class="text-primary-600 hover:underline truncate max-w-xs">
                                {{ parse_url($bp->website, PHP_URL_HOST) ?? $bp->website }}
                            </a>
                        </div>
                    @endif

                    @if($bp->business_email)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $bp->business_email }}" class="text-primary-600 hover:underline">
                                {{ $bp->business_email }}
                            </a>
                        </div>
                    @endif

                    @if($bp->business_phone)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $bp->business_phone }}
                        </div>
                    @endif
                </div>

                @if($bp->return_policy)
                    <details class="text-sm">
                        <summary class="cursor-pointer text-gray-500 hover:text-gray-700 font-medium">Return Policy</summary>
                        <p class="mt-2 text-gray-600 leading-relaxed pl-2 border-l-2 border-gray-200">{{ $bp->return_policy }}</p>
                    </details>
                @endif

                @if($bp->shipping_policy)
                    <details class="text-sm">
                        <summary class="cursor-pointer text-gray-500 hover:text-gray-700 font-medium">Shipping Policy</summary>
                        <p class="mt-2 text-gray-600 leading-relaxed pl-2 border-l-2 border-gray-200">{{ $bp->shipping_policy }}</p>
                    </details>
                @endif
            </div>
        @elseif($user->bio)
            <div class="bg-white rounded-xl p-4">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">About</h2>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $user->bio }}</p>
            </div>
        @endif

        {{-- ================================================================ --}}
        {{-- CONTACT VIA WHATSAPP                                             --}}
        {{-- ================================================================ --}}
        @if($user->whatsapp_number)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $user->whatsapp_number) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Contact via WhatsApp
            </a>
        @endif

        {{-- ================================================================ --}}
        {{-- LISTINGS GRID                                                     --}}
        {{-- ================================================================ --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                Listings
                @if($listingCount > 0)
                    <span class="text-gray-400 font-normal normal-case">({{ number_format($listingCount) }})</span>
                @endif
            </h2>

            @if($listings->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    @foreach($listings as $listing)
                        <x-mobile.listing-card :listing="$listing" />
                    @endforeach
                </div>

                @if($listings->hasPages())
                    <div class="mt-6">
                        {{ $listings->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl py-16 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-sm text-gray-500">No active listings yet.</p>
                </div>
            @endif
        </div>

    </div>
</x-guest-layout>
