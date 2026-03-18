@extends('admin.layouts.app')

@section('title', $country->name . ' Cities')
@section('page-title', $country->name . ' Cities')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $country->name }} — Cities</h2>
            <p class="text-[#37474F] text-sm mt-1">
                @if($country->code)
                    Code: <span class="font-medium">{{ $country->code }}</span> &middot;
                @endif
                Status:
                <span class="font-medium {{ $country->is_active ? 'text-green-600' : 'text-[#C0392B]' }}">
                    {{ $country->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.locations.cities.create', $country) }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus"></i>
                Add City
            </a>
            <a href="{{ route('admin.locations.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Countries
            </a>
        </div>
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

    <!-- Cities Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($cities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider w-12">#</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">City Name</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Code</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cities as $index => $city)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    {{ $cities->firstItem() + $index }}
                                </td>
                                <td class="px-3 py-2.5 text-[13px] font-medium text-[#1A1A1A]">{{ $city->name }}</td>
                                <td class="px-3 py-2.5 text-[13px]">
                                    @if($city->code)
                                        <code class="bg-gray-100 px-2 py-0.5 rounded text-[12px] text-[#37474F]">{{ $city->code }}</code>
                                    @else
                                        <span class="text-[#37474F]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <form action="{{ route('admin.locations.cities.toggle-status', [$country, $city->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition
                                                       {{ $city->is_active ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-[#C0392B]/10 text-[#C0392B] hover:bg-[#C0392B]/20' }}">
                                            {{ $city->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.locations.cities.edit', [$country, $city->id]) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.locations.cities.destroy', [$country, $city->id]) }}" method="POST"
                                              onsubmit="return confirm('Delete {{ addslashes($city->name) }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $cities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-city text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No cities added for {{ $country->name }}</p>
                <a href="{{ route('admin.locations.cities.create', $country) }}" class="text-[#D4AF37] hover:underline text-[13px] mt-1 inline-block">
                    Add the first city
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
