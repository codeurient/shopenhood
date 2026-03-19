<x-guest-layout>
    <x-slot name="title">{{ $policy->title }} — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">{{ $policy->description ?: $policy->title.' — '.config('app.name').' legal document.' }}</x-slot>

    {{-- Hero Header --}}
    <div class="bg-[#000000] py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <nav class="flex items-center gap-2 text-xs text-[#37474F] mb-4 font-['Inter','Segoe_UI',sans-serif]">
                <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('legal.index') }}" class="hover:text-[#D4AF37] transition-colors">Legal Center</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-[#E0E0E0]">{{ $policy->title }}</span>
            </nav>

            <p class="text-xs font-semibold uppercase tracking-widest text-[#D4AF37] mb-2 font-['Inter','Segoe_UI',sans-serif]">
                {{ \App\Models\Policy::POLICY_TYPES[$policy->policy_type] ?? $policy->policy_type }}
            </p>
            <h1 class="font-bold text-[#FFFFFF] mb-2 font-['Inter','Segoe_UI',sans-serif]"
                style="font-size: clamp(1.25rem, 2.5vw, 2rem);">
                {{ $policy->title }}
            </h1>
            @if($policy->description)
                <p class="text-[#E0E0E0] text-sm">{{ $policy->description }}</p>
            @endif

            {{-- Meta row --}}
            <div class="mt-5 flex flex-wrap gap-x-6 gap-y-1 text-xs text-[#37474F]">
                <span>Version <strong class="font-mono text-[#E0E0E0]">{{ $policy->version }}</strong></span>
                @if($policy->published_at)
                    <span>Last updated <strong class="text-[#E0E0E0]">{{ $policy->published_at->format('d F Y') }}</strong></span>
                @endif
                @if($versions->isNotEmpty())
                    <span>{{ $versions->count() }} previous {{ Str::plural('version', $versions->count()) }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-[#FFFFFF] min-h-screen py-10 px-4">
        <div class="max-w-3xl mx-auto flex flex-col gap-5">

            {{-- Policy Content --}}
            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] px-6 py-6 shadow-[0_1px_4px_rgba(0,0,0,0.08)]
                        prose prose-sm max-w-none
                        prose-headings:text-[#1A1A1A] prose-headings:font-semibold
                        prose-p:text-[#1A1A1A] prose-p:leading-relaxed
                        prose-a:text-[#000000] prose-a:font-semibold prose-a:no-underline hover:prose-a:underline
                        prose-li:text-[#1A1A1A]
                        prose-strong:text-[#000000]">
                {!! $policy->content !!}
            </div>

            {{-- Version History --}}
            @if($versions->isNotEmpty())
            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] px-6 py-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-[#37474F] mb-4 font-['Inter','Segoe_UI',sans-serif]">
                    Version History
                </h2>
                <div class="flex flex-col gap-2">
                    {{-- Current --}}
                    <div class="flex items-center gap-3 text-sm">
                        <span class="inline-flex items-center px-2 py-0.5 bg-[#D4AF37]/20 text-[#D4AF37] rounded-[10px] text-[11px] font-semibold leading-5">
                            Current
                        </span>
                        <span class="font-mono text-[#1A1A1A] text-xs">v{{ $policy->version }}</span>
                        @if($policy->published_at)
                            <span class="text-[#37474F] text-xs">{{ $policy->published_at->format('d M Y') }}</span>
                        @endif
                    </div>
                    {{-- Past versions --}}
                    @foreach($versions as $ver)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="inline-flex items-center px-2 py-0.5 bg-[#E0E0E0] text-[#37474F] rounded-[10px] text-[11px] font-semibold leading-5">
                            Archived
                        </span>
                        <span class="font-mono text-[#37474F] text-xs">v{{ $ver->version }}</span>
                        <span class="text-xs text-[#37474F]">{{ $ver->created_at->format('d M Y') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Back link --}}
            <div>
                <a href="{{ route('legal.index') }}"
                   class="inline-flex items-center justify-center gap-2 h-[34px] px-[14px] rounded-[4px] border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold hover:bg-[#D4AF37]/10 transition-colors font-['Inter','Segoe_UI',sans-serif]">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Legal Center
                </a>
            </div>

        </div>
    </div>

</x-guest-layout>
