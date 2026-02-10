@extends('admin.layouts.app')

@section('title', 'Edit Coupon')
@section('page-title', 'Edit Coupon')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Edit Coupon: {{ $coupon->code }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            &larr; Back to Coupons
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Usage Stats --}}
    @if($coupon->usages_count > 0)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                This coupon has been used <strong>{{ $coupon->usages_count }}</strong> time(s).
            </p>
        </div>
    @endif

    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" x-data="couponForm()">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">

            {{-- Code --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Coupon Code <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="code" id="code" required
                           value="{{ old('code', $coupon->code) }}"
                           placeholder="e.g., SUMMER2026"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 uppercase">
                    <button type="button" @click="generateCode()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                        Generate
                    </button>
                </div>
            </div>

            {{-- Type & Value --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Discount Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" x-model="type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Value <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500" x-text="type === 'percentage' ? '%' : '$'"></span>
                        <input type="number" name="value" required step="0.01" min="0.01"
                               :max="type === 'percentage' ? 100 : ''"
                               value="{{ old('value', $coupon->value) }}"
                               placeholder="0.00"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <p class="text-sm text-gray-500 mt-1" x-show="type === 'percentage'">Maximum 100%</p>
                </div>
            </div>

            {{-- Purchase & Discount Limits --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Purchase Amount</label>
                    <input type="number" name="min_purchase_amount" step="0.01" min="0"
                           value="{{ old('min_purchase_amount', $coupon->min_purchase_amount) }}"
                           placeholder="No minimum"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Discount Amount</label>
                    <input type="number" name="max_discount_amount" step="0.01" min="0"
                           value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                           placeholder="No limit"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">Cap the discount at this amount (useful for percentage coupons)</p>
                </div>
            </div>

            {{-- Usage Limits --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Usage Limit</label>
                    <input type="number" name="usage_limit" min="1"
                           value="{{ old('usage_limit', $coupon->usage_limit) }}"
                           placeholder="Unlimited"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">Currently used {{ $coupon->usage_count }} time(s)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per User Limit</label>
                    <input type="number" name="per_user_limit" min="1"
                           value="{{ old('per_user_limit', $coupon->per_user_limit) }}"
                           placeholder="Unlimited"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="datetime-local" name="expires_at"
                           value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Applicable To --}}
            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Applicable To <span class="text-red-500">*</span>
                </label>
                <select name="applicable_to" x-model="applicableTo" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ old('applicable_to', $coupon->applicable_to) === 'all' ? 'selected' : '' }}>All Products</option>
                    <option value="categories" {{ old('applicable_to', $coupon->applicable_to) === 'categories' ? 'selected' : '' }}>Specific Categories</option>
                    <option value="listings" {{ old('applicable_to', $coupon->applicable_to) === 'listings' ? 'selected' : '' }}>Specific Listings</option>
                    <option value="users" {{ old('applicable_to', $coupon->applicable_to) === 'users' ? 'selected' : '' }}>Specific Users</option>
                </select>
            </div>

            {{-- Restrictions: Categories --}}
            <div x-show="applicableTo === 'categories'" x-transition class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Categories</label>
                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 space-y-2">
                    @foreach($categories as $category)
                        <label class="flex items-center">
                            <input type="checkbox" name="restrictions[]" value="{{ $category->id }}"
                                   {{ in_array($category->id, old('restrictions', $existingRestrictionIds)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Restrictions: Users --}}
            <div x-show="applicableTo === 'users'" x-transition class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 space-y-2">
                    @foreach($users as $user)
                        <label class="flex items-center">
                            <input type="checkbox" name="restrictions[]" value="{{ $user->id }}"
                                   {{ in_array($user->id, old('restrictions', $existingRestrictionIds)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $user->name }} ({{ $user->email }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Restrictions: Listings --}}
            <div x-show="applicableTo === 'listings'" x-transition class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Listings</label>
                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 space-y-2">
                    @foreach($listings as $listing)
                        <label class="flex items-center">
                            <input type="checkbox" name="restrictions[]" value="{{ $listing->id }}"
                                   {{ in_array($listing->id, old('restrictions', $existingRestrictionIds)) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $listing->title }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                          placeholder="Internal note or description for this coupon"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $coupon->description) }}</textarea>
            </div>

            {{-- Active --}}
            <div class="border-t pt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
                <p class="text-sm text-gray-500 ml-6">Inactive coupons cannot be used at checkout</p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
