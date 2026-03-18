<x-guest-layout>
    <x-slot name="title">Blog — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">Read the latest news, tips, and updates from {{ config('app.name') }}.</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Blog</h1>
            <p class="mt-1 text-gray-500">News, tips, and updates from {{ config('app.name') }}.</p>
        </div>

        @if($posts->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
                No posts published yet. Check back soon.
            </div>
        @else
            <div class="space-y-4">
                @foreach($posts as $post)
                    <a href="{{ route('blog.show', $post) }}"
                       class="flex gap-5 bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-sm transition-all group">
                        @if($post->featured_image_path)
                            <img src="{{ Storage::url($post->featured_image_path) }}"
                                 alt="{{ $post->title }}"
                                 class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-24 h-24 bg-primary-50 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <svg class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </p>
                            @if($post->excerpt)
                                <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $post->excerpt }}</p>
                            @endif
                            <p class="mt-2 text-xs text-gray-400">{{ $post->published_at->format('d M Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @endif

    </div>
</x-guest-layout>
