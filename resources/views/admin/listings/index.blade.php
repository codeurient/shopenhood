@extends('admin.layouts.app')

@section('title', 'Listings Management')
@section('page-title', 'Listings')

@section('content')
<div x-data="{
    selectedIds: [],
    get hasSelected() { return this.selectedIds.length > 0; }
}">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Listings</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage product listings</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['total'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Total</p>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Active</p>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Pending</p>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-[#C0392B]">{{ $stats['rejected'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Rejected</p>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-[#37474F]">{{ $stats['draft'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Drafts</p>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['deleted'] }}</p>
            <p class="text-[#37474F] text-xs mt-0.5">Deleted</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block mb-1.5 text-[12px] font-medium text-[#1A1A1A]">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search listings..."
                       class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
            </div>
            <div>
                <label class="block mb-1.5 text-[12px] font-medium text-[#1A1A1A]">User</label>
                <input type="text" name="user_search" value="{{ request('user_search') }}"
                       placeholder="Name or email..."
                       class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
            </div>
            <div>
                <label class="block mb-1.5 text-[12px] font-medium text-[#1A1A1A]">Status</label>
                <select name="status"
                        class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <option value="">All Statuses</option>
                    <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="sold"     {{ request('status') === 'sold'     ? 'selected' : '' }}>Sold</option>
                    <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="deleted"  {{ request('status') === 'deleted'  ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div>
                <label class="block mb-1.5 text-[12px] font-medium text-[#1A1A1A]">Category</label>
                <select name="category_id"
                        class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Filter
                </button>
                <a href="{{ route('admin.listings.index') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div x-show="hasSelected" style="display: none;"
         class="mb-4 p-3 bg-[#37474F]/10 border border-[#37474F]/30 rounded-[6px] flex items-center gap-3 flex-wrap">
        <span class="text-[13px] text-[#1A1A1A] font-medium" x-text="`${selectedIds.length} listing(s) selected`"></span>

        @if(request('status') === 'deleted')
            <form action="{{ route('admin.listings.bulk-restore') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    Restore Selected
                </button>
            </form>
            <form action="{{ route('admin.listings.bulk-force-destroy') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button"
                    @click="$dispatch('open-confirm-modal', { message: `Permanently delete ${selectedIds.length} listing(s)? This cannot be undone.`, form: $el.closest('form') })"
                    class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Permanently Delete Selected
                </button>
            </form>
        @else
            <form action="{{ route('admin.listings.bulk-delete') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button"
                    @click="$dispatch('open-confirm-modal', { message: `Delete ${selectedIds.length} listing(s)?`, form: $el.closest('form') })"
                    class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Delete Selected
                </button>
            </form>
        @endif

        <button type="button" @click="selectedIds = []"
            class="inline-flex items-center h-[28px] px-3 text-[12px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            Clear Selection
        </button>
    </div>

    <!-- Listings Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#37474F] border-b border-[#000000]/20">
                        <th class="px-3 py-2.5 w-10">
                            <input type="checkbox" id="select-all"
                                   @change="selectedIds = $event.target.checked ? [{{ $listings->pluck('id')->join(',') }}] : []"
                                   class="rounded border-[#E0E0E0] accent-[#D4AF37]">
                        </th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Listing</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">User</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Category</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Type</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Price</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                        <td class="px-3 py-2.5">
                            <input type="checkbox" :value="{{ $listing->id }}" x-model="selectedIds"
                                   class="rounded border-[#E0E0E0] accent-[#D4AF37]">
                        </td>
                        <td class="px-3 py-2.5 max-w-[220px]">
                            <div class="flex items-center gap-2">
                                @if($listing->primaryImage)
                                    <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}"
                                         alt="{{ $listing->title }}"
                                         class="w-9 h-9 rounded border border-[#E0E0E0] object-cover flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 bg-gray-100 rounded border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-box text-[#E0E0E0] text-sm"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-[13px] font-medium text-[#1A1A1A] truncate" title="{{ $listing->title }}">{{ $listing->title }}</div>
                                    <div class="text-[11px] text-[#37474F] truncate">{{ $listing->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2.5 max-w-[160px]">
                            @if($listing->user)
                                <div class="text-[13px] font-medium text-[#1A1A1A] truncate" title="{{ $listing->user->name }}">{{ $listing->user->name }}</div>
                                <div class="text-[11px] text-[#37474F] truncate">{{ $listing->user->email }}</div>
                            @else
                                <span class="text-[#37474F]">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A] max-w-[120px] truncate">{{ $listing->category->name }}</td>
                        <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A] max-w-[100px] truncate">{{ $listing->listingType->name }}</td>
                        <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                            @if($listing->base_price)
                                {{ $listing->currency }} {{ number_format($listing->base_price, 2) }}
                            @else
                                <span class="text-[#37474F]">N/A</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5">
                            @if($listing->trashed())
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-purple-100 text-purple-700">Deleted</span>
                                <div class="text-[11px] text-[#37474F] mt-0.5">{{ $listing->deleted_at->diffForHumans() }}</div>
                            @else
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
                            @endif
                        </td>
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($listing->trashed())
                                    <form method="POST" action="{{ route('admin.listings.restore', $listing->id) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 h-[28px] px-2.5 text-[12px] font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.listings.force-delete', $listing->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            @click="$dispatch('open-confirm-modal', { message: 'Permanently delete this listing? This cannot be undone.', form: $el.closest('form') })"
                                            class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    @if($listing->isPending())
                                        <form method="POST" action="{{ route('admin.listings.approval.approve', $listing) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 h-[28px] px-2.5 text-[12px] font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition">
                                                <i class="fa-solid fa-check text-xs"></i>
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.listings.show', $listing) }}"
                                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                       title="View">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            @click="$dispatch('open-confirm-modal', { message: 'Are you sure you want to delete this listing?', form: $el.closest('form') })"
                                            class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                            title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fa-solid fa-box-open text-3xl text-[#E0E0E0] mb-3 block"></i>
                            <p class="text-[#37474F] text-[13px]">No listings found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listings->hasPages())
            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $listings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
