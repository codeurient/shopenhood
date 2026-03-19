<x-guest-layout>
    <x-slot name="title">Blog — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">Read the latest news, tips, and updates from {{ config('app.name') }}.</x-slot>

    {{-- Hero --}}
    <div class="bg-[#000000] border-b border-[#37474F]">
        <div class="max-w-5xl mx-auto px-4 py-14 text-center">
            <p class="text-[#D4AF37] text-xs font-semibold uppercase tracking-[0.2em] mb-3">Journal</p>
            <h1 class="text-white font-bold" style="font-size:clamp(1.5rem,3vw,2.5rem);">Insights &amp; Updates</h1>
            <p class="mt-3 text-[#E0E0E0] text-sm max-w-md mx-auto">News, tips, and stories from the {{ config('app.name') }} community.</p>
        </div>
    </div>

    <div class="bg-[#FFFFFF] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 py-12">

            @if($posts->isEmpty())
                <div class="border border-[#E0E0E0] rounded-[8px] p-14 text-center">
                    <i class="fa-regular fa-newspaper text-3xl text-[#E0E0E0] mb-4 block"></i>
                    <p class="text-[#37474F] text-sm">No posts published yet. Check back soon.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($posts as $post)
                        <a href="{{ route('blog.show', $post) }}"
                           class="group flex flex-col border border-[#E0E0E0] rounded-[8px] overflow-hidden hover:border-[#D4AF37] transition-colors"
                           style="box-shadow: 0 1px 4px rgba(0,0,0,0.08);">
                            @if($post->featured_image_path)
                                <div class="h-44 overflow-hidden flex-shrink-0">
                                    <img src="{{ Storage::url($post->featured_image_path) }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @else
                                <div class="h-44 bg-[#000000] flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-pen-nib text-3xl text-[#D4AF37]"></i>
                                </div>
                            @endif
                            <div class="flex flex-col flex-1 p-5">
                                <p class="font-semibold text-[#1A1A1A] text-[0.8125rem] group-hover:text-[#D4AF37] transition-colors line-clamp-2 mb-2">
                                    {{ $post->title }}
                                </p>
                                @if($post->excerpt)
                                    <p class="text-[#37474F] text-xs line-clamp-3 mb-4">{{ $post->excerpt }}</p>
                                @endif
                                <div class="mt-auto flex items-center justify-between">
                                    <p class="text-[#37474F] text-[11px]">{{ $post->published_at->format('d M Y') }}</p>
                                    <span class="inline-flex items-center gap-1 text-[#D4AF37] text-[11px] font-semibold">
                                        Read <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif

        </div>
    </div>
</x-guest-layout>
