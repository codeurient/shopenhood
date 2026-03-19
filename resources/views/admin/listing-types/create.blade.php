@extends('admin.layouts.app')

@section('title', 'Create Listing Type')
@section('page-title', 'Create Listing Type')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Create Listing Type</h2>
            <p class="text-[#37474F] text-sm mt-1">Add a new listing type (sell, buy, gift, barter, auction)</p>
        </div>
        <a href="{{ route('admin.listing-types.index') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Listing Types
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
            <div>
                <h3 class="text-[13px] font-semibold text-red-800">There were errors with your submission</h3>
                <ul class="mt-1 text-[13px] text-red-700 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.listing-types.store') }}" method="POST">
        @csrf

        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="p-6 space-y-5">

                <!-- Name -->
                <div>
                    <label for="name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name') }}"
                           placeholder="e.g., Sell, Buy, Gift, Barter, Auction"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('name') border-red-500 @else border-[#E0E0E0] @enderror">
                    <p class="mt-1 text-[12px] text-[#37474F]">The display name of the listing type</p>
                    @error('name')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Slug
                    </label>
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug') }}"
                           placeholder="Auto-generated from name"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('slug') border-red-500 @else border-[#E0E0E0] @enderror">
                    <p class="mt-1 text-[12px] text-[#37474F]">URL-friendly version (auto-generated if left empty)</p>
                    @error('slug')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Brief description of this listing type"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border focus:ring-0 focus:border-[#D4AF37] @error('description') border-red-500 @else border-[#E0E0E0] @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Icon <span class="text-[12px] font-normal text-[#37474F]">(Font Awesome HTML)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="icon" id="icon"
                               value="{{ old('icon') }}"
                               placeholder="e.g., <i class=&quot;fa-solid fa-tag&quot;></i>"
                               oninput="document.getElementById('icon-preview').innerHTML = this.value || '<i class=\'fa-solid fa-tag\'></i>'"
                               class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('icon') border-red-500 @else border-[#E0E0E0] @enderror">
                        <div id="icon-preview"
                             class="flex-shrink-0 w-[34px] h-[34px] flex items-center justify-center border border-[#E0E0E0] rounded bg-[#37474F] text-[#D4AF37] text-lg">
                            {!! old('icon') ?: '<i class="fa-solid fa-tag"></i>' !!}
                        </div>
                    </div>
                    <p class="mt-1 text-[12px] text-[#37474F]">Font Awesome icon HTML — preview updates live as you type</p>
                    @error('icon')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Sort Order
                    </label>
                    <input type="number" name="sort_order" id="sort_order"
                           value="{{ old('sort_order', 0) }}" min="0"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('sort_order') border-red-500 @else border-[#E0E0E0] @enderror">
                    <p class="mt-1 text-[12px] text-[#37474F]">Display order (lower numbers appear first)</p>
                    @error('sort_order')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Toggles -->
                <div class="pt-4 border-t border-[#E0E0E0] space-y-4">

                    <!-- Requires Price -->
                    <div class="flex items-start gap-4">
                        <label for="requires_price" class="inline-flex items-center gap-3 cursor-pointer mt-0.5">
                            <input type="checkbox" name="requires_price" id="requires_price" value="1"
                                   {{ old('requires_price', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        </label>
                        <div>
                            <p class="text-[13px] font-medium text-[#1A1A1A]">Requires Price</p>
                            <p class="text-[12px] text-[#37474F] mt-0.5">Listings of this type must have a price (e.g., Sell, Auction)</p>
                        </div>
                    </div>

                    <!-- Active -->
                    <div class="flex items-start gap-4">
                        <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer mt-0.5">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        </label>
                        <div>
                            <p class="text-[13px] font-medium text-[#1A1A1A]">Active</p>
                            <p class="text-[12px] text-[#37474F] mt-0.5">Inactive types won't be available when creating listings</p>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-[#E0E0E0] flex justify-end gap-3">
                <a href="{{ route('admin.listing-types.index') }}"
                   class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Create Listing Type
                </button>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
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
