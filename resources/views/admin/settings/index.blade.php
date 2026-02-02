@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-3xl">
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Listing Settings --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Listing Settings</h2>

            <div class="space-y-4">
                <div>
                    <label for="listing_default_duration_days" class="block text-sm font-medium text-gray-700 mb-1">
                        Default Listing Duration (days)
                    </label>
                    <input type="number"
                           id="listing_default_duration_days"
                           name="listing_default_duration_days"
                           value="{{ old('listing_default_duration_days', $settings['listing_default_duration_days']) }}"
                           min="1" max="365"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">How long a listing stays active before expiring. Applies to normal users and as the default for business users.</p>
                    @error('listing_default_duration_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="listing_soft_delete_retention_days" class="block text-sm font-medium text-gray-700 mb-1">
                        Soft Delete Retention (days)
                    </label>
                    <input type="number"
                           id="listing_soft_delete_retention_days"
                           name="listing_soft_delete_retention_days"
                           value="{{ old('listing_soft_delete_retention_days', $settings['listing_soft_delete_retention_days']) }}"
                           min="1" max="365"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">How long soft-deleted listings are kept before permanent removal. Users can reshare during this period.</p>
                    @error('listing_soft_delete_retention_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
