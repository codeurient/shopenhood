<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('user.listings.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition mb-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to Listings
            </a>
            <h1 class="text-xl font-bold text-[#1A1A1A] leading-tight">{{ $listing->title }}</h1>
            <p class="text-[12px] text-[#37474F] mt-0.5">Created {{ $listing->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            @if(!in_array($listing->status, ['sold', 'expired']))
            <a href="{{ route('user.listings.edit', $listing) }}"
               class="inline-flex items-center gap-1.5 h-[34px] px-3 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-pen text-xs"></i>
                Edit
            </a>
            @endif
            <form action="{{ route('user.listings.toggle', $listing) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                    <i class="fa-solid {{ $listing->is_visible ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                    {{ $listing->is_visible ? 'Hide' : 'Show' }}
                </button>
            </form>
        </div>
    </div>

    {{-- ── Status Banners ──────────────────────────────────────────────── --}}
    @if($listing->status === 'pending')
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded text-[13px]">
        <i class="fa-solid fa-clock flex-shrink-0"></i>
        This listing is <strong>pending approval</strong> and is not yet visible to buyers.
    </div>
    @endif

    @if($listing->isRejected() && $listing->rejection_reason)
    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded text-[13px]">
        <div class="flex items-center gap-2 text-red-700 font-semibold mb-1">
            <i class="fa-solid fa-circle-xmark"></i>
            Listing Rejected
        </div>
        <p class="text-red-600">{{ $listing->rejection_reason }}</p>
        @if($listing->rejected_at)
        <p class="text-[11px] text-red-400 mt-1">{{ $listing->rejected_at->diffForHumans() }}</p>
        @endif
    </div>
    @endif

    {{-- ── Main Grid ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left / Main ──────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Basic Information --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-circle-info text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Basic Information</h2>
                </div>
                <div class="px-5 py-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Title</dt>
                            <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Category</dt>
                            <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->category->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Listing Type</dt>
                            <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->listingType->name ?? '—' }}</dd>
                        </div>
                        @if($listing->base_price)
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Base Price</dt>
                            <dd class="text-[13px] font-semibold text-[#1A1A1A]">{{ $listing->currency ?? 'USD' }} {{ number_format($listing->base_price, 2) }}</dd>
                        </div>
                        @endif
                        @if($listing->discount_price)
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Discount Price</dt>
                            <dd class="text-[13px] text-[#1A1A1A]">
                                {{ $listing->currency ?? 'USD' }} {{ number_format($listing->discount_price, 2) }}
                                @if($listing->discount_start_date && $listing->discount_end_date)
                                <span class="text-[11px] text-[#37474F]">({{ $listing->discount_start_date->format('M d') }} – {{ $listing->discount_end_date->format('M d') }})</span>
                                @endif
                            </dd>
                        </div>
                        @endif
                        @if($listing->store_name)
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Store</dt>
                            <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->store_name }}</dd>
                        </div>
                        @endif
                    </dl>

                    @if($listing->short_description)
                    <div class="mt-5 pt-4 border-t border-[#E0E0E0]">
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">Short Description</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->short_description }}</dd>
                    </div>
                    @endif

                    @if($listing->description)
                    <div class="mt-4 pt-4 border-t border-[#E0E0E0]">
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1">Description</dt>
                        <dd class="text-[13px] text-[#1A1A1A] leading-relaxed">{!! nl2br(e($listing->description)) !!}</dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Attributes --}}
            @if($listing->listingVariants && $listing->listingVariants->count() > 0)
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-tags text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Attributes</h2>
                </div>
                <div class="px-5 py-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        @foreach($listing->listingVariants->groupBy('variant_id') as $variantId => $items)
                        <div>
                            <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">{{ $items->first()->variant->name ?? 'Attribute' }}</dt>
                            <dd class="flex flex-wrap gap-1.5">
                                @foreach($items as $item)
                                <span class="px-2 py-0.5 text-[11px] font-medium bg-[#F5F5F5] border border-[#E0E0E0] text-[#1A1A1A] rounded">
                                    {{ $item->variantItem?->value ?? $item->custom_value }}
                                </span>
                                @endforeach
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif

            {{-- Product Variations --}}
            @if($listing->variations && $listing->variations->count() > 0)
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-layer-group text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">
                        Product Variations
                        <span class="ml-1 px-2 py-0.5 text-[11px] bg-[#F5F5F5] border border-[#E0E0E0] text-[#37474F] rounded-full">{{ $listing->variations->count() }}</span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-[#37474F]">
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Variant</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Price</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Stock</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E0E0E0]">
                            @foreach($listing->variations as $i => $variation)
                            <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-[#FAFAFA]' }}">
                                <td class="px-4 py-3 text-[13px] font-medium text-[#1A1A1A]">
                                    {{ $variation->sku }}
                                    @if($variation->is_default)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-semibold bg-[#D4AF37]/15 text-[#1A1A1A] rounded">Default</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($variation->attributes as $attr)
                                        <span class="px-1.5 py-0.5 text-[11px] bg-[#F5F5F5] border border-[#E0E0E0] text-[#37474F] rounded">
                                            {{ $attr->variant->name ?? '' }}: {{ $attr->variantItem->value ?? '' }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#1A1A1A]">
                                    {{ $listing->currency ?? 'USD' }} {{ number_format($variation->price, 2) }}
                                    @if($variation->discount_price)
                                    <br><span class="text-[11px] text-green-600">{{ $listing->currency ?? 'USD' }} {{ number_format($variation->discount_price, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#1A1A1A]">
                                    {{ $variation->stock_quantity ?? 0 }}
                                    @if(($variation->stock_quantity ?? 0) <= 0)
                                    <span class="text-[11px] text-red-500">(Out)</span>
                                    @elseif(($variation->stock_quantity ?? 0) <= ($variation->low_stock_threshold ?? 5))
                                    <span class="text-[11px] text-orange-500">(Low)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $variation->is_active ? 'bg-green-100 text-green-700' : 'bg-[#E0E0E0] text-[#37474F]' }}">
                                        {{ $variation->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Images --}}
            @if($listing->images && $listing->images->count() > 0)
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-images text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">
                        Images
                        <span class="ml-1 px-2 py-0.5 text-[11px] bg-[#F5F5F5] border border-[#E0E0E0] text-[#37474F] rounded-full">{{ $listing->images->count() }}</span>
                    </h2>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                        @foreach($listing->images as $image)
                        <div class="relative aspect-square bg-[#F5F5F5] rounded border border-[#E0E0E0] overflow-hidden">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-full object-cover">
                            @if($image->is_primary)
                            <span class="absolute top-1 left-1 px-1.5 py-0.5 bg-[#D4AF37] text-[#000000] text-[10px] font-bold rounded">
                                Primary
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right / Sidebar ──────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Status & Visibility --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-signal text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Status & Visibility</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    @php
                        $badge = match($listing->status) {
                            'active'   => 'bg-green-100 text-green-700',
                            'pending'  => 'bg-yellow-100 text-yellow-700',
                            'draft'    => 'bg-[#E0E0E0] text-[#37474F]',
                            'expired'  => 'bg-red-100 text-red-600',
                            'rejected' => 'bg-red-100 text-red-600',
                            'sold'     => 'bg-blue-100 text-blue-700',
                            default    => 'bg-[#E0E0E0] text-[#37474F]',
                        };
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">Status</span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">{{ ucfirst($listing->status) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">Visible</span>
                        <span class="text-[13px] font-semibold {{ $listing->is_visible ? 'text-green-600' : 'text-red-500' }}">
                            {{ $listing->is_visible ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">Negotiable</span>
                        <span class="text-[13px] font-semibold {{ $listing->is_negotiable ? 'text-green-600' : 'text-[#37474F]' }}">
                            {{ $listing->is_negotiable ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @if($listing->views_count > 0)
                    <div class="flex items-center justify-between pt-3 border-t border-[#E0E0E0]">
                        <span class="text-[13px] text-[#37474F]">Views</span>
                        <span class="text-[13px] font-semibold text-[#1A1A1A]">{{ number_format($listing->views_count) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Location --}}
            @if($listing->country || $listing->city)
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-location-dot text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Location</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    @if($listing->country)
                    <div>
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Country</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->country }}</dd>
                    </div>
                    @endif
                    @if($listing->city)
                    <div>
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">City</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->city }}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Timestamps --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-clock text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Timestamps</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Created</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Updated</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($listing->expires_at)
                    <div>
                        <dt class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-0.5">Expires</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $listing->expires_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
