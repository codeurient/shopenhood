@extends('admin.layouts.app')

@section('title', 'User Login History')
@section('page-title', 'Login Histories')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.login-histories.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to All Histories
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">{{ $user->name }}</h2>
            <p class="text-[13px] text-[#37474F] mt-0.5">{{ $user->email }}</p>
        </div>
    </div>

    <!-- Stats -->
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
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-clock text-green-500"></i>
                </div>
                <div>
                    <p class="text-[13px] font-bold text-[#1A1A1A]">
                        {{ $stats['last_login'] ? $stats['last_login']->format('M d, Y') : 'Never' }}
                    </p>
                    @if($stats['last_login'])
                        <p class="text-[11px] text-[#37474F]">{{ $stats['last_login']->format('h:i A') }}</p>
                    @endif
                    <p class="text-[#37474F] text-xs">Last Login</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($loginHistories->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">IP Address</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Device / Browser</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Platform</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Location</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider text-center">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loginHistories as $history)
                            <tr class="{{ $history->is_suspicious ? 'bg-red-50' : ($loop->even ? 'bg-white' : 'bg-gray-50') }} hover:bg-[#D4AF37]/5 transition-colors">
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
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    @if($history->country || $history->city)
                                        {{ $history->city }}{{ $history->city && $history->country ? ', ' : '' }}{{ $history->country }}
                                    @else
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
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
                <p class="text-[#37474F] text-[13px]">No login history for this user</p>
            </div>
        @endif
    </div>

</div>
@endsection
