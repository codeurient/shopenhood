@extends('admin.layouts.app')

@section('title', "Edit Page — {$page->title}")
@section('page-title', 'Pages')

@section('content')
<div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.pages.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Pages
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $page->title }}</h2>
            <p class="text-[12px] text-[#37474F] mt-0.5">Key: <span class="font-mono">{{ $page->key }}</span></p>
        </div>
        @if($page->is_published)
        <a href="{{ route('pages.show', $page->key) }}" target="_blank"
           class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-medium rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            View Live
        </a>
        @endif
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-[#D4AF37]"></i>
                    Page Details
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">URL Key</label>
                    <input type="text" value="{{ $page->key }}" disabled
                           class="h-[34px] border border-[#E0E0E0] text-[#37474F] text-[13px] rounded block w-full px-3 bg-gray-50 cursor-not-allowed">
                    <p class="text-[12px] text-[#37474F] mt-1">URL: <span class="font-mono">/pages/{{ $page->key }}</span></p>
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Page Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Content</label>
                    <textarea name="content" rows="16"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37] font-mono resize-y">{{ old('content', $page->content) }}</textarea>
                    <p class="text-[12px] text-[#37474F] mt-1">HTML is supported.</p>
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Meta Description</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}"
                           maxlength="255" placeholder="Used for SEO..."
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                    <p class="text-[12px] text-[#37474F] mt-1">Max 255 characters.</p>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <label for="is_published" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                               class="sr-only peer"
                               {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                    </label>
                    <p class="text-[13px] font-medium text-[#1A1A1A]">Visible on site</p>
                </div>

            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.pages.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
