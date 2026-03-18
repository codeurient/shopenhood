@extends('admin.layouts.app')

@section('title', 'Create Coupon')
@section('page-title', 'Create Coupon')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Coupons
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Create New Coupon</h2>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="text-[13px] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.coupons.store') }}" method="POST" x-data="couponForm()">
        @csrf

        <!-- Coupon Details -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-[#D4AF37]"></i>
                    Coupon Details
                </h3>
            </div>
            <div class="p-4 space-y-4">

                <!-- Code -->
                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">
                        Coupon Code <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="code" id="code" required
                               value="{{ old('code') }}"
                               placeholder="e.g., SUMMER2026"
                               class="flex-1 h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 uppercase font-mono">
                        <button type="button" @click="generateCode()"
                                class="inline-flex items-center gap-1.5 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                            <i class="fa-solid fa-rotate text-xs"></i>
                            Generate
                        </button>
                    </div>
                    <p class="text-[12px] text-[#37474F] mt-1">Unique code that customers will enter at checkout</p>
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">
                            Discount Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" x-model="type" required
                                class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                            <option value="percentage" {{ old('type', 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">
                            Value <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#37474F] text-[13px]" x-text="type === 'percentage' ? '%' : '$'"></span>
                            <input type="number" name="value" required step="0.01" min="0.01"
                                   :max="type === 'percentage' ? 100 : ''"
                                   value="{{ old('value') }}"
                                   placeholder="0.00"
                                   class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] pl-8 pr-3">
                        </div>
                        <p class="text-[12px] text-[#37474F] mt-1" x-show="type === 'percentage'">Maximum 100%</p>
                    </div>
                </div>

                <!-- Purchase & Discount Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Minimum Purchase Amount</label>
                        <input type="number" name="min_purchase_amount" step="0.01" min="0"
                               value="{{ old('min_purchase_amount') }}"
                               placeholder="No minimum"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Maximum Discount Amount</label>
                        <input type="number" name="max_discount_amount" step="0.01" min="0"
                               value="{{ old('max_discount_amount') }}"
                               placeholder="No limit"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Cap the discount (useful for percentage coupons)</p>
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Total Usage Limit</label>
                        <input type="number" name="usage_limit" min="1"
                               value="{{ old('usage_limit') }}"
                               placeholder="Unlimited"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Maximum total times this coupon can be used</p>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Per User Limit</label>
                        <input type="number" name="per_user_limit" min="1"
                               value="{{ old('per_user_limit') }}"
                               placeholder="Unlimited"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Maximum times a single user can use this coupon</p>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Start Date</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at') }}"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Leave empty for immediate availability</p>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#37474F] mb-1">Expiry Date</label>
                        <input type="datetime-local" name="expires_at"
                               value="{{ old('expires_at') }}"
                               class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Leave empty for no expiry</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Internal note or description for this coupon"
                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2">{{ old('description') }}</textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center justify-between py-2 border-t border-[#E0E0E0]">
                    <div>
                        <p class="text-[13px] font-medium text-[#1A1A1A]">Active</p>
                        <p class="text-[12px] text-[#37474F]">Inactive coupons cannot be used at checkout</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-[#D4AF37] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>

            </div>
        </div>

        <!-- Scope & Restrictions -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-bullseye text-[#D4AF37]"></i>
                    Scope & Restrictions
                </h3>
            </div>
            <div class="p-4 space-y-4">

                <!-- Applicable To -->
                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">
                        Applicable To <span class="text-red-500">*</span>
                    </label>
                    <select name="applicable_to" x-model="applicableTo" required
                            class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                        <option value="all" {{ old('applicable_to', 'all') === 'all' ? 'selected' : '' }}>All Products</option>
                        <option value="categories" {{ old('applicable_to') === 'categories' ? 'selected' : '' }}>Specific Categories</option>
                        <option value="listings" {{ old('applicable_to') === 'listings' ? 'selected' : '' }}>Specific Listings</option>
                    </select>
                    <p class="text-[12px] text-[#37474F] mt-1">Choose where this coupon can be applied</p>
                </div>

                <!-- Categories -->
                <div x-show="applicableTo === 'categories'" x-transition>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Select Categories</label>
                    <div class="max-h-48 overflow-y-auto border border-[#E0E0E0] rounded p-3 space-y-2">
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="restrictions[]" value="{{ $category->id }}"
                                       {{ in_array($category->id, old('restrictions', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0">
                                <span class="text-[13px] text-[#1A1A1A]">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Listings -->
                <div x-show="applicableTo === 'listings'" x-transition>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Select Listings</label>
                    <div class="max-h-48 overflow-y-auto border border-[#E0E0E0] rounded p-3 space-y-2">
                        @foreach($listings as $listing)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="restrictions[]" value="{{ $listing->id }}"
                                       {{ in_array($listing->id, old('restrictions', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0">
                                <span class="text-[13px] text-[#1A1A1A]">{{ $listing->title }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Create Coupon
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
function couponForm() {
    return {
        type: '{{ old('type', 'percentage') }}',
        applicableTo: '{{ old('applicable_to', 'all') }}',
        generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = code;
        }
    }
}
</script>
@endpush
@endsection
