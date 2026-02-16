@extends('admin.layouts.app')

@section('title', $country->name . ' Cities')
@section('page-title', $country->name . ' Cities')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">{{ $country->name }} — Cities</h2>
            <p class="text-gray-600 mt-1">
                @if($country->code)
                    Code: <strong>{{ $country->code }}</strong> |
                @endif
                Status: <span class="{{ $country->is_active ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $country->is_active ? 'Active' : 'Inactive' }}</span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.locations.cities.create', $country) }}" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
                ➕ Add City
            </a>
            <a href="{{ route('admin.locations.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                &larr; Back to Countries
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            ✗ {{ session('error') }}
        </div>
    @endif

    <!-- Cities Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($cities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cities as $index => $city)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $cities->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $city->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($city->code)
                                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $city->code }}</code>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.locations.cities.toggle-status', [$country, $city->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs rounded {{ $city->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                            {{ $city->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.locations.cities.edit', [$country, $city->id]) }}" class="text-primary-600 hover:text-primary-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.locations.cities.destroy', [$country, $city->id]) }}" method="POST" onsubmit="return confirm('Delete {{ $city->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $cities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🏙️</div>
                <p class="text-gray-500 text-lg">No cities added for {{ $country->name }}</p>
                <a href="{{ route('admin.locations.cities.create', $country) }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                    Add the first city
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
