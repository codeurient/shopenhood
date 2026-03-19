<x-guest-layout>
    <x-slot name="title">{{ $page->title }} — {{ config('app.name') }}</x-slot>
    @if($page->meta_description)
        <x-slot name="metaDescription">{{ $page->meta_description }}</x-slot>
    @endif

    {{-- Hero Header --}}
    <div class="bg-[#000000] py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <nav class="flex items-center gap-2 text-xs text-[#37474F] mb-4 font-['Inter','Segoe_UI',sans-serif]">
                <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-[#E0E0E0]">{{ $page->title }}</span>
            </nav>
            <h1 class="font-bold text-[#FFFFFF] font-['Inter','Segoe_UI',sans-serif]"
                style="font-size: clamp(1.25rem, 2.5vw, 2rem);">
                {{ $page->title }}
            </h1>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-[#FFFFFF] min-h-screen py-10 px-4">
        <div class="max-w-3xl mx-auto flex flex-col gap-5">

            @if($page->content)
                <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] px-6 py-6 shadow-[0_1px_4px_rgba(0,0,0,0.08)]
                            prose prose-sm max-w-none
                            prose-headings:text-[#1A1A1A] prose-headings:font-semibold
                            prose-p:text-[#1A1A1A] prose-p:leading-relaxed
                            prose-a:text-[#000000] prose-a:font-semibold prose-a:no-underline hover:prose-a:underline
                            prose-li:text-[#1A1A1A]
                            prose-strong:text-[#000000]">
                    {!! $page->content !!}
                </div>
            @else
                <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] p-10 text-center shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <i class="fa-solid fa-file-pen text-3xl text-[#E0E0E0] mb-3"></i>
                    <p class="text-sm text-[#37474F]">Content coming soon.</p>
                </div>
            @endif

            {{-- Back link --}}
            <div>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center gap-2 h-[34px] px-[14px] rounded-[4px] border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold hover:bg-[#D4AF37]/10 transition-colors font-['Inter','Segoe_UI',sans-serif]">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Home
                </a>
            </div>

        </div>
    </div>

</x-guest-layout>
