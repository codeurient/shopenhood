@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div>

    {{-- ══ Greeting ══════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Platform Overview</h2>
            <p class="text-[#37474F] text-sm mt-0.5">
                <i class="fa-regular fa-calendar text-[#D4AF37] mr-1"></i>
                {{ now()->format('l, F j Y') }}
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-clock text-xs"></i>
                {{ $listingStats['pending'] }} Pending
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                <i class="fa-solid fa-users text-xs"></i>
                Users
            </a>
        </div>
    </div>

    {{-- ══ KPI CARDS ══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Users --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-users text-blue-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">
                    +{{ $userStats['new_month'] }} mo
                </span>
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($userStats['total']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Users</p>
            <div class="flex items-center gap-2 mt-2 flex-wrap">
                <span class="text-[11px] text-[#D4AF37] font-semibold">{{ $userStats['business'] }} business</span>
                @if($userStats['banned'] > 0)
                    <span class="text-[11px] text-red-500 font-semibold">· {{ $userStats['banned'] }} banned</span>
                @endif
            </div>
        </div>

        {{-- Listings --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#D4AF37]/10 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-[#D4AF37]"></i>
                </div>
                @if($listingStats['pending'] > 0)
                    <span class="text-[11px] font-semibold text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded">
                        {{ $listingStats['pending'] }} pending
                    </span>
                @endif
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($listingStats['total']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Listings</p>
            <div class="flex items-center gap-2 mt-2 flex-wrap">
                <span class="text-[11px] text-green-600 font-semibold">{{ $listingStats['active'] }} active</span>
                @if($listingStats['deleted'] > 0)
                    <span class="text-[11px] text-[#37474F]">· {{ $listingStats['deleted'] }} deleted</span>
                @endif
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-indigo-50 flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-indigo-600"></i>
                </div>
                @if($orderStats['pending'] > 0)
                    <span class="text-[11px] font-semibold text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded">
                        {{ $orderStats['pending'] }} pending
                    </span>
                @endif
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($orderStats['total']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Orders</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-[11px] text-green-600 font-semibold">{{ $orderStats['completed'] }} completed</span>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-green-50 flex items-center justify-center">
                    <i class="fa-solid fa-dollar-sign text-green-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-[#D4AF37]">
                    +${{ number_format($orderStats['revenue_month'], 0) }} mo
                </span>
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">${{ number_format($orderStats['revenue'], 2) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Revenue</p>
            <p class="text-[11px] text-[#37474F] mt-2">
                <span class="text-[#D4AF37] font-semibold">${{ number_format($orderStats['revenue_month'], 2) }}</span> this month
            </p>
        </div>
    </div>

    {{-- ══ CHARTS ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Revenue & Orders Trend --}}
        <div class="lg:col-span-2 bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-[14px] font-bold text-[#1A1A1A]">Revenue & Orders</h3>
                    <p class="text-[12px] text-[#37474F]">Last 6 months platform-wide</p>
                </div>
                <div class="flex items-center gap-3 text-[11px]">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-[#D4AF37] inline-block rounded"></span>Revenue</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-blue-500 inline-block rounded"></span>Orders</span>
                </div>
            </div>
            <canvas id="revChart" height="110"></canvas>
        </div>

        {{-- User Registrations --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="mb-4">
                <h3 class="text-[14px] font-bold text-[#1A1A1A]">New Registrations</h3>
                <p class="text-[12px] text-[#37474F]">Users joining per month</p>
            </div>
            <canvas id="regChart" height="160"></canvas>
            <div class="mt-4 pt-4 border-t border-[#E0E0E0] flex justify-between text-[12px]">
                <span class="text-[#37474F]">This month</span>
                <span class="font-bold text-[#1A1A1A]">{{ $userStats['new_month'] }} users</span>
            </div>
        </div>
    </div>

    {{-- ══ PENDING APPROVALS + RECENT ORDERS ══════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Pending Listings --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h3 class="text-[14px] font-bold text-[#1A1A1A]">Pending Approvals</h3>
                    <p class="text-[12px] text-[#37474F]">Listings awaiting review</p>
                </div>
                <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            @if($pendingListings->isNotEmpty())
            <div class="divide-y divide-[#F0F0F0]">
                @foreach($pendingListings as $listing)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-10 h-10 rounded-[6px] bg-[#F5F5F5] border border-[#E0E0E0] flex-shrink-0 overflow-hidden">
                        @if($listing->primaryImage)
                            <img src="{{ asset('storage/'.$listing->primaryImage->image_path) }}"
                                 class="w-full h-full object-cover" alt="">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-image text-[#E0E0E0] text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A] truncate">{{ $listing->title }}</p>
                        <p class="text-[11px] text-[#37474F]">
                            {{ $listing->user?->name ?? '—' }}
                            @if($listing->category)· {{ $listing->category->name }}@endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <form method="POST" action="{{ route('admin.listings.approval.approve', $listing) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1 h-[26px] px-2.5 bg-green-600 text-white text-[11px] font-semibold rounded hover:bg-green-700 transition">
                                <i class="fa-solid fa-check text-[9px]"></i> Approve
                            </button>
                        </form>
                        <a href="{{ route('admin.listings.show', $listing) }}"
                           class="w-[26px] h-[26px] inline-flex items-center justify-center border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-12 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-50 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                </div>
                <p class="text-[13px] font-semibold text-[#1A1A1A]">All caught up!</p>
                <p class="text-[12px] text-[#37474F] mt-0.5">No listings pending approval</p>
            </div>
            @endif
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h3 class="text-[14px] font-bold text-[#1A1A1A]">Recent Orders</h3>
                    <p class="text-[12px] text-[#37474F]">Latest platform transactions</p>
                </div>
                <a href="{{ route('admin.listings.index') }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-semibold rounded hover:bg-gray-50 transition">
                    Listings
                </a>
            </div>
            @if($recentOrders->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#F9F9F9] border-b border-[#E0E0E0]">
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Order</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Buyer</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Amount</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F0F0]">
                        @foreach($recentOrders as $order)
                        @php
                            $sc = [
                                'pending'    => 'bg-yellow-50 text-yellow-700',
                                'processing' => 'bg-blue-50 text-blue-700',
                                'shipped'    => 'bg-indigo-50 text-indigo-700',
                                'delivered'  => 'bg-teal-50 text-teal-700',
                                'completed'  => 'bg-green-50 text-green-700',
                                'cancelled'  => 'bg-red-50 text-red-600',
                            ][$order->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-[#F9F9F9] transition-colors">
                            <td class="px-4 py-2.5">
                                <span class="text-[12px] font-mono text-[#37474F]">#{{ $order->order_number ?? $order->id }}</span>
                                <p class="text-[11px] text-[#37474F]">{{ $order->created_at->format('M d') }}</p>
                            </td>
                            <td class="px-4 py-2.5 text-[13px] text-[#1A1A1A] max-w-[120px] truncate">
                                {{ $order->buyer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="text-[13px] font-bold text-[#1A1A1A]">${{ number_format($order->subtotal, 2) }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $sc }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-12 text-center">
                <i class="fa-solid fa-receipt text-3xl text-[#E0E0E0] mb-2 block"></i>
                <p class="text-[13px] text-[#37474F]">No orders yet</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ RECENT USERS + TOP SELLERS ══════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Recent Users --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h3 class="text-[14px] font-bold text-[#1A1A1A]">Recent Users</h3>
                    <p class="text-[12px] text-[#37474F]">Newly registered members</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="divide-y divide-[#F0F0F0]">
                @forelse($recentUsers as $u)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-9 h-9 rounded-full bg-[#D4AF37]/10 flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($u->avatar)
                            <img src="{{ asset('storage/'.$u->avatar) }}" class="w-full h-full object-cover" alt="">
                        @else
                            <span class="text-[#D4AF37] font-bold text-sm">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A] truncate">{{ $u->name }}</p>
                        <p class="text-[11px] text-[#37474F] truncate">{{ $u->email }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold
                            {{ $u->current_role === 'business_user' ? 'bg-[#D4AF37]/15 text-[#D4AF37]' : 'bg-[#E0E0E0] text-[#37474F]' }}">
                            {{ $u->current_role === 'business_user' ? 'Business' : 'User' }}
                        </span>
                        <span class="text-[11px] text-[#37474F]">{{ $u->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-[13px] text-[#37474F]">No users yet</div>
                @endforelse
            </div>
        </div>

        {{-- Top Sellers --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h3 class="text-[14px] font-bold text-[#1A1A1A]">Top Sellers</h3>
                    <p class="text-[12px] text-[#37474F]">By completed order revenue</p>
                </div>
                <i class="fa-solid fa-trophy text-[#D4AF37]"></i>
            </div>
            <div class="divide-y divide-[#F0F0F0]">
                @forelse($topSellers as $i => $row)
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-[#D4AF37] text-[#000000]' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-orange-200 text-orange-700' : 'bg-[#F5F5F5] text-[#37474F]')) }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A] truncate">{{ $row->seller?->name ?? 'Unknown' }}</p>
                        <p class="text-[11px] text-[#37474F] truncate">{{ $row->seller?->email ?? '' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[13px] font-bold text-[#1A1A1A]">${{ number_format($row->total_revenue, 2) }}</p>
                        <p class="text-[11px] text-[#37474F]">{{ $row->sales_count }} sales</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <i class="fa-solid fa-trophy text-3xl text-[#E0E0E0] mb-2 block"></i>
                    <p class="text-[13px] text-[#37474F]">No completed sales yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ QUICK ACTIONS ════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-[14px] font-bold text-[#1A1A1A] mb-3">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $actions = [
                    ['icon' => 'fa-clock',       'label' => 'Review Listings',  'sub' => $listingStats['pending'].' pending',  'url' => route('admin.listings.index', ['status' => 'pending']), 'gold' => true],
                    ['icon' => 'fa-users',        'label' => 'Manage Users',     'sub' => $userStats['total'].' total',         'url' => route('admin.users.index'),    'gold' => false],
                    ['icon' => 'fa-layer-group',  'label' => 'All Listings',     'sub' => $listingStats['total'].' total',      'url' => route('admin.listings.index'), 'gold' => false],
                    ['icon' => 'fa-gear',         'label' => 'Settings',         'sub' => 'Platform config',                   'url' => route('admin.settings.index'), 'gold' => false],
                ];
            @endphp
            @foreach($actions as $qa)
            <a href="{{ $qa['url'] }}"
               class="flex flex-col items-start gap-2 bg-white border rounded-[8px] px-4 py-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 hover:shadow-md transition-all
                      {{ $qa['gold'] ? 'border-[#D4AF37]/30' : 'border-[#E0E0E0]' }}">
                <div class="w-9 h-9 rounded-[6px] flex items-center justify-center
                    {{ $qa['gold'] ? 'bg-[#D4AF37] text-[#000000]' : 'bg-[#F5F5F5] text-[#D4AF37]' }}">
                    <i class="fa-solid {{ $qa['icon'] }} text-sm"></i>
                </div>
                <div>
                    <p class="text-[13px] font-bold text-[#1A1A1A]">{{ $qa['label'] }}</p>
                    <p class="text-[11px] text-[#37474F]">{{ $qa['sub'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const gold  = '#D4AF37';
    const goldT = 'rgba(212,175,55,0.10)';
    const blue  = '#3B82F6';
    const blueT = 'rgba(59,130,246,0.10)';
    const gray  = '#37474F';

    const monthly = @json($monthlyTrend->values());
    const regs    = @json($registrationTrend->values());

    // Revenue + Orders dual-axis
    new Chart(document.getElementById('revChart'), {
        type: 'line',
        data: {
            labels: monthly.map(d => d.label),
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: monthly.map(d => d.revenue),
                    borderColor: gold,
                    backgroundColor: goldT,
                    borderWidth: 2,
                    pointBackgroundColor: gold,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'yRev',
                },
                {
                    label: 'Orders',
                    data: monthly.map(d => d.orders),
                    borderColor: blue,
                    backgroundColor: blueT,
                    borderWidth: 2,
                    pointBackgroundColor: blue,
                    pointRadius: 3,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'yOrd',
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label === 'Revenue ($)'
                            ? ' $' + Number(ctx.parsed.y).toFixed(2)
                            : ' ' + ctx.parsed.y + ' orders',
                    },
                },
            },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { color: gray, font: { size: 11 } } },
                yRev: {
                    type: 'linear', position: 'left', beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { color: gray, font: { size: 11 }, callback: v => '$' + v },
                },
                yOrd: {
                    type: 'linear', position: 'right', beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: { color: blue, font: { size: 11 } },
                },
            },
        },
    });

    // Registrations bar chart
    new Chart(document.getElementById('regChart'), {
        type: 'bar',
        data: {
            labels: regs.map(d => d.label),
            datasets: [{
                label: 'New Users',
                data: regs.map(d => d.count),
                backgroundColor: goldT,
                borderColor: gold,
                borderWidth: 1.5,
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' users' } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: gray, font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { color: gray, font: { size: 11 }, precision: 0 },
                },
            },
        },
    });
}());
</script>
@endpush

@endsection
