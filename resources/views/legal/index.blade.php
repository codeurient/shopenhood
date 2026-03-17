<x-guest-layout>
    <x-slot name="title">Legal Center — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">Browse all legal policies, terms, and agreements for {{ config('app.name') }}. Find our Terms of Service, Privacy Policy, Seller Agreement, and more.</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Legal Center</h1>
            <p class="mt-1 text-gray-500">All platform policies, agreements, and legal documents in one place.</p>
        </div>

        @if($grouped->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
                No legal documents are published yet.
            </div>
        @else
            <div class="space-y-8">
                @foreach(\App\Models\Policy::POLICY_TYPES as $typeKey => $typeLabel)
                    @if($grouped->has($typeKey))
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">{{ $typeLabel }}</h2>
                        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
                            @foreach($grouped[$typeKey] as $policy)
                            <a href="{{ route('legal.show', $policy) }}"
                               class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group">
                                <div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">
                                        {{ $policy->title }}
                                    </p>
                                    @if($policy->description)
                                        <p class="mt-0.5 text-xs text-gray-500 line-clamp-1">{{ $policy->description }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                    <span class="text-xs font-mono text-gray-400">v{{ $policy->version }}</span>
                                    @if($policy->published_at)
                                        <span class="text-xs text-gray-400">{{ $policy->published_at->format('d M Y') }}</span>
                                    @endif
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
</x-guest-layout>
