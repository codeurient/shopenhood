<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Listings</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('user.listings.create') }}" 
                    class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
                    + Create Listing
                </a>
            </div>

            <!-- Active Listings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mt-4"
                 x-data="{ selectedActiveIds: [] }">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-4 flex-wrap">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Active Listings ({{ $activeListings->count() }})</h3>
                    <div class="flex items-center gap-2" x-show="selectedActiveIds.length > 0" style="display: none;">
                        <form action="{{ route('user.listings.bulk-destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedActiveIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', { message: 'Delete selected listings?', form: $el.closest('form') })"
                                class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium transition">
                                Delete Selected
                            </button>
                        </form>
                    </div>
                </div>

                @if($activeListings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox"
                                           @change="selectedActiveIds = $event.target.checked ? [{{ $activeListings->pluck('id')->join(',') }}] : []"
                                           class="rounded border-gray-300 dark:border-gray-600">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Listing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($activeListings as $listing)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-4">
                                    <input type="checkbox" :value="{{ $listing->id }}" x-model="selectedActiveIds"
                                           class="rounded border-gray-300 dark:border-gray-600">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($listing->primaryImage)
                                            <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}"
                                                 alt="{{ $listing->title }}"
                                                 class="w-12 h-12 rounded object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('user.listings.show', $listing) }}" class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-300">{{ $listing->title }}</a>
                                            @if($listing->listingType)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $listing->listingType->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $listing->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($listing->base_price)
                                        {{ $listing->currency ?? 'USD' }} {{ number_format($listing->base_price, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200',
                                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
                                            'active' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
                                            'sold' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                                            'expired' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
                                            'rejected' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="me-2 px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($listing->status) }}
                                    </span>
                                    @if($listing->status === 'rejected' && $listing->rejection_reason)
                                        <p class="text-xs text-red-500 dark:text-red-400 mt-1 max-w-xs truncate" title="{{ $listing->rejection_reason }}">{{ $listing->rejection_reason }}</p>
                                    @endif
                                    @if($listing->hidden_due_to_role_change)
                                        <span class="xl2:ms-2 mt-1 inline-block px-2 py-1 text-xs font-semibold rounded bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300">
                                            Hidden (role restriction)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('user.listings.edit', $listing) }}"
                                           class="px-3 py-1 bg-primary-500 text-white rounded hover:bg-primary-600 text-xs transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('user.listings.toggle', $listing) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-500 text-xs transition">
                                                {{ $listing->is_visible ? 'Hide' : 'Show' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('user.listings.destroy', $listing) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="$dispatch('open-confirm-modal', { message: 'Are you sure you want to delete this listing?', form: $el.closest('form') })"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

              

                @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No listings yet</p>
                    <a href="{{ route('user.listings.create') }}" class="text-primary-600 dark:text-primary-300 hover:underline">
                        Create your first listing
                    </a>
                </div>
                @endif
            </div>




            <!-- Trashed Listings -->
            @if($trashedListings->count() > 0)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
                 x-data="{ selectedTrashedIds: [] }">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Deleted Listings ({{ $trashedListings->count() }})
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Deleted listings are kept for {{ $retentionDays }} days before being permanently removed.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Permanently Delete Selected --}}
                        <form action="{{ route('user.listings.bulk-force-destroy-trashed') }}" method="POST"
                              x-show="selectedTrashedIds.length > 0" style="display: none;">
                            @csrf
                            <template x-for="id in selectedTrashedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', { message: 'Permanently delete selected listings? This cannot be undone.', form: $el.closest('form') })"
                                class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium transition">
                                Permanently Delete Selected
                            </button>
                        </form>

                        {{-- Permanently Delete All --}}
                        <form action="{{ route('user.listings.force-destroy-all-trashed') }}" method="POST">
                            @csrf
                            <button type="button"
                                @click="$dispatch('open-confirm-modal', { message: 'Permanently delete ALL deleted listings? This cannot be undone.', form: $el.closest('form') })"
                                class="px-3 py-1.5 bg-red-700 text-white rounded hover:bg-red-800 text-xs font-medium transition">
                                Delete All Permanently
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox"
                                           @change="selectedTrashedIds = $event.target.checked ? [{{ $trashedListings->pluck('id')->join(',') }}] : []"
                                           class="rounded border-gray-300 dark:border-gray-600">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Listing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Deleted</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($trashedListings as $listing)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 opacity-75">
                                <td class="px-4 py-4">
                                    <input type="checkbox" :value="{{ $listing->id }}" x-model="selectedTrashedIds"
                                           class="rounded border-gray-300 dark:border-gray-600">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $listing->title }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $listing->category->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $listing->deleted_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <form action="{{ route('user.listings.reshare', $listing->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs transition">
                                                Reshare
                                            </button>
                                        </form>
                                        <form action="{{ route('user.listings.force-destroy', $listing->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="$dispatch('open-confirm-modal', { message: 'Permanently delete this listing? This cannot be undone.', form: $el.closest('form') })"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                                Permanently Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
