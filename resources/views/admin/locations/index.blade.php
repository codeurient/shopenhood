@extends('admin.layouts.app')

@section('title', 'Locations Management')
@section('page-title', 'Locations')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Locations (Countries)</h2>
            <p class="text-gray-600 mt-1">Manage countries and their cities for listing locations</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
            ➕ Add Country
        </a>
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

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">🌍</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_countries'] }}</p>
                    <p class="text-gray-600 text-sm">Total Countries</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 text-2xl">✓</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['active_countries'] }}</p>
                    <p class="text-gray-600 text-sm">Active Countries</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600 text-2xl">🏙️</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_cities'] }}</p>
                    <p class="text-gray-600 text-sm">Total Cities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by country name..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                🔍 Filter
            </button>
            <a href="{{ route('admin.locations.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                🔄 Reset
            </a>
        </form>
    </div>

    <!-- Countries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($countries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cities</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($countries as $country)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $country->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($country->code)
                                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $country->code }}</code>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.locations.cities.index', $country) }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                                        {{ $country->cities_count }} {{ Str::plural('city', $country->cities_count) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.locations.toggle-status', $country) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs rounded {{ $country->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                            {{ $country->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.locations.cities.create', $country) }}" class="text-green-600 hover:text-green-900">
                                            + City
                                        </a>
                                        <a href="{{ route('admin.locations.edit', $country) }}" class="text-primary-600 hover:text-primary-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.locations.destroy', $country) }}" method="POST" onsubmit="return confirm('Delete {{ $country->name }} and all its cities?')" class="inline">
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
                {{ $countries->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🌍</div>
                <p class="text-gray-500 text-lg">No countries found</p>
                <a href="{{ route('admin.locations.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                    Add your first country
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
