@extends('admin.layouts.app')

@section('title', 'Legal & Policies')
@section('page-title', 'Legal & Policies')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Legal & Policies</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage all platform legal documents and acceptance requirements</p>
        </div>
        <a href="{{ route('admin.legal.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Policy
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
    @php
        $totalCount = \App\Models\Policy::count();
        $activeCount = \App\Models\Policy::where('status', 'active')->count();
        $requireAcceptanceCount = \App\Models\Policy::where('require_acceptance', true)->count();
        $footerCount = \App\Models\Policy::where('show_in_footer', true)->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-file-contract text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $totalCount }}</p>
                    <p class="text-[#37474F] text-xs">Total Policies</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $activeCount }}</p>
                    <p class="text-[#37474F] text-xs">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-handshake text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $requireAcceptanceCount }}</p>
                    <p class="text-[#37474F] text-xs">Require Acceptance</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-link text-purple-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $footerCount }}</p>
                    <p class="text-[#37474F] text-xs">In Footer</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search policies..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="type"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Types</option>
                @foreach($policyTypes as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.legal.index') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#37474F] border-b border-[#000000]/20">
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Policy</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Type</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Version</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Status</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Acceptances</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Flags</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                        <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.legal.show', $policy) }}"
                                   class="text-[13px] font-medium text-[#1A1A1A] hover:text-[#D4AF37] transition">{{ $policy->title }}</a>
                                @if($policy->description)
                                    <p class="text-[12px] text-[#37474F] mt-0.5 truncate max-w-xs">{{ $policy->description }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-[#37474F]">
                                    {{ $policyTypes[$policy->policy_type] ?? $policy->policy_type }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <span class="text-[12px] font-mono text-[#37474F]">v{{ $policy->version }}</span>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <button type="button"
                                        onclick="toggleStatus({{ $policy->id }}, this)"
                                        data-policy-id="{{ $policy->id }}"
                                        class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition {{ $policy->status === 'active' ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-gray-100 text-[#37474F] hover:bg-gray-200' }}">
                                    {{ ucfirst($policy->status) }}
                                </button>
                            </td>
                            <td class="px-3 py-2.5 text-center text-[13px] text-[#37474F]">
                                {{ number_format($policy->acceptances_count) }}
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @if($policy->require_acceptance)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700">Accept</span>
                                    @endif
                                    @if($policy->show_in_footer)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-blue-50 text-blue-700">Footer</span>
                                    @endif
                                    @if($policy->show_during_registration)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-purple-50 text-purple-700">Reg</span>
                                    @endif
                                    @if($policy->show_during_checkout)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-teal-50 text-teal-700">Checkout</span>
                                    @endif
                                    @if($policy->show_during_listing)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-orange-50 text-orange-700">Listing</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.legal.show', $policy) }}"
                                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                       title="View">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.legal.edit', $policy) }}"
                                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                       title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.legal.destroy', $policy) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                @click="$dispatch('open-confirm-modal', { message: 'Delete this policy? This cannot be undone.', form: $el.closest('form') })"
                                                class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                                title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center">
                                <i class="fa-solid fa-file-contract text-3xl text-[#E0E0E0] mb-3 block"></i>
                                <p class="text-[#37474F] text-[13px]">No policies found.</p>
                                <a href="{{ route('admin.legal.create') }}"
                                   class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition mt-3">
                                    Create one
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($policies->hasPages())
            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $policies->links() }}
            </div>
        @endif
    </div>

</div>

<script>
function toggleStatus(policyId, btn) {
    const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
    fetch(`{{ route('admin.legal.toggle-status', ':id') }}`.replace(':id', policyId), {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    })
    .then(r => r.json())
    .then(data => {
        const isActive = data.status === 'active';
        btn.textContent = isActive ? 'Active' : 'Inactive';
        btn.className = 'inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition ' +
            (isActive ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-gray-100 text-[#37474F] hover:bg-gray-200');
    });
}
</script>
@endsection
