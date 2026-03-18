<x-guest-layout>
    <x-slot name="title">{{ $blogPost->title }} — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">{{ $blogPost->excerpt ?? Str::limit(strip_tags($blogPost->content), 160) }}</x-slot>

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">Home</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('blog.index') }}" class="hover:text-gray-600 transition-colors">Blog</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-600 truncate max-w-xs">{{ $blogPost->title }}</span>
        </nav>

        @if($blogPost->featured_image_path)
            <img src="{{ Storage::url($blogPost->featured_image_path) }}"
                 alt="{{ $blogPost->title }}"
                 class="w-full h-56 object-cover rounded-xl mb-6">
        @endif

        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $blogPost->title }}</h1>
        <p class="text-xs text-gray-400 mb-6">{{ $blogPost->published_at->format('d M Y') }}</p>

        @if($blogPost->excerpt)
            <p class="text-base text-gray-600 font-medium mb-6 border-l-4 border-primary-300 pl-4">{{ $blogPost->excerpt }}</p>
        @endif

        <div class="prose prose-sm max-w-none text-gray-700 space-y-4">
            {!! nl2br(e($blogPost->content)) !!}
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Blog
            </a>
        </div>

    </div>
</x-guest-layout>
