@extends('admin.layouts.app')

@section('title', 'Edit Coupon')
@section('page-title', 'Edit Coupon')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Coupons
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Coupon: <span class="font-mono">{{ $coupon->code }}</span></h2>
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

    @if($coupon->usages_count > 0)
        <div class="mb-5 p-4 bg-blue-50 border-l-4 border-blue-400 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-blue-500"></i>
            <p class="text-[13px] text-blue-800">
                This coupon has been used <strong>{{ $coupon->usages_count }}</strong> time(s).
            </p>
        </div>
    @endif

    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" x-data="couponForm()">
        @csrf
        @method('PUT')

        <!-- Code & Type -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-[#D4AF37]"></i>
                    Coupon Details
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <!-- Code -->
                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Coupon Code <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="code" id="code" required
                               value="{{ old('code', $coupon->code) }}"
                               placeholder="e.g., SUMMER2026"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] flex-1 px-3 uppercase font-mono">
                        <button type="button" @click="generateCode()"
                                class="inline-flex items-center gap-1.5 h-[34px] px-3 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                            <i class="fa-solid fa-shuffle text-xs"></i>
                            Generate
                        </button>
                    </div>
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                            Discount Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" x-model="type" required
                                class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                            <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                            Value <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#37474F] text-[13px]" x-text="type === 'percentage' ? '%' : '$'"></span>
                            <input type="number" name="value" required step="0.01" min="0.01"
                                   :max="type === 'percentage' ? 100 : ''"
                                   value="{{ old('value', $coupon->value) }}"
                                   placeholder="0.00"
                                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full pl-8 pr-3">
                        </div>
                        <p class="text-[12px] text-[#37474F] mt-1" x-show="type === 'percentage'">Maximum 100%</p>
                    </div>
                </div>

                <!-- Purchase & Discount Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Minimum Purchase Amount</label>
                        <input type="number" name="min_purchase_amount" step="0.01" min="0"
                               value="{{ old('min_purchase_amount', $coupon->min_purchase_amount) }}"
                               placeholder="No minimum"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Maximum Discount Amount</label>
                        <input type="number" name="max_discount_amount" step="0.01" min="0"
                               value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                               placeholder="No limit"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Cap the discount at this amount (useful for percentage coupons)</p>
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Total Usage Limit</label>
                        <input type="number" name="usage_limit" min="1"
                               value="{{ old('usage_limit', $coupon->usage_limit) }}"
                               placeholder="Unlimited"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <p class="text-[12px] text-[#37474F] mt-1">Currently used {{ $coupon->usage_count }} time(s)</p>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Per User Limit</label>
                        <input type="number" name="per_user_limit" min="1"
                               value="{{ old('per_user_limit', $coupon->per_user_limit) }}"
                               placeholder="Unlimited"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Start Date</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Expiry Date</label>
                        <input type="datetime-local" name="expires_at"
                               value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    </div>
                </div>

            </div>
        </div>

        <!-- Applicable To -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-bullseye text-[#D4AF37]"></i>
                    Scope & Restrictions
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Applicable To <span class="text-red-500">*</span>
                    </label>
                    <select name="applicable_to" x-model="applicableTo" required
                            class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                        <option value="all" {{ old('applicable_to', $coupon->applicable_to) === 'all' ? 'selected' : '' }}>All Products</option>
                        <option value="categories" {{ old('applicable_to', $coupon->applicable_to) === 'categories' ? 'selected' : '' }}>Specific Categories</option>
                        <option value="listings" {{ old('applicable_to', $coupon->applicable_to) === 'listings' ? 'selected' : '' }}>Specific Listings</option>
                    </select>
                </div>

                <!-- Categories -->
                <div x-show="applicableTo === 'categories'" x-transition>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Select Categories</label>
                    <div class="max-h-60 overflow-y-auto border border-[#E0E0E0] rounded p-3 space-y-2">
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="restrictions[]" value="{{ $category->id }}"
                                       {{ in_array($category->id, old('restrictions', $existingRestrictionIds)) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0">
                                <span class="text-[13px] text-[#1A1A1A]">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Listings -->
                <div x-show="applicableTo === 'listings'" x-transition>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Select Listings</label>
                    <div class="max-h-60 overflow-y-auto border border-[#E0E0E0] rounded p-3 space-y-2">
                        @foreach($listings as $listing)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="restrictions[]" value="{{ $listing->id }}"
                                       {{ in_array($listing->id, old('restrictions', $existingRestrictionIds)) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0">
                                <span class="text-[13px] text-[#1A1A1A]">{{ $listing->title }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Internal note or description for this coupon"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]">{{ old('description', $coupon->description) }}</textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3">
                    <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                    </label>
                    <div>
                        <p class="text-[13px] font-medium text-[#1A1A1A]">Active</p>
                        <p class="text-[12px] text-[#37474F]">Inactive coupons cannot be used at checkout</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-check text-xs"></i>
                Update Coupon
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function couponForm() {
    return {
        type: '{{ old('type', $coupon->type) }}',
        applicableTo: '{{ old('applicable_to', $coupon->applicable_to) }}',
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
