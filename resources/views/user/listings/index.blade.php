<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Listings</h2>
            @php
                $listingService = app(\App\Services\ListingService::class);
                $remaining = $listingService->getRemainingListingSlots($user);
                $limit = $user->getListingLimit();
            @endphp
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    @if($limit === null)
                        Unlimited listings
                    @else
                        {{ $activeListings->count() }}/{{ $limit }} active
                    @endif
                </span>
                @if($remaining === null || $remaining > 0)
                    <a href="{{ route('user.listings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        Create Listing
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Active Listings --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Active Listings</h3>

                    @if($activeListings->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-sm">You don't have any listings yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($activeListings as $listing)
                            <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="w-16 h-16 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden">
                                    @if($listing->primaryImage)
                                        <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No img</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $listing->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-medium
                                            {{ $listing->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $listing->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                            {{ $listing->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                            {{ $listing->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        ">{{ ucfirst($listing->status) }}</span>
                                        @if($listing->category)
                                            <span>{{ $listing->category->name }}</span>
                                        @endif
                                        @if($listing->expires_at)
                                            <span>Expires: {{ $listing->expires_at->format('M d, Y') }}</span>
                                        @endif
                                        <span>{{ $listing->is_visible ? 'Visible' : 'Hidden' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <form method="POST" action="{{ route('user.listings.toggle', $listing) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-xs rounded border {{ $listing->is_visible ? 'border-yellow-400 text-yellow-600 hover:bg-yellow-50' : 'border-green-400 text-green-600 hover:bg-green-50' }}">
                                            {{ $listing->is_visible ? 'Hide' : 'Show' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('user.listings.edit', $listing) }}" class="px-3 py-1 text-xs rounded border border-blue-400 text-blue-600 hover:bg-blue-50">Edit</a>
                                    <form method="POST" action="{{ route('user.listings.destroy', $listing) }}" onsubmit="return confirm('Delete this listing?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-xs rounded border border-red-400 text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Trashed Listings --}}
            @if($trashedListings->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Deleted Listings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Deleted listings are permanently removed after {{ $retentionDays }} days. You can reshare them before that.
                    </p>

                    <div class="space-y-4">
                        @foreach($trashedListings as $listing)
                        @php
                            $daysRemaining = max(0, $retentionDays - (int) $listing->deleted_at->diffInDays(now()));
                        @endphp
                        <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 opacity-75">
                            <div class="w-16 h-16 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded overflow-hidden">
                                @if($listing->primaryImage)
                                    <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}" class="w-full h-full object-cover grayscale" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No img</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-600 dark:text-gray-400 truncate">{{ $listing->title }}</h4>
                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="text-red-500">{{ $daysRemaining }} days before permanent deletion</span>
                                    <span>Deleted: {{ $listing->deleted_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('user.listings.reshare', $listing) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs rounded border border-green-400 text-green-600 hover:bg-green-50 font-medium">Reshare</button>
                                </form>
                                @if($user->isBusinessUser())
                                <form method="POST" action="{{ route('user.listings.force-destroy', $listing) }}" onsubmit="return confirm('Permanently delete? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs rounded border border-red-400 text-red-600 hover:bg-red-50">Permanently Delete</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
