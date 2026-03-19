@extends('admin.layouts.app')

@section('title', 'Business Profiles')
@section('page-title', 'Business Profiles')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Business Profiles</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage registered business accounts</p>
        </div>
    </div>

    <!-- Stat -->
    <div class="grid grid-cols-1 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-briefcase text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['total'] }}</p>
                    <p class="text-[#37474F] text-xs">Total Business Profiles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.business-profiles.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by business name..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="industry"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Industries</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" {{ request('industry') === $industry ? 'selected' : '' }}>{{ ucfirst($industry) }}</option>
                @endforeach
            </select>

            <select name="country"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.business-profiles.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($profiles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Business</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Owner</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Industry</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Location</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Created</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $profile)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        @if($profile->logo)
                                            <img src="{{ asset('storage/' . $profile->logo) }}" alt="{{ $profile->business_name }}"
                                                 class="w-8 h-8 rounded-full object-cover border border-[#E0E0E0] flex-shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#37474F] flex items-center justify-center flex-shrink-0">
                                                <span class="text-[#D4AF37] text-[11px] font-semibold">{{ substr($profile->business_name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $profile->business_name }}</p>
                                            @if($profile->legal_name && $profile->legal_name !== $profile->business_name)
                                                <p class="text-[12px] text-[#37474F]">{{ $profile->legal_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] text-[#1A1A1A]">{{ $profile->user->name }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $profile->user->email }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($profile->industry)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#37474F]/10 text-[#37474F]">
                                            {{ ucfirst($profile->industry) }}
                                        </span>
                                    @else
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                                    @if($profile->city || $profile->country)
                                        {{ $profile->city }}{{ $profile->city && $profile->country ? ', ' : '' }}{{ $profile->country?->name }}
                                    @else
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($profile->isApproved())
                                        <span class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-gray-500">Pending</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[12px] text-[#37474F]">{{ $profile->created_at->format('M d, Y') }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.business-profiles.show', $profile) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="View">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.business-profiles.edit', $profile) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.business-profiles.destroy', $profile) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    @click="$dispatch('open-confirm-modal', { message: 'Are you sure you want to delete this business profile?', form: $el.closest('form') })"
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
                {{ $profiles->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-briefcase text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No business profiles found</p>
            </div>
        @endif
    </div>

</div>
@endsection
