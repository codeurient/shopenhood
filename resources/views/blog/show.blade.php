<x-guest-layout>
    <x-slot name="title">{{ $blogPost->title }} — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">{{ $blogPost->excerpt ?? Str::limit(strip_tags($blogPost->content), 160) }}</x-slot>

    {{-- Hero --}}
    @if($blogPost->featured_image_path)
        <div class="relative h-64 md:h-80 overflow-hidden bg-[#000000]">
            <img src="{{ Storage::url($blogPost->featured_image_path) }}"
                 alt="{{ $blogPost->title }}"
                 class="w-full h-full object-cover opacity-50">
            <div class="absolute inset-0 flex items-end">
                <div class="max-w-3xl mx-auto w-full px-4 pb-8">
                    <h1 class="text-white font-bold leading-tight"
                        style="font-size:clamp(1.25rem,3vw,2rem); text-shadow:0 2px 8px rgba(0,0,0,0.6);">
                        {{ $blogPost->title }}
                    </h1>
                    <p class="text-[#D4AF37] text-xs font-semibold mt-2">{{ $blogPost->published_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-[#000000] border-b border-[#37474F]">
            <div class="max-w-3xl mx-auto px-4 py-12">
                <h1 class="text-white font-bold leading-tight" style="font-size:clamp(1.25rem,3vw,2rem);">
                    {{ $blogPost->title }}
                </h1>
                <p class="text-[#D4AF37] text-xs font-semibold mt-3">{{ $blogPost->published_at->format('d M Y') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-[#FFFFFF] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 py-10">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-[#37474F] mb-8">
                <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#E0E0E0]"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-[#D4AF37] transition-colors">Blog</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#E0E0E0]"></i>
                <span class="text-[#1A1A1A] truncate max-w-xs">{{ $blogPost->title }}</span>
            </nav>

            {{-- Excerpt pull-quote --}}
            @if($blogPost->excerpt)
                <div class="border-l-[3px] border-[#D4AF37] pl-4 mb-8 bg-[#FAFAF8] py-3 rounded-r-[4px]">
                    <p class="text-[#1A1A1A] text-sm font-medium leading-relaxed">{{ $blogPost->excerpt }}</p>
                </div>
            @endif

            {{-- Content --}}
            <div class="text-[#1A1A1A] text-sm leading-relaxed space-y-4">
                {!! nl2br(e($blogPost->content)) !!}
            </div>

            {{-- Footer divider + back link --}}
            <div class="mt-12 pt-6 border-t border-[#E0E0E0] flex items-center justify-between">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-semibold text-[#37474F] hover:text-[#D4AF37] transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Blog
                </a>
                <span class="text-[#E0E0E0] text-xs">{{ $blogPost->published_at->format('d M Y') }}</span>
            </div>

        </div>
    </div>
</x-guest-layout>
