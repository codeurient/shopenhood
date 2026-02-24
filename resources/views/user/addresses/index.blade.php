<x-guest-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Addresses</h2>
            <a href="{{ route('user.addresses.create') }}" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
                + Add Address
            </a>
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

            {{-- Addresses Grid --}}
            @if($addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($addresses as $address)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 relative {{ $address->is_default ? 'ring-2 ring-primary-500' : '' }}">
                    {{-- Default Badge --}}
                    @if($address->is_default)
                    <span class="absolute top-4 right-4 px-2 py-1 text-xs font-semibold rounded bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300">
                        Default
                    </span>
                    @endif

                    {{-- Label --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ $address->label }}
                        </span>
                    </div>

                    {{-- Recipient Info --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                        {{ $address->recipient_name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {{ $address->phone }}
                        @if($address->email)
                            <br>{{ $address->email }}
                        @endif
                    </p>

                    {{-- Address --}}
                    <div class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                        <p>{{ $address->street }}</p>
                        @if($address->building || $address->apartment)
                            <p>
                                @if($address->building)Bldg: {{ $address->building }}@endif
                                @if($address->building && $address->apartment), @endif
                                @if($address->apartment)Apt: {{ $address->apartment }}@endif
                            </p>
                        @endif
                        @if($address->district)
                            <p>{{ $address->district }}</p>
                        @endif
                        <p>{{ $address->city }}, {{ $address->country }}</p>
                        @if($address->postal_code)
                            <p>{{ $address->postal_code }}</p>
                        @endif
                    </div>

                    {{-- Additional Notes --}}
                    @if($address->additional_notes)
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4 italic">
                        {{ Str::limit($address->additional_notes, 100) }}
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('user.addresses.edit', $address) }}"
                           class="px-3 py-1 bg-primary-500 text-white rounded hover:bg-primary-600 text-xs transition">
                            Edit
                        </a>

                        @if(!$address->is_default)
                        <form action="{{ route('user.addresses.set-default', $address) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-500 text-xs transition">
                                Set Default
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this address?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No addresses yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">Add an address to use during checkout</p>
                    <a href="{{ route('user.addresses.create') }}" class="text-primary-600 dark:text-primary-300 hover:underline">
                        Add your first address
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-guest-layout>
