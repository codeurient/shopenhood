@extends('admin.layouts.app')

@section('title', 'Login Histories')
@section('page-title', 'Login Histories')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Login Histories</h2>
            <p class="text-[#37474F] text-sm mt-1">Monitor user login activity and detect suspicious behavior</p>
        </div>
        <form action="{{ route('admin.login-histories.clear-all') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button"
                    @click="$dispatch('open-confirm-modal', { message: 'This will permanently delete ALL login history records. This action cannot be undone.', form: $el.closest('form') })"
                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                <i class="fa-solid fa-trash text-xs"></i>
                Clear All History
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-right-to-bracket text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['total']) }}</p>
                    <p class="text-[#37474F] text-xs">Total Logins</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar-day text-green-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['today']) }}</p>
                    <p class="text-[#37474F] text-xs">Today</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-[#C0392B]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['suspicious']) }}</p>
                    <p class="text-[#37474F] text-xs">Suspicious</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-globe text-purple-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['unique_ips']) }}</p>
                    <p class="text-[#37474F] text-xs">Unique IPs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspicious IPs Warning -->
    @if($suspiciousIps->count() > 0)
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 rounded">
            <h3 class="text-[13px] font-semibold text-[#C0392B] mb-2 flex items-center gap-2">
                <i class="fa-solid fa-shield-exclamation"></i>
                Suspicious IP Addresses
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($suspiciousIps as $ip)
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-red-100 text-[#C0392B]">
                        {{ $ip->ip_address }} ({{ $ip->count }}x)
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.login-histories.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search IP, user, browser..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[180px]">

            <input type="text" name="ip_address" value="{{ request('ip_address') }}" placeholder="Filter by IP..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 w-40 font-mono">

            <select name="is_suspicious"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All</option>
                <option value="yes" {{ request('is_suspicious') === 'yes' ? 'selected' : '' }}>Suspicious Only</option>
                <option value="no" {{ request('is_suspicious') === 'no' ? 'selected' : '' }}>Normal Only</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.login-histories.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($loginHistories->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">User</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">IP Address</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Device / Browser</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Platform</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Time</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loginHistories as $history)
                            <tr class="{{ $history->is_suspicious ? 'bg-red-50' : ($loop->even ? 'bg-white' : 'bg-gray-50') }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    @if($history->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-[#37474F] flex items-center justify-center text-[#D4AF37] text-[11px] font-semibold flex-shrink-0">
                                                {{ substr($history->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $history->user->name }}</p>
                                                <p class="text-[12px] text-[#37474F]">{{ $history->user->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[13px] text-[#37474F]">Deleted User</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <code class="px-2 py-0.5 bg-gray-100 text-[12px] rounded text-[#1A1A1A] font-mono">{{ $history->ip_address }}</code>
                                </td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] text-[#1A1A1A]">{{ $history->device ?? 'Unknown' }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $history->browser ?? 'Unknown' }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    {{ $history->platform ?? 'Unknown' }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($history->is_suspicious)
                                        <span class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            Suspicious
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-green-50 text-green-700">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] text-[#1A1A1A]">{{ $history->logged_in_at->format('M d, Y') }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $history->logged_in_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('admin.login-histories.show', $history) }}"
                                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                       title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $loginHistories->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-shield-halved text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No login histories found</p>
                @if(request()->hasAny(['search', 'ip_address', 'is_suspicious', 'date_from', 'date_to']))
                    <a href="{{ route('admin.login-histories.index') }}"
                       class="text-[13px] text-[#37474F] hover:text-[#1A1A1A] mt-2 inline-block underline">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
