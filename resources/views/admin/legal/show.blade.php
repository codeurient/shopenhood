@extends('admin.layouts.app')

@section('title', $policy->title)
@section('page-title', 'Legal & Policies')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.legal.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $policy->title }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ \App\Models\Policy::POLICY_TYPES[$policy->policy_type] ?? $policy->policy_type }}
                    &mdash; v{{ $policy->version }}
                    &mdash; <span class="{{ $policy->isActive() ? 'text-green-600' : 'text-gray-400' }} font-medium">{{ ucfirst($policy->status) }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.legal.edit', $policy) }}"
           class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 text-sm font-medium transition">
            Edit Policy
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: policy meta + actions --}}
        <div class="space-y-6">

            {{-- Meta --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Policy Details</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Current Version</dt>
                        <dd class="font-mono font-semibold text-gray-800">v{{ $policy->version }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd class="{{ $policy->isActive() ? 'text-green-600' : 'text-gray-400' }} font-medium">{{ ucfirst($policy->status) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Published</dt>
                        <dd class="text-gray-700">{{ $policy->published_at?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Last Updated</dt>
                        <dd class="text-gray-700">{{ $policy->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Acceptances</dt>
                        <dd class="font-semibold text-gray-800">{{ number_format($policy->acceptances->count()) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">This Version</dt>
                        <dd class="font-semibold text-gray-800">
                            {{ number_format($policy->acceptances->where('policy_version', $policy->version)->count()) }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Flags --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Display Flags</h3>
                <ul class="space-y-2 text-sm">
                    @foreach([
                        ['require_acceptance', 'Requires User Acceptance'],
                        ['show_in_footer', 'Shown in Footer'],
                        ['show_during_registration', 'Shown at Registration'],
                        ['show_during_checkout', 'Shown at Checkout'],
                        ['show_during_listing', 'Shown at Product Listing'],
                    ] as [$field, $label])
                    <li class="flex items-center gap-2">
                        @if($policy->$field)
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        @endif
                        <span class="{{ $policy->$field ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Platform Actions --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Platform Action Triggers</h3>
                @if($policy->platformActions->count())
                    <ul class="space-y-1.5">
                        @foreach($policy->platformActions as $action)
                        <li class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0"></span>
                            {{ $platformActions[$action->action] ?? $action->action }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No platform actions assigned.</p>
                @endif
            </div>

            {{-- Version History --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Version History</h3>
                @if($policy->versions->count())
                    <ul class="space-y-2">
                        @foreach($policy->versions as $ver)
                        <li class="flex items-center justify-between text-sm">
                            <span class="font-mono text-gray-700">v{{ $ver->version }}</span>
                            <span class="text-gray-400 text-xs">{{ $ver->created_at->format('d M Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No previous versions archived yet.</p>
                @endif
            </div>

        </div>

        {{-- Right: Acceptance Logs --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                    <h3 class="text-base font-semibold text-gray-800">User Acceptance Log</h3>
                    <form method="GET" class="flex items-center gap-2">
                        <label class="text-xs text-gray-500">Version:</label>
                        <select name="version"
                                onchange="this.form.submit()"
                                class="px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                            <option value="">All Versions</option>
                            @foreach($versionOptions as $v)
                                <option value="{{ $v }}" @selected(request('version') === $v)>v{{ $v }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">User</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Version</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Context</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">IP Address</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">Accepted At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($acceptances as $acceptance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $acceptance->user?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $acceptance->user?->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-mono text-xs text-gray-600">v{{ $acceptance->policy_version }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($acceptance->context)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $acceptance->context }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ $acceptance->ip_address ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-xs text-gray-500 whitespace-nowrap">
                                    {{ $acceptance->accepted_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    No acceptances recorded yet for this policy.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($acceptances->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">{{ $acceptances->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
