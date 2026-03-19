@extends('admin.layouts.app')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Coupons</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage discount coupons and promotions</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Create Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-ticket text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['total'] }}</p>
                    <p class="text-[#37474F] text-xs">Total Coupons</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['active'] }}</p>
                    <p class="text-[#37474F] text-xs">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-clock text-[#C0392B]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['expired'] }}</p>
                    <p class="text-[#37474F] text-xs">Expired</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-[#37474F]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['inactive'] }}</p>
                    <p class="text-[#37474F] text-xs">Inactive</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or description..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="type"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Types</option>
                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
            </select>

            <select name="status"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>

            <select name="applicable_to"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Scope</option>
                <option value="all" {{ request('applicable_to') === 'all' ? 'selected' : '' }}>All Products</option>
                <option value="categories" {{ request('applicable_to') === 'categories' ? 'selected' : '' }}>Categories</option>
                <option value="listings" {{ request('applicable_to') === 'listings' ? 'selected' : '' }}>Listings</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Code</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Type</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Value</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Applies To</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Usage</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Dates</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <code class="bg-gray-100 px-2 py-0.5 rounded font-mono text-[12px] font-semibold text-[#1A1A1A]">{{ $coupon->code }}</code>
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $coupon->type === 'percentage' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                        {{ ucfirst($coupon->type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] font-semibold text-[#1A1A1A]">
                                    @if($coupon->type === 'percentage')
                                        {{ rtrim(rtrim($coupon->value, '0'), '.') }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    {{ $coupon->applicable_to === 'all' ? 'All Products' : ucfirst($coupon->applicable_to) }}
                                    @if($coupon->restrictions_count > 0)
                                        <span class="text-[11px] text-[#E0E0E0]">({{ $coupon->restrictions_count }})</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    <span class="font-semibold">{{ $coupon->usage_count }}</span>
                                    <span class="text-[#37474F]">/ {{ $coupon->usage_limit ?? '∞' }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($coupon->starts_at)
                                        <p class="text-[12px] text-[#37474F]">From: {{ $coupon->starts_at->format('M d, Y') }}</p>
                                    @endif
                                    @if($coupon->expires_at)
                                        <p class="text-[12px] {{ $coupon->expires_at->isPast() ? 'text-[#C0392B] font-semibold' : 'text-[#37474F]' }}">
                                            To: {{ $coupon->expires_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($coupon->expires_at && $coupon->expires_at->isPast())
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-[#37474F]">Expired</span>
                                    @else
                                        <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition {{ $coupon->is_active ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-[#C0392B]/10 text-[#C0392B] hover:bg-[#C0392B]/20' }}">
                                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    @click="$dispatch('open-confirm-modal', { message: 'Are you sure you want to delete this coupon?', form: $el.closest('form') })"
                                                    class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $coupons->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-ticket text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No coupons found</p>
                <a href="{{ route('admin.coupons.create') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition mt-3">
                    Create your first coupon
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
