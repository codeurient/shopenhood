<x-app-layout>
    <x-slot name="header">
        <span class="text-[13px] font-semibold text-[#E0E0E0]">Dashboard</span>
    </x-slot>

    @push('styles')
    <style>
        .kpi-card { transition: transform .15s, box-shadow .15s; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.12); }
    </style>
    @endpush

    {{-- ── Greeting bar ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-[13px] text-[#37474F] mt-0.5">{{ now()->format('l, F j Y') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('user.listings.create') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i> New Listing
            </a>
            <a href="{{ route('user.sales.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                <i class="fa-solid fa-chart-line text-xs"></i> My Sales
            </a>
        </div>
    </div>

    {{-- ── KPI Row ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Listings --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[8px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] text-[#37474F] uppercase tracking-wide font-semibold">Listings</p>
                    <p class="text-[clamp(1.25rem,2.5vw,2rem)] font-bold text-[#1A1A1A] mt-1">{{ number_format($listingStats['total']) }}</p>
                    <p class="text-[12px] text-[#37474F] mt-1">
                        <span class="text-green-600 font-semibold">{{ $listingStats['active'] }}</span> active
                        @if($listingStats['pending'] > 0)
                            · <span class="text-yellow-600 font-semibold">{{ $listingStats['pending'] }}</span> pending
                        @endif
                    </p>
                </div>
                <div class="w-9 h-9 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-list text-[#D4AF37]"></i>
                </div>
            </div>
            @if($listingStats['limit'] !== null)
                <div class="mt-3">
                    <div class="w-full bg-[#E0E0E0] rounded-full h-1">
                        <div class="bg-[#D4AF37] h-1 rounded-full" style="width: {{ min(100, ($listingStats['total'] / max(1, $listingStats['limit'])) * 100) }}%"></div>
                    </div>
                    <p class="text-[11px] text-[#37474F] mt-1">{{ $listingStats['total'] }} / {{ $listingStats['limit'] }} limit</p>
                </div>
            @endif
        </div>

        {{-- Revenue --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[8px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] text-[#37474F] uppercase tracking-wide font-semibold">Total Revenue</p>
                    <p class="text-[clamp(1.25rem,2.5vw,2rem)] font-bold text-[#1A1A1A] mt-1">${{ number_format($salesStats['revenue'], 2) }}</p>
                    <p class="text-[12px] text-[#37474F] mt-1">
                        <span class="text-[#D4AF37] font-semibold">${{ number_format($salesStats['revenue_month'], 2) }}</span> this month
                    </p>
                </div>
                <div class="w-9 h-9 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-dollar-sign text-[#D4AF37]"></i>
                </div>
            </div>
        </div>

        {{-- Completed Sales --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[8px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] text-[#37474F] uppercase tracking-wide font-semibold">Completed Sales</p>
                    <p class="text-[clamp(1.25rem,2.5vw,2rem)] font-bold text-[#1A1A1A] mt-1">{{ number_format($salesStats['completed']) }}</p>
                    <p class="text-[12px] text-[#37474F] mt-1">
                        <span class="text-[#C0392B] font-semibold">{{ $salesStats['cancelled'] }}</span> cancelled
                    </p>
                </div>
                <div class="w-9 h-9 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                </div>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div class="kpi-card bg-white border border-[#E0E0E0] rounded-[8px] p-4 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[12px] text-[#37474F] uppercase tracking-wide font-semibold">Pending Orders</p>
                    <p class="text-[clamp(1.25rem,2.5vw,2rem)] font-bold text-[#1A1A1A] mt-1">{{ number_format($salesStats['pending']) }}</p>
                    <p class="text-[12px] text-[#37474F] mt-1">
                        <span class="text-[#37474F] font-semibold">{{ $purchaseStats['total'] }}</span> purchases made
                    </p>
                </div>
                <div class="w-9 h-9 rounded-full bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Chart + Notifications ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Sales Trend Chart --}}
        <div class="lg:col-span-2 bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-[14px] font-semibold text-[#1A1A1A]">Sales Trend</h2>
                    <p class="text-[12px] text-[#37474F]">Last 6 months revenue</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-[#D4AF37] font-semibold">
                    <i class="fa-solid fa-circle text-[8px]"></i> Revenue
                </span>
            </div>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        {{-- Notifications --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] flex flex-col">
            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-[#E0E0E0]">
                <div>
                    <h2 class="text-[14px] font-semibold text-[#1A1A1A]">Notifications</h2>
                    @if($unreadCount > 0)
                        <p class="text-[12px] text-[#37474F]">{{ $unreadCount }} unread</p>
                    @endif
                </div>
                <a href="{{ route('user.notifications.index') }}"
                   class="text-[12px] text-[#D4AF37] hover:underline font-medium">View all</a>
            </div>
            <div class="flex-1 divide-y divide-[#E0E0E0] overflow-hidden">
                @forelse($recentNotifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="flex items-start gap-3 px-5 py-3 {{ $notification->read_at ? '' : 'bg-[#D4AF37]/5' }}">
                        <div class="w-7 h-7 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            @if(($data['type'] ?? '') === 'listing_approved')
                                <i class="fa-solid fa-circle-check text-[#D4AF37] text-[11px]"></i>
                            @elseif(($data['type'] ?? '') === 'listing_rejected')
                                <i class="fa-solid fa-circle-xmark text-[#C0392B] text-[11px]"></i>
                            @else
                                <i class="fa-solid fa-bell text-[#D4AF37] text-[11px]"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[12px] text-[#1A1A1A] font-medium leading-snug truncate">
                                {{ $data['message'] ?? ($data['title'] ?? 'Notification') }}
                            </p>
                            <p class="text-[11px] text-[#37474F] mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(! $notification->read_at)
                            <span class="w-2 h-2 rounded-full bg-[#D4AF37] flex-shrink-0 mt-1.5"></span>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <i class="fa-solid fa-bell-slash text-2xl text-[#E0E0E0] mb-2 block"></i>
                        <p class="text-[12px] text-[#37474F]">No notifications yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Recent Sales + Listing Stats ──────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Recent Sales Table --}}
        <div class="lg:col-span-2 bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <h2 class="text-[14px] font-semibold text-[#1A1A1A]">Recent Sales</h2>
                <a href="{{ route('user.sales.index') }}" class="text-[12px] text-[#D4AF37] hover:underline font-medium">View all</a>
            </div>
            @if($recentSales->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#37474F]/10">
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Order</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Listing</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Buyer</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Amount</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-[#37474F] uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E0E0E0]">
                            @foreach($recentSales as $order)
                            <tr class="hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#37474F] font-mono">
                                    #{{ $order->order_number ?? $order->id }}
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#1A1A1A] max-w-[180px] truncate">
                                    {{ $order->listing?->title ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#37474F]">
                                    {{ $order->buyer?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-[13px] font-semibold text-[#1A1A1A]">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'pending'    => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'shipped'    => 'bg-indigo-100 text-indigo-700',
                                            'delivered'  => 'bg-teal-100 text-teal-700',
                                            'completed'  => 'bg-green-100 text-green-700',
                                            'cancelled'  => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center">
                    <i class="fa-solid fa-chart-line text-3xl text-[#E0E0E0] mb-3 block"></i>
                    <p class="text-[13px] text-[#37474F]">No sales yet</p>
                    <a href="{{ route('user.listings.create') }}" class="text-[13px] text-[#D4AF37] hover:underline mt-1 inline-block">Create a listing</a>
                </div>
            @endif
        </div>

        {{-- Listing Status Breakdown --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <h2 class="text-[14px] font-semibold text-[#1A1A1A] mb-4">Listing Status</h2>
            <div class="space-y-3">
                @php
                    $statusItems = [
                        ['label' => 'Active',   'value' => $listingStats['active'],   'color' => 'bg-green-500'],
                        ['label' => 'Pending',  'value' => $listingStats['pending'],  'color' => 'bg-yellow-500'],
                        ['label' => 'Expired',  'value' => $listingStats['expired'],  'color' => 'bg-gray-400'],
                        ['label' => 'Rejected', 'value' => $listingStats['rejected'], 'color' => 'bg-red-500'],
                    ];
                    $total = max(1, $listingStats['total']);
                @endphp
                @foreach($statusItems as $item)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-[13px] text-[#37474F]">{{ $item['label'] }}</span>
                        <span class="text-[13px] font-semibold text-[#1A1A1A]">{{ $item['value'] }}</span>
                    </div>
                    <div class="w-full bg-[#E0E0E0] rounded-full h-1.5">
                        <div class="{{ $item['color'] }} h-1.5 rounded-full transition-all"
                             style="width: {{ number_format(($item['value'] / $total) * 100, 1) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 pt-4 border-t border-[#E0E0E0]">
                <canvas id="listingDonut" height="140"></canvas>
            </div>

            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('user.listings.create') }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-[32px] px-3 bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-plus text-[10px]"></i> New
                </a>
                <a href="{{ route('user.listings.index') }}"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-[32px] px-3 border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                    <i class="fa-solid fa-list text-[10px]"></i> Manage
                </a>
            </div>
        </div>
    </div>

    {{-- ── My Listings + Top Performers ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Recent Listings --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <h2 class="text-[14px] font-semibold text-[#1A1A1A]">My Recent Listings</h2>
                <a href="{{ route('user.listings.index') }}" class="text-[12px] text-[#D4AF37] hover:underline font-medium">View all</a>
            </div>
            <div class="divide-y divide-[#E0E0E0]">
                @forelse($recentListings as $listing)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#D4AF37]/5 transition-colors">
                    {{-- Thumbnail --}}
                    <div class="w-10 h-10 rounded bg-[#E0E0E0] flex-shrink-0 overflow-hidden">
                        @if($listing->primaryImage)
                            <img src="{{ Storage::url($listing->primaryImage->image_path) }}"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-image text-[#37474F] text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium text-[#1A1A1A] truncate">{{ $listing->title }}</p>
                        <p class="text-[11px] text-[#37474F]">
                            @if($listing->base_price)
                                ${{ number_format($listing->base_price, 2) }}
                            @else
                                No price
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @php
                            $sc = [
                                'active'   => 'bg-green-100 text-green-700',
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'expired'  => 'bg-gray-100 text-gray-600',
                                'rejected' => 'bg-red-100 text-red-700',
                                'draft'    => 'bg-gray-100 text-gray-500',
                            ][$listing->status] ?? 'bg-gray-100 text-gray-500';
                        @endphp
                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $sc }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                        <a href="{{ route('user.listings.edit', $listing->slug) }}"
                           class="w-7 h-7 inline-flex items-center justify-center border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-50 transition">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center">
                    <i class="fa-solid fa-list text-3xl text-[#E0E0E0] mb-3 block"></i>
                    <p class="text-[13px] text-[#37474F]">No listings yet</p>
                    <a href="{{ route('user.listings.create') }}" class="text-[13px] text-[#D4AF37] hover:underline mt-1 inline-block">Create your first</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top Performing Listings --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E0E0E0]">
                <h2 class="text-[14px] font-semibold text-[#1A1A1A]">Top Selling Listings</h2>
                <span class="text-[12px] text-[#37474F]">By completed orders</span>
            </div>
            <div class="divide-y divide-[#E0E0E0]">
                @forelse($topListings as $i => $row)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-[#D4AF37]/5 transition-colors">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-[#D4AF37] text-[#000000]' : 'bg-[#E0E0E0] text-[#37474F]' }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium text-[#1A1A1A] truncate">
                            {{ $row->listing?->title ?? 'Deleted listing' }}
                        </p>
                        <p class="text-[11px] text-[#37474F]">{{ $row->sales_count }} {{ Str::plural('sale', $row->sales_count) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[13px] font-semibold text-[#1A1A1A]">${{ number_format($row->total_revenue, 2) }}</p>
                        <p class="text-[11px] text-[#37474F]">revenue</p>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center">
                    <i class="fa-solid fa-trophy text-3xl text-[#E0E0E0] mb-3 block"></i>
                    <p class="text-[13px] text-[#37474F]">No completed sales yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Account & Quick Links ─────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Profile card --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            @php $user = auth()->user(); @endphp
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-[#D4AF37]/10 border-2 border-[#D4AF37] flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-[#D4AF37] text-lg"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-[14px] font-semibold text-[#1A1A1A] truncate">{{ $user->name }}</p>
                    <p class="text-[12px] text-[#37474F] truncate">{{ $user->email }}</p>
                    <span class="inline-flex items-center h-4 px-1.5 rounded-[6px] text-[10px] font-semibold mt-0.5
                        {{ $user->isBusinessUser() ? 'bg-[#D4AF37]/20 text-[#D4AF37]' : 'bg-[#37474F]/20 text-[#37474F]' }}">
                        {{ $user->isBusinessUser() ? 'Business' : 'Standard' }}
                    </span>
                </div>
            </div>
            <div class="space-y-1.5 text-[12px]">
                @if($user->phone)
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-phone w-4 text-center text-[#D4AF37]"></i>
                    {{ $user->phone }}
                </div>
                @endif
                @if($user->isBusinessUser() && $user->business_valid_until)
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-calendar-check w-4 text-center text-[#D4AF37]"></i>
                    Business until {{ $user->business_valid_until->format('M d, Y') }}
                </div>
                @endif
                <div class="flex items-center gap-2 text-[#37474F]">
                    <i class="fa-solid fa-calendar w-4 text-center text-[#D4AF37]"></i>
                    Member since {{ $user->created_at->format('M Y') }}
                </div>
            </div>
            <a href="{{ route('profile.edit') }}"
               class="mt-4 w-full inline-flex items-center justify-center gap-2 h-[32px] border border-[#D4AF37] text-[#D4AF37] text-[12px] font-semibold rounded hover:bg-[#D4AF37]/10 transition">
                <i class="fa-solid fa-pen text-[10px]"></i> Edit Profile
            </a>
        </div>

        {{-- Quick links --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <h2 class="text-[14px] font-semibold text-[#1A1A1A] mb-4">Quick Links</h2>
            <div class="space-y-1.5">
                @php
                    $links = [
                        ['icon' => 'fa-list',         'label' => 'My Listings',         'route' => 'user.listings.index'],
                        ['icon' => 'fa-bag-shopping',  'label' => 'My Purchases',        'route' => 'user.purchases.index'],
                        ['icon' => 'fa-chart-line',    'label' => 'My Sales',            'route' => 'user.sales.index'],
                        ['icon' => 'fa-location-dot',  'label' => 'My Addresses',        'route' => 'user.addresses.index'],
                        ['icon' => 'fa-bell',          'label' => 'Notifications',       'route' => 'user.notifications.index'],
                        ['icon' => 'fa-heart',         'label' => 'Saved Listings',      'route' => 'user.favorites.index'],
                    ];
                @endphp
                @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 h-9 px-3 rounded text-[13px] text-[#37474F] hover:bg-[#D4AF37]/10 hover:text-[#1A1A1A] transition-colors">
                    <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-[#D4AF37] text-[12px]"></i>
                    {{ $link['label'] }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Account summary --}}
        <div class="bg-white border border-[#E0E0E0] rounded-[8px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <h2 class="text-[14px] font-semibold text-[#1A1A1A] mb-4">Account Summary</h2>
            <div class="space-y-3">
                @php
                    $summaryItems = [
                        ['label' => 'Total Listings',    'value' => $listingStats['total'],         'icon' => 'fa-list'],
                        ['label' => 'Total Sales',       'value' => $salesStats['total'],           'icon' => 'fa-receipt'],
                        ['label' => 'Completed',         'value' => $salesStats['completed'],       'icon' => 'fa-circle-check'],
                        ['label' => 'Purchases Made',    'value' => $purchaseStats['total'],        'icon' => 'fa-bag-shopping'],
                        ['label' => 'Notifications',     'value' => $unreadCount . ' unread',       'icon' => 'fa-bell'],
                    ];
                @endphp
                @foreach($summaryItems as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-[13px] text-[#37474F]">
                        <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-[#D4AF37] text-[12px]"></i>
                        {{ $item['label'] }}
                    </div>
                    <span class="text-[13px] font-semibold text-[#1A1A1A]">{{ $item['value'] }}</span>
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
        const gold  = '#D4AF37';
        const goldT = 'rgba(212,175,55,0.12)';
        const gray  = '#37474F';

        // ── Sales Trend Line Chart ────────────────────────────────────
        const trendData = @json($salesTrend->values());

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [{
                    label: 'Revenue ($)',
                    data: trendData.map(d => d.revenue),
                    borderColor: gold,
                    backgroundColor: goldT,
                    borderWidth: 2,
                    pointBackgroundColor: gold,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' $' + Number(ctx.parsed.y).toFixed(2),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,.05)' },
                        ticks: { color: gray, font: { size: 11 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,.05)' },
                        ticks: {
                            color: gray,
                            font: { size: 11 },
                            callback: v => '$' + v,
                        },
                    },
                },
            },
        });

        // ── Listing Status Donut ──────────────────────────────────────
        const donutEl = document.getElementById('listingDonut');
        if (donutEl) {
            new Chart(donutEl, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Pending', 'Expired', 'Rejected'],
                    datasets: [{
                        data: [
                            {{ $listingStats['active'] }},
                            {{ $listingStats['pending'] }},
                            {{ $listingStats['expired'] }},
                            {{ $listingStats['rejected'] }},
                        ],
                        backgroundColor: ['#22c55e', '#eab308', '#9ca3af', '#ef4444'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 11 }, color: gray, boxWidth: 10, padding: 10 },
                        },
                    },
                },
            });
        }
    }());
    </script>
    @endpush
</x-app-layout>
