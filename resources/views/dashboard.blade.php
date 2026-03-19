<x-app-layout>
    <x-slot name="header">
        <span class="text-[13px] font-semibold text-[#E0E0E0]">Dashboard</span>
    </x-slot>

    @push('styles')
    <style>
        .kpi-card { transition: transform .15s ease, box-shadow .15s ease; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.10); }
        .quick-action { transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
        .quick-action:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212,175,55,.12); border-color: #D4AF37; }
        .dash-table tr:hover td { background: rgba(212,175,55,.04); }
    </style>
    @endpush

    @php $user = auth()->user(); @endphp

    {{-- ══════════════════════════════════════════════════════════════
         GREETING BAR
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#D4AF37]/15 border-2 border-[#D4AF37]/40 flex items-center justify-center flex-shrink-0 overflow-hidden">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-[#D4AF37] font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#1A1A1A]">Welcome back, {{ $user->name }}</h1>
                <p class="text-[13px] text-[#37474F] mt-0.5">
                    <i class="fa-regular fa-calendar text-[#D4AF37] mr-1"></i>
                    {{ now()->format('l, F j Y') }}
                    @if($unreadCount > 0)
                        · <span class="text-[#D4AF37] font-semibold">{{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('user.listings.create') }}"
               class="inline-flex items-center gap-2 h-[36px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i> New Listing
            </a>
            <a href="{{ route('user.sales.index') }}"
               class="inline-flex items-center gap-2 h-[36px] px-4 border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                <i class="fa-solid fa-chart-line text-xs"></i> My Sales
            </a>
            <a href="{{ route('user.purchases.index') }}"
               class="inline-flex items-center gap-2 h-[36px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-bag-shopping text-xs"></i> Purchases
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         KPI CARDS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Total Listings --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-[#D4AF37]/10 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-[#D4AF37]"></i>
                </div>
                @if($listingStats['limit'] !== null)
                    <span class="text-[11px] font-semibold text-[#37474F]">{{ $listingStats['total'] }}/{{ $listingStats['limit'] }}</span>
                @endif
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($listingStats['total']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Listings</p>
            <div class="flex items-center gap-2 mt-2 flex-wrap">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-green-700 bg-green-50 px-1.5 py-0.5 rounded">
                    <i class="fa-solid fa-circle text-[6px]"></i> {{ $listingStats['active'] }} active
                </span>
                @if($listingStats['pending'] > 0)
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded">
                        {{ $listingStats['pending'] }} pending
                    </span>
                @endif
            </div>
            @if($listingStats['limit'] !== null)
                <div class="mt-3">
                    <div class="w-full bg-[#E0E0E0] rounded-full h-1">
                        <div class="bg-[#D4AF37] h-1 rounded-full" style="width: {{ min(100, ($listingStats['total'] / max(1,$listingStats['limit'])) * 100) }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Total Revenue --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-green-50 flex items-center justify-center">
                    <i class="fa-solid fa-dollar-sign text-green-600"></i>
                </div>
                <span class="text-[11px] font-semibold text-[#D4AF37]">
                    +${{ number_format($salesStats['revenue_month'], 0) }} mo
                </span>
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">${{ number_format($salesStats['revenue'], 2) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Revenue</p>
            <p class="text-[11px] text-[#37474F] mt-2">
                <span class="text-[#D4AF37] font-semibold">${{ number_format($salesStats['revenue_month'], 2) }}</span> this month
            </p>
        </div>

        {{-- Sales --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-blue-600"></i>
                </div>
                @if($salesStats['cancelled'] > 0)
                    <span class="text-[11px] font-semibold text-red-500">{{ $salesStats['cancelled'] }} cancelled</span>
                @endif
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($salesStats['total']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Total Orders</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-green-700 bg-green-50 px-1.5 py-0.5 rounded">
                    {{ $salesStats['completed'] }} completed
                </span>
            </div>
        </div>

        {{-- Pending + Purchases --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-[8px] bg-yellow-50 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-yellow-600"></i>
                </div>
                @if($salesStats['pending'] > 0)
                    <span class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-100 text-yellow-700">
                        Needs action
                    </span>
                @endif
            </div>
            <p class="text-[clamp(1.5rem,3vw,2.25rem)] font-bold text-[#1A1A1A] leading-none">{{ number_format($salesStats['pending']) }}</p>
            <p class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wide mt-1.5">Pending Orders</p>
            <p class="text-[11px] text-[#37474F] mt-2">
                <i class="fa-solid fa-bag-shopping text-[#D4AF37] mr-1"></i>
                <span class="font-semibold text-[#1A1A1A]">{{ $purchaseStats['total'] }}</span> purchases made
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CHARTS SECTION
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6"
         x-data="{ period: 'monthly' }">

        {{-- Main Chart --}}
        <div class="lg:col-span-2 bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="text-[14px] font-bold text-[#1A1A1A]">Sales Analytics</h2>
                    <p class="text-[12px] text-[#37474F] mt-0.5">Revenue & order volume over time</p>
                </div>
                <div class="flex items-center gap-1 bg-[#F5F5F5] rounded-[6px] p-0.5">
                    <button @click="period = 'weekly'"
                            :class="period === 'weekly' ? 'bg-white shadow text-[#1A1A1A]' : 'text-[#37474F]'"
                            class="h-[28px] px-3 rounded text-[12px] font-semibold transition-all">
                        7 Days
                    </button>
                    <button @click="period = 'monthly'"
                            :class="period === 'monthly' ? 'bg-white shadow text-[#1A1A1A]' : 'text-[#37474F]'"
                            class="h-[28px] px-3 rounded text-[12px] font-semibold transition-all">
                        6 Months
                    </button>
                </div>
            </div>
            <div x-show="period === 'monthly'"><canvas id="chartMonthly" height="110"></canvas></div>
            <div x-show="period === 'weekly'"  x-cloak><canvas id="chartWeekly"  height="110"></canvas></div>
        </div>

        {{-- Orders Breakdown donut + stats --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)] flex flex-col">
            <h2 class="text-[14px] font-bold text-[#1A1A1A] mb-1">Orders Overview</h2>
            <p class="text-[12px] text-[#37474F] mb-4">Status breakdown</p>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="ordersDonut" height="160"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @php
                    $orderBreakdown = [
                        ['label' => 'Completed', 'value' => $salesStats['completed'], 'color' => 'bg-green-500'],
                        ['label' => 'Pending',   'value' => $salesStats['pending'],   'color' => 'bg-yellow-500'],
                        ['label' => 'Cancelled', 'value' => $salesStats['cancelled'], 'color' => 'bg-red-400'],
                    ];
                    $totalOrders = max(1, $salesStats['total']);
                @endphp
                @foreach($orderBreakdown as $ob)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $ob['color'] }} flex-shrink-0"></div>
                    <span class="text-[12px] text-[#37474F] flex-1">{{ $ob['label'] }}</span>
                    <span class="text-[12px] font-semibold text-[#1A1A1A]">{{ $ob['value'] }}</span>
                    <span class="text-[11px] text-[#37474F]">({{ $totalOrders > 0 ? number_format(($ob['value']/$totalOrders)*100,0) : 0 }}%)</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RECENT SALES + NOTIFICATIONS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Recent Sales Table --}}
        <div class="lg:col-span-2 bg-white border border-[#E0E0E0] rounded-[10px] shadow-[0_1px_4px_rgba(0,0,0,0.07)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h2 class="text-[14px] font-bold text-[#1A1A1A]">Recent Sales</h2>
                    <p class="text-[12px] text-[#37474F]">Orders received as seller</p>
                </div>
                <a href="{{ route('user.sales.index') }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            @if($recentSales->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left dash-table">
                    <thead>
                        <tr class="bg-[#F9F9F9] border-b border-[#E0E0E0]">
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Order</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Product</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide hidden sm:table-cell">Buyer</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Amount</th>
                            <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F0F0]">
                        @foreach($recentSales as $order)
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
                        <tr class="transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-[12px] text-[#37474F] font-mono">#{{ $order->order_number ?? $order->id }}</span>
                                <p class="text-[11px] text-[#37474F]">{{ $order->created_at->format('M d') }}</p>
                            </td>
                            <td class="px-4 py-3 max-w-[150px]">
                                <span class="text-[13px] font-medium text-[#1A1A1A] truncate block">{{ $order->listing?->title ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell text-[13px] text-[#37474F]">
                                {{ $order->buyer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-[13px] font-bold text-[#1A1A1A]">${{ number_format($order->subtotal, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
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
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-[#E0E0E0] text-xl"></i>
                </div>
                <p class="text-[13px] font-semibold text-[#1A1A1A]">No sales yet</p>
                <a href="{{ route('user.listings.create') }}"
                   class="mt-2 text-[12px] text-[#D4AF37] hover:underline inline-block">Create a listing to start selling</a>
            </div>
            @endif
        </div>

        {{-- Notifications --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] shadow-[0_1px_4px_rgba(0,0,0,0.07)] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h2 class="text-[14px] font-bold text-[#1A1A1A]">Notifications</h2>
                    @if($unreadCount > 0)
                        <p class="text-[12px] text-[#D4AF37] font-semibold">{{ $unreadCount }} unread</p>
                    @else
                        <p class="text-[12px] text-[#37474F]">All caught up</p>
                    @endif
                </div>
                <a href="{{ route('user.notifications.index') }}"
                   class="text-[12px] text-[#D4AF37] hover:underline font-medium">View all</a>
            </div>
            <div class="flex-1 divide-y divide-[#F0F0F0] overflow-hidden">
                @forelse($recentNotifications as $notification)
                @php $nd = $notification->data; @endphp
                <div class="flex items-start gap-3 px-4 py-3 {{ $notification->read_at ? '' : 'bg-[#D4AF37]/5' }} hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                        {{ ($nd['type'] ?? '') === 'listing_approved' ? 'bg-green-50' : (($nd['type'] ?? '') === 'listing_rejected' ? 'bg-red-50' : 'bg-[#D4AF37]/10') }}">
                        @if(($nd['type'] ?? '') === 'listing_approved')
                            <i class="fa-solid fa-circle-check text-green-500 text-[11px]"></i>
                        @elseif(($nd['type'] ?? '') === 'listing_rejected')
                            <i class="fa-solid fa-circle-xmark text-red-500 text-[11px]"></i>
                        @else
                            <i class="fa-solid fa-bell text-[#D4AF37] text-[11px]"></i>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[12px] font-medium text-[#1A1A1A] leading-snug">
                            {{ $nd['message'] ?? ($nd['title'] ?? 'Notification') }}
                        </p>
                        <p class="text-[11px] text-[#37474F] mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(! $notification->read_at)
                        <div class="w-2 h-2 rounded-full bg-[#D4AF37] flex-shrink-0 mt-1.5"></div>
                    @endif
                </div>
                @empty
                <div class="py-10 text-center">
                    <i class="fa-solid fa-bell-slash text-3xl text-[#E0E0E0] mb-2 block"></i>
                    <p class="text-[12px] text-[#37474F]">No notifications yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MY PRODUCTS + TOP PERFORMERS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- My Recent Products --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] shadow-[0_1px_4px_rgba(0,0,0,0.07)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h2 class="text-[14px] font-bold text-[#1A1A1A]">My Products</h2>
                    <p class="text-[12px] text-[#37474F]">Recently added listings</p>
                </div>
                <a href="{{ route('user.listings.index') }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    Manage <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="divide-y divide-[#F0F0F0]">
                @forelse($recentListings as $listing)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-11 h-11 rounded-[6px] bg-[#F5F5F5] flex-shrink-0 overflow-hidden border border-[#E0E0E0]">
                        @if($listing->primaryImage)
                            <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}"
                                 alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-image text-[#E0E0E0]"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A] truncate">{{ $listing->title }}</p>
                        <p class="text-[11px] text-[#37474F]">
                            @if($listing->base_price)
                                <span class="text-[#D4AF37] font-semibold">${{ number_format($listing->base_price, 2) }}</span>
                            @else
                                <span>No price set</span>
                            @endif
                            @if($listing->listingType)
                                · {{ $listing->listingType->name }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @php
                            $lsc = [
                                'active'   => 'bg-green-50 text-green-700',
                                'pending'  => 'bg-yellow-50 text-yellow-700',
                                'expired'  => 'bg-gray-100 text-gray-500',
                                'rejected' => 'bg-red-50 text-red-600',
                                'draft'    => 'bg-gray-100 text-gray-500',
                            ][$listing->status] ?? 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $lsc }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                        <a href="{{ route('user.listings.edit', $listing->slug) }}"
                           class="w-7 h-7 inline-flex items-center justify-center border border-[#E0E0E0] text-[#37474F] rounded-[4px] hover:border-[#D4AF37] hover:text-[#D4AF37] transition"
                           title="Edit">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </a>
                        <a href="{{ route('listings.show', $listing->slug) }}"
                           class="w-7 h-7 inline-flex items-center justify-center border border-[#E0E0E0] text-[#37474F] rounded-[4px] hover:border-[#D4AF37] hover:text-[#D4AF37] transition"
                           title="View" target="_blank">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                        <i class="fa-solid fa-box text-[#E0E0E0] text-xl"></i>
                    </div>
                    <p class="text-[13px] font-semibold text-[#1A1A1A]">No listings yet</p>
                    <a href="{{ route('user.listings.create') }}"
                       class="mt-2 text-[12px] text-[#D4AF37] hover:underline inline-block">Create your first listing</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top Performing --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] shadow-[0_1px_4px_rgba(0,0,0,0.07)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <div>
                    <h2 class="text-[14px] font-bold text-[#1A1A1A]">Top Performing</h2>
                    <p class="text-[12px] text-[#37474F]">By completed sales</p>
                </div>
                <i class="fa-solid fa-trophy text-[#D4AF37]"></i>
            </div>
            <div class="divide-y divide-[#F0F0F0]">
                @forelse($topListings as $i => $row)
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#F9F9F9] transition-colors">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-[#D4AF37] text-[#000000]' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-orange-200 text-orange-700' : 'bg-[#F5F5F5] text-[#37474F]')) }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A] truncate">
                            {{ $row->listing?->title ?? 'Deleted listing' }}
                        </p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] text-[#37474F]">{{ $row->sales_count }} {{ Str::plural('sale', $row->sales_count) }}</span>
                            <span class="w-1 h-1 rounded-full bg-[#E0E0E0]"></span>
                            @if($row->listing?->base_price)
                                <span class="text-[11px] text-[#37474F]">${{ number_format($row->listing->base_price, 2) }} each</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[13px] font-bold text-[#1A1A1A]">${{ number_format($row->total_revenue, 2) }}</p>
                        <p class="text-[11px] text-[#37474F]">revenue</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                        <i class="fa-solid fa-trophy text-[#E0E0E0] text-xl"></i>
                    </div>
                    <p class="text-[13px] font-semibold text-[#1A1A1A]">No completed sales yet</p>
                    <p class="text-[12px] text-[#37474F] mt-1">Your top products will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         QUICK ACTIONS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="mb-6">
        <h2 class="text-[14px] font-bold text-[#1A1A1A] mb-3">Quick Actions</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $quickActions = [
                    ['icon' => 'fa-plus',         'label' => 'New Listing',       'sub' => 'Add a product',        'route' => route('user.listings.create'),   'gold' => true],
                    ['icon' => 'fa-list',          'label' => 'Manage Listings',   'sub' => 'Edit or delete',       'route' => route('user.listings.index'),    'gold' => false],
                    ['icon' => 'fa-receipt',       'label' => 'View Orders',       'sub' => 'Track your sales',     'route' => route('user.sales.index'),       'gold' => false],
                    ['icon' => 'fa-bag-shopping',  'label' => 'My Purchases',      'sub' => 'Orders I placed',      'route' => route('user.purchases.index'),   'gold' => false],
                ];
            @endphp
            @foreach($quickActions as $qa)
            <a href="{{ $qa['route'] }}"
               class="quick-action flex flex-col items-start gap-2 bg-white border rounded-[10px] px-4 py-4 shadow-[0_1px_4px_rgba(0,0,0,0.07)]
                      {{ $qa['gold'] ? 'border-[#D4AF37]/30 bg-[#D4AF37]/5' : 'border-[#E0E0E0]' }}">
                <div class="w-9 h-9 rounded-[8px] flex items-center justify-center
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

    {{-- ══════════════════════════════════════════════════════════════
         PROFILE + LISTING STATUS + ACCOUNT SUMMARY
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Profile Card --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-[#D4AF37]/10 border-2 border-[#D4AF37]/30 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-[#D4AF37] font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-[14px] font-bold text-[#1A1A1A] truncate">{{ $user->name }}</p>
                    <p class="text-[12px] text-[#37474F] truncate">{{ $user->email }}</p>
                    <span class="inline-flex items-center h-4 px-2 rounded text-[10px] font-semibold mt-0.5
                        {{ $user->isBusinessUser() ? 'bg-[#D4AF37]/15 text-[#D4AF37]' : 'bg-[#E0E0E0] text-[#37474F]' }}">
                        {{ $user->isBusinessUser() ? 'Business' : 'Standard' }}
                    </span>
                </div>
            </div>
            <div class="space-y-2 text-[12px] mb-4">
                @if($user->phone)
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-phone text-[#D4AF37] w-4 text-center text-[11px]"></i>
                    {{ $user->phone }}
                </div>
                @endif
                @if($user->isBusinessUser() && $user->business_valid_until)
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-calendar-check text-[#D4AF37] w-4 text-center text-[11px]"></i>
                    Business until {{ $user->business_valid_until->format('M d, Y') }}
                </div>
                @endif
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-calendar text-[#D4AF37] w-4 text-center text-[11px]"></i>
                    Member since {{ $user->created_at->format('M Y') }}
                </div>
            </div>
            <a href="{{ route('profile.edit') }}"
               class="w-full inline-flex items-center justify-center gap-2 h-[34px] border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                <i class="fa-solid fa-pen text-[10px]"></i> Edit Profile
            </a>
        </div>

        {{-- Listing Status Breakdown --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <h2 class="text-[14px] font-bold text-[#1A1A1A] mb-4">Listing Status</h2>
            <div class="space-y-3">
                @php
                    $statusBars = [
                        ['label' => 'Active',   'value' => $listingStats['active'],   'color' => 'bg-green-500'],
                        ['label' => 'Pending',  'value' => $listingStats['pending'],  'color' => 'bg-yellow-500'],
                        ['label' => 'Draft',    'value' => $listingStats['draft'],    'color' => 'bg-gray-300'],
                        ['label' => 'Expired',  'value' => $listingStats['expired'],  'color' => 'bg-gray-400'],
                        ['label' => 'Rejected', 'value' => $listingStats['rejected'], 'color' => 'bg-red-400'],
                    ];
                    $totalL = max(1, $listingStats['total']);
                @endphp
                @foreach($statusBars as $sb)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-[12px] text-[#37474F]">{{ $sb['label'] }}</span>
                        <span class="text-[12px] font-semibold text-[#1A1A1A]">{{ $sb['value'] }}</span>
                    </div>
                    <div class="w-full bg-[#F0F0F0] rounded-full h-1.5">
                        <div class="{{ $sb['color'] }} h-1.5 rounded-full" style="width: {{ number_format(($sb['value']/$totalL)*100,1) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('user.listings.create') }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-[32px] bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-plus text-[10px]"></i> New
                </a>
                <a href="{{ route('user.listings.index') }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-[32px] border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    <i class="fa-solid fa-list text-[10px]"></i> All
                </a>
            </div>
        </div>

        {{-- Account Summary --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[10px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.07)]">
            <h2 class="text-[14px] font-bold text-[#1A1A1A] mb-4">Account Summary</h2>
            <div class="space-y-3">
                @php
                    $summaryRows = [
                        ['icon' => 'fa-layer-group',   'label' => 'Listings',          'value' => number_format($listingStats['total'])],
                        ['icon' => 'fa-receipt',        'label' => 'Total Orders',       'value' => number_format($salesStats['total'])],
                        ['icon' => 'fa-circle-check',   'label' => 'Completed',          'value' => number_format($salesStats['completed'])],
                        ['icon' => 'fa-bag-shopping',   'label' => 'Purchases Made',     'value' => number_format($purchaseStats['total'])],
                        ['icon' => 'fa-dollar-sign',    'label' => 'Total Spent',        'value' => '$' . number_format($purchaseStats['spent'], 2)],
                        ['icon' => 'fa-bell',           'label' => 'Unread Notif.',      'value' => $unreadCount],
                    ];
                @endphp
                @foreach($summaryRows as $sr)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid {{ $sr['icon'] }} text-[#D4AF37] text-[11px]"></i>
                        </div>
                        <span class="text-[13px] text-[#37474F]">{{ $sr['label'] }}</span>
                    </div>
                    <span class="text-[13px] font-bold text-[#1A1A1A]">{{ $sr['value'] }}</span>
                </div>
                @endforeach
            </div>
            @if(! $user->isBusinessUser())
            <div class="mt-5 pt-4 border-t border-[#E0E0E0]">
                <p class="text-[12px] text-[#37474F] mb-2">Unlock business features</p>
                <a href="{{ route('business.listings.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 h-[32px] bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-building text-[10px]"></i> Go Business
                </a>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const gold   = '#D4AF37';
        const goldT  = 'rgba(212,175,55,0.10)';
        const gray   = '#37474F';
        const blue   = '#3B82F6';
        const blueT  = 'rgba(59,130,246,0.10)';

        const monthlyData = @json($monthlyTrend->values());
        const weeklyData  = @json($weeklyTrend->values());

        function makeLineChart(id, data) {
            const el = document.getElementById(id);
            if (!el) { return; }
            return new Chart(el, {
                type: 'line',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [
                        {
                            label: 'Revenue ($)',
                            data: data.map(d => d.revenue),
                            borderColor: gold,
                            backgroundColor: goldT,
                            borderWidth: 2,
                            pointBackgroundColor: gold,
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'yRevenue',
                        },
                        {
                            label: 'Orders',
                            data: data.map(d => d.orders),
                            borderColor: blue,
                            backgroundColor: blueT,
                            borderWidth: 2,
                            pointBackgroundColor: blue,
                            pointRadius: 3,
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'yOrders',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { color: gray, font: { size: 11 }, boxWidth: 10, padding: 12 },
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label === 'Revenue ($)'
                                    ? ' $' + Number(ctx.parsed.y).toFixed(2)
                                    : ' ' + ctx.parsed.y + ' orders',
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0,0,0,.04)' },
                            ticks: { color: gray, font: { size: 11 } },
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,.04)' },
                            ticks: { color: gray, font: { size: 11 }, callback: v => '$' + v },
                        },
                        yOrders: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            ticks: { color: blue, font: { size: 11 } },
                        },
                    },
                },
            });
        }

        makeLineChart('chartMonthly', monthlyData);
        makeLineChart('chartWeekly',  weeklyData);

        // Orders donut
        new Chart(document.getElementById('ordersDonut'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $salesStats['completed'] }},
                        {{ $salesStats['pending'] }},
                        {{ $salesStats['cancelled'] }},
                    ],
                    backgroundColor: ['#22c55e', '#eab308', '#f87171'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed,
                        },
                    },
                },
            },
        });
    }());
    </script>
    @endpush
</x-app-layout>
