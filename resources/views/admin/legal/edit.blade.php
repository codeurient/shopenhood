@extends('admin.layouts.app')

@section('title', 'Edit Policy')
@section('page-title', 'Legal & Policies')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.legal.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Legal & Policies
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Policy</h2>
            <p class="text-[13px] text-[#37474F] mt-0.5">{{ $policy->title }} &mdash; v{{ $policy->version }}</p>
        </div>
        <a href="{{ route('admin.legal.show', $policy) }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-eye text-xs"></i>
            View Details &amp; Acceptances
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="text-[13px] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.legal.update', $policy) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Version Bump -->
        <div class="bg-yellow-50 border border-yellow-300 rounded-[6px] p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="bump_version" value="1"
                       class="mt-0.5 rounded border-yellow-300 text-yellow-600"
                       id="bump_version"
                       onchange="document.getElementById('new_version_row').classList.toggle('hidden', !this.checked)">
                <div>
                    <span class="text-[13px] font-semibold text-yellow-800">Bump to a new version</span>
                    <p class="text-[12px] text-yellow-700 mt-0.5">
                        Checking this will archive the current content (v{{ $policy->version }}) as a historical version.
                        Users who accepted v{{ $policy->version }} will need to re-accept the updated policy.
                    </p>
                </div>
            </label>
            <div id="new_version_row" class="mt-3 hidden">
                <label class="block text-[12px] font-medium text-yellow-800 mb-1">New Version Number</label>
                <input type="text" name="version" value="{{ old('version', $policy->version) }}"
                       class="h-[34px] border border-yellow-300 text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-yellow-500 px-3 w-40"
                       placeholder="e.g. 2.0">
            </div>
        </div>

        @include('admin.legal._form', ['policy' => $policy])

        <div class="flex justify-between">
            <a href="{{ route('admin.legal.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-check text-xs"></i>
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
