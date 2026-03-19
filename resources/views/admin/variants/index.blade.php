@extends('admin.layouts.app')

@section('title', 'Variants Management')
@section('page-title', 'Variants')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Variants</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage product variant types</p>
        </div>
        <a href="{{ route('admin.variants.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus"></i>
            Create Variant
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" id="filterForm">
            <div class="flex flex-wrap gap-3 items-center">
                <input type="text" name="search"
                       placeholder="Search variants..."
                       value="{{ request('search') }}"
                       class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 w-56">

                <select name="type"
                        class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                    <option value="">All Types</option>
                    <option value="select" {{ request('type') === 'select' ? 'selected' : '' }}>Select</option>
                    <option value="radio" {{ request('type') === 'radio' ? 'selected' : '' }}>Radio</option>
                    <option value="checkbox" {{ request('type') === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                    <option value="number" {{ request('type') === 'number' ? 'selected' : '' }}>Number</option>
                </select>

                <select name="is_active"
                        class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-filter text-xs"></i>
                    Filter
                </button>
                <a href="{{ route('admin.variants.index') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Variants Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div id="variantsTableContainer">
            @include('admin.variants.partials.table', ['variants' => $variants])
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $variants->links() }}
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    filterForm.querySelectorAll('select').forEach(input => {
        input.addEventListener('change', () => filterForm.submit());
    });
});
</script>
@endsection
