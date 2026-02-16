@extends('admin.layouts.app')

@section('title', 'Edit Listing Type')
@section('page-title', 'Edit Listing Type')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Edit Listing Type</h2>
        <a href="{{ route('admin.listing-types.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Back to Listing Types
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

    <form action="{{ route('admin.listing-types.update', $listingType) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $listingType->name) }}"
                       placeholder="e.g., Sell, Buy, Gift, Barter, Auction"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <p class="text-sm text-gray-500 mt-1">The display name of the listing type</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug"
                       value="{{ old('slug', $listingType->slug) }}"
                       placeholder="Auto-generated from name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <p class="text-sm text-gray-500 mt-1">URL-friendly version (auto-generated if left empty)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Brief description of this listing type"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('description', $listingType->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Emoji or HTML)</label>
                <input type="text" name="icon"
                       value="{{ old('icon', $listingType->icon) }}"
                       placeholder="e.g., 💰 or <i class='fas fa-tag'></i>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <p class="text-sm text-gray-500 mt-1">Optional icon to display with the listing type</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order"
                       value="{{ old('sort_order', $listingType->sort_order) }}"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <p class="text-sm text-gray-500 mt-1">Display order (lower numbers appear first)</p>
            </div>

            <div class="border-t pt-6">
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_price" value="1" {{ old('requires_price', $listingType->requires_price) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Requires Price</span>
                    </label>
                    <p class="text-sm text-gray-500 ml-6">Check if listings of this type must have a price (e.g., Sell, Auction)</p>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $listingType->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <p class="text-sm text-gray-500 ml-6">Inactive types won't be available when creating listings</p>
                </div>
            </div>

            @if($listingType->listings_count > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            ⚠️
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This listing type has <strong>{{ $listingType->listings_count }}</strong> listing(s) associated with it.
                                Be careful when making changes.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.listing-types.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                💾 Update Listing Type
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    const originalSlug = '{{ $listingType->slug }}';

    if (slugInput.value === originalSlug || slugInput.dataset.autoGenerated === 'true') {
        slugInput.value = this.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.dataset.autoGenerated = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    delete this.dataset.autoGenerated;
});
</script>
@endpush
@endsection
