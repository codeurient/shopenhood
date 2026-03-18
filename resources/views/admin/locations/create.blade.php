@extends('admin.layouts.app')

@section('title', 'Add Country')
@section('page-title', 'Add Country')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Add Country</h2>
            <p class="text-[#37474F] text-sm mt-1">Create a new country for listing locations</p>
        </div>
        <a href="{{ route('admin.locations.index') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Locations
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
            <div>
                <h3 class="text-[13px] font-semibold text-red-800">There were errors with your submission</h3>
                <ul class="mt-1 text-[13px] text-red-700 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <form action="{{ route('admin.locations.store') }}" method="POST">
            @csrf

            <div class="p-6 space-y-5">

                <!-- Country Name -->
                <div>
                    <label for="name" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Country Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('name') border-red-500 @else border-[#E0E0E0] @enderror"
                           placeholder="e.g., Azerbaijan">
                    @error('name')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country Code -->
                <div>
                    <label for="code" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                        Country Code
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" maxlength="10"
                           class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('code') border-red-500 @else border-[#E0E0E0] @enderror"
                           placeholder="e.g., AZ, TR, US">
                    <p class="mt-1 text-[12px] text-[#37474F]">Optional ISO country code</p>
                    @error('code')
                        <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">Active</span>
                    </label>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-[#E0E0E0] flex justify-end gap-3">
                <a href="{{ route('admin.locations.index') }}"
                   class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Create Country
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
