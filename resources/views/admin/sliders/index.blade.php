@extends('admin.layouts.app')

@section('title', 'Sliders & Banners Management')
@section('page-title', 'Sliders & Banners')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Sliders & Banners</h2>
            <p class="text-gray-600 mt-1">Manage homepage slider and banner images</p>
        </div>
        <a href="{{ route('admin.sliders.create') }}" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
            ➕ Create Slider/Banner
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">🖼️</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-gray-600 text-sm">Total Sliders</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 text-2xl">🎬</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['active_sliders'] }}</p>
                    <p class="text-gray-600 text-sm">Active Main Slides</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 text-2xl">🏷️</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['active_banners'] }}</p>
                    <p class="text-gray-600 text-sm">Active Banners</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('admin.sliders.index') }}" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Types</option>
                    <option value="main_slider" {{ request('type') === 'main_slider' ? 'selected' : '' }}>Main Slider</option>
                    <option value="banner_small" {{ request('type') === 'banner_small' ? 'selected' : '' }}>Small Banner</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                    Filter
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Sliders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sliders as $slider)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="h-16 w-24 object-cover rounded">
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $slider->title }}</div>
                        @if($slider->subtitle)
                            <div class="text-sm text-gray-500">{{ Str::limit($slider->subtitle, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($slider->type === 'main_slider')
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Main Slider</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded">Small Banner</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($slider->link)
                            <a href="{{ $slider->link }}" target="_blank" class="text-sm text-primary-600 hover:underline">
                                {{ Str::limit($slider->link, 30) }}
                            </a>
                        @else
                            <span class="text-sm text-gray-400">No link</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $slider->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($slider->is_active)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-6xl mb-4">🖼️</div>
                        <p class="text-lg">No sliders found</p>
                        <p class="text-sm mt-2">Create your first slider to get started</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($sliders->hasPages())
        <div class="mt-6">
            {{ $sliders->links() }}
        </div>
    @endif
</div>
@endsection
