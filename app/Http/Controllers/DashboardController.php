<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Order;
use App\Models\Purchase;

class DashboardController extends Controller
{
    public function __invoke(): \Illuminate\View\View
    {
        $user = auth()->user();

        // ── Listing stats ────────────────────────────────────────────
        $listingCounts = $user->listings()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $listingStats = [
            'total' => $listingCounts->sum(),
            'active' => $listingCounts->get('active', 0),
            'pending' => $listingCounts->get('pending', 0),
            'expired' => $listingCounts->get('expired', 0),
            'rejected' => $listingCounts->get('rejected', 0),
            'draft' => $listingCounts->get('draft', 0),
            'limit' => $user->getListingLimit(),
        ];

        // ── Sales stats (as seller) ──────────────────────────────────
        $salesStats = [
            'total' => Order::forSeller($user->id)->count(),
            'pending' => Order::forSeller($user->id)->whereIn('status', ['pending', 'processing'])->count(),
            'completed' => Order::forSeller($user->id)->completed()->count(),
            'cancelled' => Order::forSeller($user->id)->cancelled()->count(),
            'revenue' => Order::forSeller($user->id)->completed()->sum('subtotal'),
            'revenue_month' => Order::forSeller($user->id)->completed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('subtotal'),
        ];

        // ── Monthly trend — last 6 months ────────────────────────────
        $monthRows = Order::forSeller($user->id)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(subtotal) as revenue, COUNT(*) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $row = $monthRows->get($key);
            $monthlyTrend->put($key, [
                'label' => now()->subMonths($i)->format('M y'),
                'revenue' => $row ? (float) $row->revenue : 0,
                'orders' => $row ? (int) $row->orders : 0,
            ]);
        }

        // ── Weekly trend — last 7 days ───────────────────────────────
        $weekRows = Order::forSeller($user->id)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as period, SUM(subtotal) as revenue, COUNT(*) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $weeklyTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $key = now()->subDays($i)->format('Y-m-d');
            $row = $weekRows->get($key);
            $weeklyTrend->put($key, [
                'label' => now()->subDays($i)->format('D d'),
                'revenue' => $row ? (float) $row->revenue : 0,
                'orders' => $row ? (int) $row->orders : 0,
            ]);
        }

        // ── Recent sales (as seller) ─────────────────────────────────
        $recentSales = Order::forSeller($user->id)
            ->with(['listing:id,title,slug', 'buyer:id,name'])
            ->latest()
            ->take(8)
            ->get();

        // ── Recent purchases (as buyer) ──────────────────────────────
        $recentPurchases = Purchase::where('buyer_id', $user->id)
            ->with(['orders.listing:id,title,slug'])
            ->latest()
            ->take(5)
            ->get();

        // ── User's recent listings ───────────────────────────────────
        $recentListings = $user->listings()
            ->with(['primaryImage', 'listingType:id,name'])
            ->latest()
            ->take(6)
            ->get();

        // ── Top performing listings (by completed order count) ───────
        $topListings = Order::forSeller($user->id)
            ->completed()
            ->selectRaw('listing_id, COUNT(*) as sales_count, SUM(subtotal) as total_revenue')
            ->groupBy('listing_id')
            ->orderByDesc('sales_count')
            ->take(5)
            ->with('listing:id,title,slug,base_price,currency')
            ->get();

        // ── Notifications ────────────────────────────────────────────
        $recentNotifications = $user->notifications()->latest()->take(6)->get();
        $unreadCount = $user->unreadNotifications()->count();

        // ── Purchase stats (as buyer) ────────────────────────────────
        $purchaseStats = [
            'total' => Purchase::where('buyer_id', $user->id)->count(),
            'pending' => Purchase::where('buyer_id', $user->id)->where('status', 'pending')->count(),
            'spent' => Purchase::where('buyer_id', $user->id)->where('status', 'completed')->sum('total_amount'),
        ];

        return view('dashboard', compact(
            'listingStats',
            'salesStats',
            'monthlyTrend',
            'weeklyTrend',
            'recentSales',
            'recentPurchases',
            'recentListings',
            'topListings',
            'recentNotifications',
            'unreadCount',
            'purchaseStats',
        ));
    }
}
