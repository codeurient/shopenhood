@extends('admin.layouts.app')

@section('title', $policy->title)
@section('page-title', 'Legal & Policies')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.legal.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Legal & Policies
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $policy->title }}</h2>
            <p class="text-[13px] text-[#37474F] mt-0.5">
                {{ \App\Models\Policy::POLICY_TYPES[$policy->policy_type] ?? $policy->policy_type }}
                &mdash; v{{ $policy->version }}
                &mdash; <span class="{{ $policy->isActive() ? 'text-green-600' : 'text-[#37474F]' }} font-medium">{{ ucfirst($policy->status) }}</span>
            </p>
        </div>
        <a href="{{ route('admin.legal.edit', $policy) }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-pen text-xs"></i>
            Edit Policy
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Left Sidebar -->
        <div class="space-y-5">

            <!-- Policy Details -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                        Policy Details
                    </h3>
                </div>
                <dl class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">Current Version</dt>
                        <dd class="text-[13px] font-mono font-semibold text-[#1A1A1A]">v{{ $policy->version }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">Status</dt>
                        <dd class="text-[13px] font-medium {{ $policy->isActive() ? 'text-green-600' : 'text-[#37474F]' }}">{{ ucfirst($policy->status) }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">Published</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $policy->published_at?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">Last Updated</dt>
                        <dd class="text-[13px] text-[#1A1A1A]">{{ $policy->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">Total Acceptances</dt>
                        <dd class="text-[13px] font-semibold text-[#1A1A1A]">{{ number_format($policy->acceptances->count()) }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-[12px] text-[#37474F]">This Version</dt>
                        <dd class="text-[13px] font-semibold text-[#1A1A1A]">
                            {{ number_format($policy->acceptances->where('policy_version', $policy->version)->count()) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Display Flags -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-flag text-[#D4AF37]"></i>
                        Display Flags
                    </h3>
                </div>
                <ul class="p-4 space-y-2">
                    @foreach([
                        ['require_acceptance', 'Requires User Acceptance'],
                        ['show_in_footer', 'Shown in Footer'],
                        ['show_during_registration', 'Shown at Registration'],
                        ['show_during_checkout', 'Shown at Checkout'],
                        ['show_during_listing', 'Shown at Product Listing'],
                    ] as [$field, $label])
                        <li class="flex items-center gap-2">
                            @if($policy->$field)
                                <i class="fa-solid fa-circle-check text-green-500 flex-shrink-0 text-[13px]"></i>
                            @else
                                <i class="fa-solid fa-circle-xmark text-[#E0E0E0] flex-shrink-0 text-[13px]"></i>
                            @endif
                            <span class="text-[13px] {{ $policy->$field ? 'text-[#1A1A1A]' : 'text-[#37474F]' }}">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Platform Actions -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-[#D4AF37]"></i>
                        Platform Action Triggers
                    </h3>
                </div>
                <div class="p-4">
                    @if($policy->platformActions->count())
                        <ul class="space-y-1.5">
                            @foreach($policy->platformActions as $action)
                                <li class="flex items-center gap-2 text-[13px] text-[#1A1A1A]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] flex-shrink-0"></span>
                                    {{ $platformActions[$action->action] ?? $action->action }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-[13px] text-[#37474F]">No platform actions assigned.</p>
                    @endif
                </div>
            </div>

            <!-- Version History -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-[#D4AF37]"></i>
                        Version History
                    </h3>
                </div>
                <div class="p-4">
                    @if($policy->versions->count())
                        <ul class="space-y-2">
                            @foreach($policy->versions as $ver)
                                <li class="flex items-center justify-between">
                                    <span class="text-[13px] font-mono text-[#1A1A1A]">v{{ $ver->version }}</span>
                                    <span class="text-[12px] text-[#37474F]">{{ $ver->created_at->format('d M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-[13px] text-[#37474F]">No previous versions archived yet.</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right: Acceptance Log -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0] flex items-center justify-between gap-3 flex-wrap">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-users text-[#D4AF37]"></i>
                        User Acceptance Log
                    </h3>
                    <form method="GET" class="flex items-center gap-2">
                        <label class="text-[12px] text-[#37474F]">Version:</label>
                        <select name="version"
                                onchange="this.form.submit()"
                                class="h-[28px] border border-[#E0E0E0] text-[#1A1A1A] text-[12px] rounded focus:ring-0 focus:border-[#D4AF37] px-2">
                            <option value="">All Versions</option>
                            @foreach($versionOptions as $v)
                                <option value="{{ $v }}" @selected(request('version') === $v)>v{{ $v }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#37474F] border-b border-[#000000]/20">
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">User</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Version</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Context</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">IP Address</th>
                                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-right">Accepted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($acceptances as $acceptance)
                                <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                    <td class="px-3 py-2.5">
                                        <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $acceptance->user?->name ?? '—' }}</p>
                                        <p class="text-[12px] text-[#37474F]">{{ $acceptance->user?->email }}</p>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span class="text-[12px] font-mono text-[#37474F]">v{{ $acceptance->policy_version }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        @if($acceptance->context)
                                            <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-[#37474F]">{{ $acceptance->context }}</span>
                                        @else
                                            <span class="text-[#E0E0E0]">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-[12px] text-[#37474F] font-mono">{{ $acceptance->ip_address ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-right text-[12px] text-[#37474F]">
                                        {{ $acceptance->accepted_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-12 text-center">
                                        <i class="fa-solid fa-handshake text-3xl text-[#E0E0E0] mb-3 block"></i>
                                        <p class="text-[#37474F] text-[13px]">No acceptances recorded yet for this policy.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($acceptances->hasPages())
                    <div class="px-4 py-3 border-t border-[#E0E0E0]">
                        {{ $acceptances->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
