<x-guest-layout>
    <x-slot name="title">{{ $page->title }} — {{ config('app.name') }}</x-slot>
    @if($page->meta_description)
        <x-slot name="metaDescription">{{ $page->meta_description }}</x-slot>
    @endif

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">Home</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-600">{{ $page->title }}</span>
        </nav>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>

        @if($page->content)
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-gray-700 leading-relaxed">
                {!! $page->content !!}
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
                Content coming soon.
            </div>
        @endif

    </div>
</x-guest-layout>
