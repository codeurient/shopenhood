@extends('admin.layouts.app')

@section('title', 'Login Details')
@section('page-title', 'Login Details')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.login-histories.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Histories
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Login Details</h2>
            <p class="text-[#37474F] text-sm mt-1">Detailed information about this login event</p>
        </div>
    </div>

    <!-- User Card -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-5 mb-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($loginHistory->user)
                    <div class="w-16 h-16 rounded-full bg-[#37474F] flex items-center justify-center text-[#D4AF37] text-2xl font-bold flex-shrink-0">
                        {{ substr($loginHistory->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-[16px] font-semibold text-[#1A1A1A]">{{ $loginHistory->user->name }}</h3>
                        <p class="text-[13px] text-[#37474F]">{{ $loginHistory->user->email }}</p>
                        <a href="{{ route('admin.login-histories.user', $loginHistory->user) }}"
                           class="text-[12px] text-[#D4AF37] hover:underline mt-0.5 inline-block">
                            View all logins for this user
                        </a>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-[#37474F] text-2xl font-bold flex-shrink-0">?</div>
                    <div>
                        <h3 class="text-[16px] font-semibold text-[#37474F]">Deleted User</h3>
                    </div>
                @endif
            </div>
            @if($loginHistory->is_suspicious)
                <span class="inline-flex items-center gap-1.5 h-6 px-3 rounded-[10px] text-[12px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">
                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    Suspicious Login
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 h-6 px-3 rounded-[10px] text-[12px] font-semibold bg-green-50 text-green-700">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    Normal Login
                </span>
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

        <!-- Connection Info -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-[#D4AF37]"></i>
                    Connection Information
                </h4>
            </div>
            <dl class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">IP Address</dt>
                    <dd><code class="px-2 py-0.5 bg-gray-100 text-[12px] rounded text-[#1A1A1A] font-mono">{{ $loginHistory->ip_address }}</code></dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">Country</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->country ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">City</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->city ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">Login Time</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->logged_in_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Device Info -->
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-laptop text-[#D4AF37]"></i>
                    Device Information
                </h4>
            </div>
            <dl class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">Device</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->device ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">Browser</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->browser ?? 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-[13px] text-[#37474F]">Platform</dt>
                    <dd class="text-[13px] text-[#1A1A1A]">{{ $loginHistory->platform ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- User Agent -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-code text-[#D4AF37]"></i>
                User Agent
            </h4>
        </div>
        <div class="p-4">
            <code class="block p-3 bg-gray-50 rounded text-[12px] text-[#1A1A1A] break-all font-mono">
                {{ $loginHistory->user_agent ?? 'Not available' }}
            </code>
        </div>
    </div>

    <!-- Same IP Logins -->
    @if($sameIpLogins->count() > 0)
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-share-nodes text-[#D4AF37]"></i>
                    Other Logins from {{ $loginHistory->ip_address }}
                </h4>
            </div>
            <div class="p-4 space-y-2">
                @foreach($sameIpLogins as $login)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#37474F] flex items-center justify-center text-[#D4AF37] text-[11px] font-semibold flex-shrink-0">
                                {{ $login->user ? substr($login->user->name, 0, 1) : '?' }}
                            </div>
                            <div>
                                <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $login->user?->name ?? 'Deleted User' }}</p>
                                <p class="text-[12px] text-[#37474F]">{{ $login->logged_in_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @if($login->is_suspicious)
                            <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Suspicious</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- User Recent Logins -->
    @if($userRecentLogins->count() > 0)
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
            <div class="px-4 py-3 border-b border-[#E0E0E0]">
                <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#D4AF37]"></i>
                    Recent Logins by {{ $loginHistory->user?->name ?? 'this User' }}
                </h4>
            </div>
            <div class="p-4 space-y-2">
                @foreach($userRecentLogins as $login)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <code class="text-[12px] text-[#1A1A1A] font-mono">{{ $login->ip_address }}</code>
                            <p class="text-[12px] text-[#37474F] mt-0.5">{{ $login->device }} / {{ $login->browser }} / {{ $login->platform }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[13px] text-[#1A1A1A]">{{ $login->logged_in_at->format('M d, Y') }}</p>
                            <p class="text-[12px] text-[#37474F]">{{ $login->logged_in_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h4 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#D4AF37]"></i>
                Actions
            </h4>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.login-histories.block-ip') }}" method="POST"
                  class="flex items-center gap-4"
                  onsubmit="return confirm('Are you sure you want to flag this IP address?')">
                @csrf
                <input type="hidden" name="ip_address" value="{{ $loginHistory->ip_address }}">
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#C0392B] text-white text-[13px] font-semibold rounded hover:bg-[#a93226] transition">
                    <i class="fa-solid fa-flag text-xs"></i>
                    Flag IP Address
                </button>
                <span class="text-[12px] text-[#37474F]">This will log the action. Configure firewall rules separately.</span>
            </form>
        </div>
    </div>

</div>
@endsection
