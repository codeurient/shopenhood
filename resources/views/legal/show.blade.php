<x-guest-layout>
    <x-slot name="title">{{ $policy->title }} — {{ config('app.name') }}</x-slot>
    <x-slot name="metaDescription">{{ $policy->description ?: $policy->title.' — '.config('app.name').' legal document.' }}</x-slot>

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">Home</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('legal.index') }}" class="hover:text-gray-600 transition-colors">Legal Center</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-600">{{ $policy->title }}</span>
        </nav>

        {{-- Policy Header --}}
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-primary-600 mb-1">
                        {{ \App\Models\Policy::POLICY_TYPES[$policy->policy_type] ?? $policy->policy_type }}
                    </p>
                    <h1 class="text-xl font-bold text-gray-900">{{ $policy->title }}</h1>
                    @if($policy->description)
                        <p class="mt-1 text-sm text-gray-500">{{ $policy->description }}</p>
                    @endif
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-400">
                <span>Version <strong class="font-mono text-gray-600">{{ $policy->version }}</strong></span>
                @if($policy->published_at)
                    <span>Last updated <strong class="text-gray-600">{{ $policy->published_at->format('d F Y') }}</strong></span>
                @endif
                @if($versions->isNotEmpty())
                    <span>{{ $versions->count() }} previous {{ Str::plural('version', $versions->count()) }}</span>
                @endif
            </div>
        </div>

        {{-- Policy Content --}}
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-6 mb-6 prose prose-sm max-w-none
                    prose-headings:text-gray-900 prose-headings:font-semibold
                    prose-p:text-gray-600 prose-p:leading-relaxed
                    prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline
                    prose-li:text-gray-600
                    prose-strong:text-gray-800">
            {!! $policy->content !!}
        </div>

        {{-- Version History --}}
        @if($versions->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Version History</h2>
            <div class="space-y-2">
                {{-- Current version --}}
                <div class="flex items-center gap-3 text-sm">
                    <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">Current</span>
                    <span class="font-mono text-gray-700">v{{ $policy->version }}</span>
                    @if($policy->published_at)
                        <span class="text-gray-400 text-xs">{{ $policy->published_at->format('d M Y') }}</span>
                    @endif
                </div>
                {{-- Past versions --}}
                @foreach($versions as $ver)
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">Archived</span>
                    <span class="font-mono">v{{ $ver->version }}</span>
                    <span class="text-xs text-gray-400">{{ $ver->created_at->format('d M Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Back link --}}
        <div class="mt-6">
            <a href="{{ route('legal.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Legal Center
            </a>
        </div>

    </div>
</x-guest-layout>
