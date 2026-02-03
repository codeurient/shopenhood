@extends('admin.layouts.app')

@section('title', 'Edit City')
@section('page-title', 'Edit City')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Edit City: {{ $city->name }}</h2>
        <a href="{{ route('admin.locations.cities.index', $country) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            &larr; Back to {{ $country->name }} Cities
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

    <div class="bg-white rounded-lg shadow p-6">
{{-- UPDATE CITY FORM --}}
<form action="{{ route('admin.locations.cities.update', [$country, $city->id]) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="space-y-6">

        {{-- City Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                City Name <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="name"
                id="name"
                required
                value="{{ old('name', $city->name) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg
                       focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- City Code --}}
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                City Code
            </label>

            <input
                type="text"
                name="code"
                id="code"
                maxlength="10"
                placeholder="Optional code"
                value="{{ old('code', $city->code) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg
                       focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Active --}}
        <div class="flex items-center gap-2">
            <input
                type="checkbox"
                name="is_active"
                id="is_active"
                value="1"
                {{ old('is_active', $city->is_active) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600
                       focus:ring-indigo-500">

            <label for="is_active" class="text-sm text-gray-700">
                Active
            </label>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="mt-6 flex justify-end gap-4">
        <a href="{{ route('admin.locations.cities.index', $country) }}"
           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            Cancel
        </a>

        <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            💾 Update City
        </button>
    </div>
</form>

{{-- DELETE CITY FORM --}}
<form action="{{ route('admin.locations.cities.destroy', [$country, $city->id]) }}"
      method="POST"
      class="mt-6"
      onsubmit="return confirm('Delete {{ $city->name }}?')">
    @csrf
    @method('DELETE')

    <button type="submit"
            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
        🗑️ Delete City
    </button>
</form>

    </div>
</div>
@endsection
