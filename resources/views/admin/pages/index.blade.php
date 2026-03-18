@extends('admin.layouts.app')

@section('title', 'Pages')
@section('page-title', 'Pages')

@section('content')
<div>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#1A1A1A]">Pages</h2>
        <p class="text-[#37474F] text-sm mt-1">Manage static content pages displayed on the site</p>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#37474F] border-b border-[#000000]/20">
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Page</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Last Updated</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                            <td class="px-3 py-2.5">
                                <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $page->title }}</p>
                                <p class="text-[12px] text-[#37474F]">{{ $page->key }}</p>
                            </td>
                            <td class="px-3 py-2.5">
                                @if($page->is_published)
                                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Published</span>
                                @else
                                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-[#37474F]">Hidden</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-[12px] text-[#37474F]">{{ $page->updated_at->format('M d, Y') }}</td>
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.pages.edit', $page) }}"
                                   class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                    Edit Content
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
