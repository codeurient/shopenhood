@extends('admin.layouts.app')

@section('title', 'Listing: ' . $listing->title)
@section('page-title', 'Listing Details')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('admin.listings.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Listings
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $listing->title }}</h2>
            <p class="text-[#37474F] text-sm mt-1">
                Created {{ $listing->created_at->diffForHumans() }} by <span class="font-medium">{{ $listing->user->name }}</span>
            </p>
        </div>
        <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="inline"
              onsubmit="return confirm('Are you sure you want to delete this listing?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                <i class="fa-solid fa-trash text-xs"></i>
                Delete
            </button>
        </form>
    </div>

    {{-- Approval Panel --}}
    @if($listing->isPending())
    <div x-data="{ showRejectModal: false }"
         class="mb-5 bg-yellow-50 border border-yellow-300 rounded-[6px] p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-clock text-yellow-600"></i>
            <span class="text-[13px] font-medium text-yellow-800">This listing is pending approval</span>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.listings.approval.approve', $listing) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Approve
                </button>
            </form>
            <button @click="showRejectModal = true"
                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reject
            </button>
        </div>

        {{-- Rejection Modal --}}
        <div x-show="showRejectModal" x-cloak style="display: none"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             @keydown.escape.window="showRejectModal = false">
            <div @click.away="showRejectModal = false"
                 class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-xl p-6 w-full max-w-md">
                <h3 class="text-[15px] font-semibold text-[#1A1A1A] mb-4">Reject Listing</h3>
                <form method="POST" action="{{ route('admin.listings.approval.reject', $listing) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                            Reason for rejection
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required maxlength="500"
                                  class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]"
                                  placeholder="Explain why this listing is being rejected..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRejectModal = false"
                                class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                            Reject Listing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Rejection Info --}}
    @if($listing->isRejected() && $listing->rejection_reason)
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-[6px] flex items-start gap-3">
        <i class="fa-solid fa-circle-xmark text-[#C0392B] mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="text-[13px] font-semibold text-red-800">This listing was rejected</p>
            <p class="text-[13px] text-red-700 mt-1">{{ $listing->rejection_reason }}</p>
            @if($listing->rejected_at)
                <p class="text-[11px] text-red-500 mt-1">Rejected {{ $listing->rejected_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Basic Information -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                        Basic Information
                    </h3>
                </div>
                <div class="p-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Title</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Slug</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Category</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Listing Type</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->listingType->name }}</dd>
                        </div>
                        @if($listing->base_price)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Base Price</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->currency }} {{ number_format($listing->base_price, 2) }}</dd>
                        </div>
                        @endif
                        @if($listing->discount_price)
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Discount Price</dt>
                            <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">
                                {{ $listing->currency }} {{ number_format($listing->discount_price, 2) }}
                                <span class="text-[11px] text-[#37474F] ml-1">
                                    ({{ $listing->discount_start_date->format('M d') }} – {{ $listing->discount_end_date->format('M d') }})
                                </span>
                            </dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-[12px] font-medium text-[#37474F]">Status</dt>
                            <dd class="mt-0.5">
                                @php
                                    $statusColors = [
                                        'draft'    => 'bg-gray-100 text-gray-600',
                                        'pending'  => 'bg-yellow-50 text-yellow-700',
                                        'active'   => 'bg-[#D4AF37]/20 text-[#D4AF37]',
                                        'sold'     => 'bg-blue-50 text-blue-700',
                                        'expired'  => 'bg-[#C0392B]/10 text-[#C0392B]',
                                        'rejected' => 'bg-[#C0392B]/10 text-[#C0392B]',
                                    ];
                                @endphp
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>

                    @if($listing->description)
                    <div class="mt-4 pt-4 border-t border-[#E0E0E0]">
                        <dt class="text-[12px] font-medium text-[#37474F] mb-1.5">Description</dt>
                        <dd class="text-[13px] text-[#1A1A1A] leading-relaxed">
                            {!! nl2br(e($listing->description)) !!}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Variations -->
            @if($listing->variations->count() > 0)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-[#D4AF37]"></i>
                        Product Variations ({{ $listing->variations->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#37474F] border-b border-[#000000]/20">
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">SKU</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Variant</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Price</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Stock</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listing->variations as $variation)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5 text-[13px] font-medium text-[#1A1A1A]">
                                    {{ $variation->sku }}
                                    @if($variation->is_default)
                                        <span class="ml-1.5 inline-flex items-center h-4 px-1.5 rounded text-[10px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Default</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($variation->attributes as $attr)
                                            <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-medium bg-gray-100 text-[#37474F]">
                                                {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    ${{ number_format($variation->price, 2) }}
                                    @if($variation->discount_price)
                                        <br><span class="text-[11px] text-green-600">${{ number_format($variation->discount_price, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    {{ $variation->stock_quantity }}
                                    @if($variation->stock_quantity <= $variation->low_stock_threshold && $variation->stock_quantity > 0)
                                        <span class="text-[11px] text-orange-600">(Low)</span>
                                    @elseif($variation->stock_quantity <= 0)
                                        <span class="text-[11px] text-[#C0392B]">(Out)</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($variation->is_active)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Active</span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-gray-500">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('admin.stock.edit', $variation) }}"
                                       class="inline-flex items-center gap-1.5 h-[28px] px-2.5 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition">
                                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                        Stock
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Images -->
            @if($listing->images->count() > 0)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-images text-[#D4AF37]"></i>
                        Images ({{ $listing->images->count() }})
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($listing->images as $image)
                        <div class="relative aspect-square bg-gray-100 rounded border border-[#E0E0E0] overflow-hidden">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-full object-cover">
                            @if($image->is_primary)
                                <span class="absolute top-1.5 left-1.5 inline-flex items-center h-4 px-1.5 rounded text-[10px] font-semibold bg-[#D4AF37] text-[#000000]">
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

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Status & Visibility -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-toggle-on text-[#D4AF37]"></i>
                        Status & Visibility
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach([
                        ['label' => 'Visible',    'value' => $listing->is_visible],
                        ['label' => 'Featured',   'value' => $listing->is_featured],
                        ['label' => 'Negotiable', 'value' => $listing->is_negotiable],
                    ] as $row)
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#37474F]">{{ $row['label'] }}</span>
                        <span class="text-[13px] font-medium {{ $row['value'] ? 'text-green-600' : 'text-[#C0392B]' }}">
                            {{ $row['value'] ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @endforeach
                    @if($listing->views_count > 0)
                    <div class="flex items-center justify-between pt-3 border-t border-[#E0E0E0]">
                        <span class="text-[13px] text-[#37474F]">Views</span>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">{{ number_format($listing->views_count) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Location -->
            @if($listing->country || $listing->city)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[#D4AF37]"></i>
                        Location
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @if($listing->country)
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Country</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->country }}</dd>
                    </div>
                    @endif
                    @if($listing->city)
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">City</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->city }}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- SEO -->
            @if($listing->meta_title || $listing->meta_description)
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-[#D4AF37]"></i>
                        SEO
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @if($listing->meta_title)
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Meta Title</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->meta_title }}</dd>
                    </div>
                    @endif
                    @if($listing->meta_description)
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Meta Description</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->meta_description }}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-clock text-[#D4AF37]"></i>
                        Timestamps
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Created</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Updated</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $listing->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
