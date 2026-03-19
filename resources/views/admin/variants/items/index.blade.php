@extends('admin.layouts.app')

@section('title', 'Manage Variant Items: ' . $variant->name)
@section('page-title', 'Variant Items')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $variant->name }} Items</h2>
            <p class="text-[#37474F] text-sm mt-1">
                Type: <span class="font-medium">{{ ucfirst($variant->type) }}</span>
                &middot;
                Status: <span class="font-medium">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.variants.items.create', $variant) }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus"></i>
                Add New Item
            </a>
            <a href="{{ route('admin.variants.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Variants
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div id="itemsTableContainer">
            @include('admin.variants.items.partials.table', ['items' => $items, 'variant' => $variant])
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>

</div>
@endsection
