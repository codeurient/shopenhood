<x-guest-layout>
    <x-slot name="title">Legal Center — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">Browse all legal policies, terms, and agreements for {{ config('app.name') }}. Find our Terms of Service, Privacy Policy, Seller Agreement, and more.</x-slot>

    {{-- Hero Header --}}
    <div class="bg-[#000000] py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-2 text-xs text-[#37474F] mb-4 font-['Inter','Segoe_UI',sans-serif]">
                <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-[#E0E0E0]">Legal Center</span>
            </div>
            <h1 class="font-bold text-[#FFFFFF] mb-2 font-['Inter','Segoe_UI',sans-serif]"
                style="font-size: clamp(1.25rem, 2.5vw, 2rem);">
                Legal Center
            </h1>
            <p class="text-[#E0E0E0] text-sm">All platform policies, agreements, and legal documents in one place.</p>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-[#FFFFFF] min-h-screen py-10 px-4">
        <div class="max-w-4xl mx-auto">

            @if($grouped->isEmpty())
                <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] p-10 text-center shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
                    <i class="fa-solid fa-folder-open text-3xl text-[#E0E0E0] mb-3"></i>
                    <p class="text-sm text-[#37474F]">No legal documents are published yet.</p>
                </div>
            @else
                <div class="flex flex-col gap-8">
                    @foreach(\App\Models\Policy::POLICY_TYPES as $typeKey => $typeLabel)
                        @if($grouped->has($typeKey))
                        <div>
                            <h2 class="text-xs font-semibold uppercase tracking-widest text-[#37474F] mb-3 font-['Inter','Segoe_UI',sans-serif]">
                                {{ $typeLabel }}
                            </h2>
                            <div class="bg-[#FFFFFF] rounded-[8px] border border-[#E0E0E0] overflow-hidden shadow-[0_1px_4px_rgba(0,0,0,0.08)] divide-y divide-[#E0E0E0]">
                                @foreach($grouped[$typeKey] as $policy)
                                <a href="{{ route('legal.show', $policy) }}"
                                   class="flex items-start gap-4 px-5 py-4 hover:bg-[#F9F9F9] transition-colors group">

                                    {{-- Icon --}}
                                    <div class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-[4px] bg-[#000000] flex items-center justify-center">
                                        <i class="fa-solid fa-file-lines text-[#D4AF37] text-sm"></i>
                                    </div>

                                    {{-- Text --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-[#1A1A1A] group-hover:text-[#000000] transition-colors font-['Inter','Segoe_UI',sans-serif]">
                                            {{ $policy->title }}
                                        </p>
                                        @if($policy->description)
                                            <p class="mt-0.5 text-xs text-[#37474F] line-clamp-1">{{ $policy->description }}</p>
                                        @endif
                                    </div>

                                    {{-- Meta --}}
                                    <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                        <span class="text-xs font-mono text-[#37474F]">v{{ $policy->version }}</span>
                                        @if($policy->published_at)
                                            <span class="text-xs text-[#37474F]">{{ $policy->published_at->format('d M Y') }}</span>
                                        @endif
                                    </div>

                                    {{-- Arrow --}}
                                    <div class="flex-shrink-0 self-center">
                                        <i class="fa-solid fa-chevron-right text-xs text-[#E0E0E0] group-hover:text-[#D4AF37] transition-colors"></i>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</x-guest-layout>
