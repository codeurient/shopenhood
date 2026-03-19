@extends('admin.layouts.app')

@section('title', 'Edit Blog Post')
@section('page-title', 'Blog Posts')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.blog-posts.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Blog Posts
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Blog Post</h2>
        </div>
        <form action="{{ route('admin.blog-posts.destroy', $blogPost) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button"
                    @click="$dispatch('open-confirm-modal', { message: 'Delete this blog post? This cannot be undone.', form: $el.closest('form') })"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#C0392B] text-white text-[13px] font-semibold rounded hover:bg-[#a93226] transition">
                <i class="fa-solid fa-trash text-xs"></i>
                Delete Post
            </button>
        </form>
    </div>

    <form action="{{ route('admin.blog-posts.update', $blogPost) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-newspaper text-[#D4AF37]"></i>
                    Post Content
                </h3>
            </div>
            <div class="p-5 space-y-5">

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $blogPost->title) }}" required
                           class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3">
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Excerpt</label>
                    <textarea name="excerpt" rows="2"
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37]">{{ old('excerpt', $blogPost->excerpt) }}</textarea>
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="16" required
                              class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border border-[#E0E0E0] focus:ring-0 focus:border-[#D4AF37] font-mono">{{ old('content', $blogPost->content) }}</textarea>
                </div>

                <div>
                    <label class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Featured Image</label>
                    @if($blogPost->featured_image_path)
                        <div class="mb-3">
                            <img src="{{ Storage::url($blogPost->featured_image_path) }}"
                                 alt="Current featured image" class="h-32 w-auto rounded-[6px] object-cover border border-[#E0E0E0]">
                            <p class="text-[12px] text-[#37474F] mt-1">Current image — upload a new one to replace it</p>
                        </div>
                    @endif
                    <input type="file" name="featured_image" accept="image/*"
                           class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-white focus:outline-none focus:border-[#D4AF37] p-0">
                </div>

                <!-- Published Toggle -->
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <label for="is_published" class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                               class="sr-only peer"
                               {{ old('is_published', $blogPost->is_published) ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                    </label>
                    <div>
                        <p class="text-[13px] font-medium text-[#1A1A1A]">Published</p>
                        @if($blogPost->published_at)
                            <p class="text-[12px] text-[#37474F]">First published {{ $blogPost->published_at->format('M d, Y') }}</p>
                        @endif
                    </div>
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
                <i class="fa-solid fa-check text-xs"></i>
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
