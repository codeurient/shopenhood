@extends('admin.layouts.app')

@section('title', 'Create Blog Post')
@section('page-title', 'Blog Posts')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.blog-posts.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Blog Posts
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Create Blog Post</h2>
        </div>
    </div>

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

    <form action="{{ route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-newspaper text-[#D4AF37]"></i>
                    Post Content
                </h3>
            </div>
            <div class="p-4 space-y-4">

                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                </div>

                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Excerpt</label>
                    <textarea name="excerpt" rows="2"
                              placeholder="Short summary shown in post lists..."
                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2">{{ old('excerpt') }}</textarea>
                </div>

                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="16" required
                              class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 font-mono">{{ old('content') }}</textarea>
                </div>

                <div>
                    <label class="block text-[12px] font-medium text-[#37474F] mb-1">Featured Image</label>
                    <input type="file" name="featured_image" accept="image/*"
                           class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-white focus:outline-none focus:border-[#D4AF37] p-0">
                </div>

                <!-- Published Toggle -->
                <div class="flex items-center justify-between py-2 border-t border-[#E0E0E0]">
                    <div>
                        <p class="text-[13px] font-medium text-[#1A1A1A]">Publish immediately</p>
                        <p class="text-[12px] text-[#37474F]">Make this post visible to the public right away</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                               class="sr-only peer"
                               {{ old('is_published') ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-[#D4AF37] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>

            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.blog-posts.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Create Post
            </button>
        </div>

    </form>

</div>
@endsection
