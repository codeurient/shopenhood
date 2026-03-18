@extends('admin.layouts.app')

@section('title', 'Edit Blog Post')
@section('page-title', 'Blog Posts')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.blog-posts.index') }}" class="text-gray-500 hover:text-gray-700">
            ← Back to Blog Posts
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Edit Blog Post</h2>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.blog-posts.update', $blogPost) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $blogPost->title) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                <textarea name="excerpt" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('excerpt', $blogPost->excerpt) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                <textarea name="content" rows="16" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ old('content', $blogPost->content) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                @if($blogPost->featured_image_path)
                    <div class="mb-3">
                        <img src="{{ Storage::url($blogPost->featured_image_path) }}"
                             alt="Current featured image" class="h-32 w-auto rounded-lg object-cover">
                        <p class="text-xs text-gray-500 mt-1">Current image — upload a new one to replace it</p>
                    </div>
                @endif
                <input type="file" name="featured_image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                       {{ old('is_published', $blogPost->is_published) ? 'checked' : '' }}>
                <label for="is_published" class="text-sm font-medium text-gray-700">Published</label>
                @if($blogPost->published_at)
                    <span class="text-xs text-gray-400">(first published {{ $blogPost->published_at->format('M d, Y') }})</span>
                @endif
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                    Save Changes
                </button>
                <a href="{{ route('admin.blog-posts.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
            <form action="{{ route('admin.blog-posts.destroy', $blogPost) }}" method="POST"
                  onsubmit="return confirm('Delete this blog post? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Delete Post
                </button>
            </form>
        </div>
    </form>
</div>
@endsection
