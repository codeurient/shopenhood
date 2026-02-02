<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Listing</h2>
            <a href="{{ route('user.listings.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to My Listings</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('user.listings.update', $listing) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            {{-- Status Info --}}
                            <div class="p-3 rounded-lg text-sm
                                {{ $listing->status === 'active' ? 'bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $listing->status === 'pending' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $listing->status === 'rejected' ? 'bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-200' : '' }}
                            ">
                                Status: <strong>{{ ucfirst($listing->status) }}</strong>
                                @if($listing->expires_at)
                                    | Expires: {{ $listing->expires_at->format('M d, Y H:i') }}
                                @endif
                                @if($listing->rejection_reason)
                                    <div class="mt-1">Rejection reason: {{ $listing->rejection_reason }}</div>
                                @endif
                                <div class="mt-1 text-xs opacity-75">Editing will reset the status to pending for review.</div>
                            </div>

                            {{-- Listing Type --}}
                            <div>
                                <label for="listing_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Listing Type *</label>
                                <select name="listing_type_id" id="listing_type_id" required
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($listingTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('listing_type_id', $listing->listing_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->icon }} {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('listing_type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                                <select name="category_id" id="category_id" required
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $listing->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $listing->title) }}" required maxlength="255"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description *</label>
                                <textarea name="description" id="description" rows="5" required
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $listing->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Short Description --}}
                            <div>
                                <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                                <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $listing->short_description) }}" maxlength="500"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- Price --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="base_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price</label>
                                    <input type="number" name="base_price" id="base_price" value="{{ old('base_price', $listing->base_price) }}" step="0.01" min="0"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                                    <input type="text" name="currency" id="currency" value="{{ old('currency', $listing->currency ?? 'USD') }}" maxlength="3"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            {{-- Negotiable --}}
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_negotiable" id="is_negotiable" value="1" {{ old('is_negotiable', $listing->is_negotiable) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="is_negotiable" class="text-sm text-gray-700 dark:text-gray-300">Price is negotiable</label>
                            </div>

                            {{-- Location --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                                    <input type="text" name="country" id="country" value="{{ old('country', $listing->country) }}" maxlength="100"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $listing->city) }}" maxlength="100"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            {{-- Existing Images --}}
                            @if($listing->images->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Images</label>
                                <div class="grid grid-cols-4 gap-3">
                                    @foreach($listing->images as $image)
                                    <div class="relative border rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-24 object-cover" alt="">
                                        <label class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="rounded border-white text-red-500 focus:ring-red-500">
                                            Delete
                                        </label>
                                        @if($image->is_primary)
                                            <span class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-1 rounded">Primary</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- New Images --}}
                            <div>
                                <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add More Images</label>
                                <input type="file" name="images[]" id="images" multiple accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                                <a href="{{ route('user.listings.index') }}" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300">Cancel</a>
                                <button type="submit" class="px-6 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 font-medium">Update Listing</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
