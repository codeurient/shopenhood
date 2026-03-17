@extends('admin.layouts.app')

@section('title', 'Edit Policy')
@section('page-title', 'Legal & Policies')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.legal.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Policy</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $policy->title }} &mdash; v{{ $policy->version }}</p>
            </div>
        </div>
        <a href="{{ route('admin.legal.show', $policy) }}"
           class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
            View Details &amp; Acceptances
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.legal.update', $policy) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Version bump option --}}
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="bump_version" value="1" class="mt-0.5 rounded border-gray-300 text-amber-600"
                       id="bump_version" onchange="document.getElementById('new_version_row').classList.toggle('hidden', !this.checked)">
                <div>
                    <span class="font-semibold text-amber-800 text-sm">Bump to a new version</span>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Checking this will archive the current content (v{{ $policy->version }}) as a historical version.
                        Users who accepted v{{ $policy->version }} will need to re-accept the updated policy.
                    </p>
                </div>
            </label>
            <div id="new_version_row" class="mt-3 hidden">
                <label class="block text-xs font-medium text-amber-800 mb-1">New Version Number</label>
                <input type="text" name="version" value="{{ old('version', $policy->version) }}"
                       class="px-3 py-2 border border-amber-300 rounded-lg text-sm w-40 focus:outline-none focus:ring-2 focus:ring-amber-400"
                       placeholder="e.g. 2.0">
            </div>
        </div>

        @include('admin.legal._form', ['policy' => $policy])

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium transition">
                Save Changes
            </button>
            <a href="{{ route('admin.legal.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
