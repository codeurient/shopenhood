@extends('admin.layouts.app')

@section('title', 'Listing Types')
@section('page-title', 'Listing Types')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Listing Types</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage listing types (sell, buy, gift, barter, auction)</p>
        </div>
        <a href="{{ route('admin.listing-types.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus"></i>
            Create Listing Type
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-list text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['total'] }}</p>
                    <p class="text-[#37474F] text-xs">Total Types</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['active'] }}</p>
                    <p class="text-[#37474F] text-xs">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-xmark text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['inactive'] }}</p>
                    <p class="text-[#37474F] text-xs">Inactive</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.listing-types.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or description..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="is_active"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.listing-types.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($listingTypes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Name</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Slug</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Description</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Requires Price</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Listings</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Sort</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listingTypes as $type)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        @if($type->icon)
                                            <span class="text-lg leading-none">{!! $type->icon !!}</span>
                                        @endif
                                        <span class="text-[13px] font-medium text-[#1A1A1A]">{{ $type->name }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <code class="bg-gray-100 px-2 py-0.5 rounded text-[12px] text-[#37474F]">{{ $type->slug }}</code>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    {{ Str::limit($type->description, 50) }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($type->requires_price)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Yes</span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-gray-500">No</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] font-medium text-[#1A1A1A]">
                                    {{ $type->listings_count }}
                                </td>
                                <td class="px-3 py-2.5">
                                    <form action="{{ route('admin.listing-types.toggle-status', $type) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition
                                                       {{ $type->is_active ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-[#C0392B]/10 text-[#C0392B] hover:bg-[#C0392B]/20' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">{{ $type->sort_order }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.listing-types.edit', $type) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        @if($type->listings_count === 0)
                                            <form action="{{ route('admin.listing-types.destroy', $type) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this listing type?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                                        title="Delete">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#E0E0E0] rounded cursor-not-allowed"
                                                  title="Cannot delete: has {{ $type->listings_count }} listing(s)">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $listingTypes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-list text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No listing types found</p>
                <a href="{{ route('admin.listing-types.create') }}" class="text-[#D4AF37] hover:underline text-[13px] mt-1 inline-block">
                    Create your first listing type
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
