@extends('admin.layouts.app')

@section('title', 'Legal & Policies')
@section('page-title', 'Legal & Policies')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Legal & Policies</h2>
            <p class="text-gray-600 mt-1">Manage all platform legal documents and acceptance requirements</p>
        </div>
        <a href="{{ route('admin.legal.create') }}"
           class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition font-medium">
            + Add Policy
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    {{-- Stats row --}}
    @php
        $totalCount = \App\Models\Policy::count();
        $activeCount = \App\Models\Policy::where('status', 'active')->count();
        $requireAcceptanceCount = \App\Models\Policy::where('require_acceptance', true)->count();
        $footerCount = \App\Models\Policy::where('show_in_footer', true)->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p><p class="text-sm text-gray-500">Total Policies</p></div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $activeCount }}</p><p class="text-sm text-gray-500">Active</p></div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $requireAcceptanceCount }}</p><p class="text-sm text-gray-500">Require Acceptance</p></div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $footerCount }}</p><p class="text-sm text-gray-500">In Footer</p></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search policies..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            </div>
            <div class="min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Policy Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">All Types</option>
                    @foreach($policyTypes as $key => $label)
                        <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.legal.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Policies grouped by type --}}
    @php
        $grouped = $policies->getCollection()->groupBy('policy_type');
    @endphp
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Policy</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Type</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Version</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Acceptance</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Flags</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($policies as $policy)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.legal.show', $policy) }}"
                           class="font-semibold text-gray-900 hover:text-primary-600">{{ $policy->title }}</a>
                        @if($policy->description)
                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $policy->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium whitespace-nowrap">
                            {{ $policyTypes[$policy->policy_type] ?? $policy->policy_type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-mono text-gray-600">v{{ $policy->version }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button"
                                onclick="toggleStatus({{ $policy->id }}, this)"
                                data-policy-id="{{ $policy->id }}"
                                class="px-2.5 py-1 rounded-full text-xs font-semibold transition {{ $policy->status === 'active' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ ucfirst($policy->status) }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs text-gray-500">{{ number_format($policy->acceptances_count) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1 flex-wrap">
                            @if($policy->require_acceptance)
                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-xs" title="Requires acceptance">✓ Accept</span>
                            @endif
                            @if($policy->show_in_footer)
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-xs" title="Shown in footer">Footer</span>
                            @endif
                            @if($policy->show_during_registration)
                                <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs" title="Shown during registration">Reg</span>
                            @endif
                            @if($policy->show_during_checkout)
                                <span class="px-1.5 py-0.5 bg-teal-100 text-teal-700 rounded text-xs" title="Shown during checkout">Checkout</span>
                            @endif
                            @if($policy->show_during_listing)
                                <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-xs" title="Shown during listing">Listing</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.legal.show', $policy) }}"
                               class="text-xs px-3 py-1 border border-gray-300 text-gray-600 rounded hover:bg-gray-50 transition">
                                View
                            </a>
                            <a href="{{ route('admin.legal.edit', $policy) }}"
                               class="text-xs px-3 py-1 bg-primary-500 text-white rounded hover:bg-primary-600 transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.legal.destroy', $policy) }}" method="POST"
                                  onsubmit="return confirm('Delete this policy? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                        No policies found. <a href="{{ route('admin.legal.create') }}" class="text-primary-600 hover:underline">Create one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($policies->hasPages())
        <div class="mt-4">{{ $policies->links() }}</div>
    @endif

</div>

<script>
function toggleStatus(policyId, btn) {
    const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
    fetch(`/admin/legal/${policyId}/toggle-status`, {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    })
    .then(r => r.json())
    .then(data => {
        const isActive = data.status === 'active';
        btn.textContent = isActive ? 'Active' : 'Inactive';
        btn.className = 'px-2.5 py-1 rounded-full text-xs font-semibold transition ' +
            (isActive ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200');
    });
}
</script>
@endsection
