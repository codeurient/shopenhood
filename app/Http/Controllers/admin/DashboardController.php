<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // ── User stats ───────────────────────────────────────────────
        $userStats = [
            'total' => User::count(),
            'new_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'business' => User::where('current_role', 'business_user')->count(),
            'banned' => User::where('status', 'banned')->count(),
        ];

        // ── Listing stats ────────────────────────────────────────────
        $listingCounts = Listing::withTrashed()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $listingStats = [
            'total' => Listing::count(),
            'active' => $listingCounts->get('active', 0),
            'pending' => Listing::where('status', 'pending')->count(),
            'deleted' => Listing::onlyTrashed()->count(),
            'expired' => $listingCounts->get('expired', 0),
            'rejected' => $listingCounts->get('rejected', 0),
        ];

        // ── Order / revenue stats ────────────────────────────────────
        $orderStats = [
            'total' => Order::count(),
            'pending' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'completed' => Order::completed()->count(),
            'cancelled' => Order::cancelled()->count(),
            'revenue' => Order::completed()->sum('subtotal'),
            'revenue_month' => Order::completed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('subtotal'),
        ];

        // ── Monthly trend — last 6 months (orders + revenue) ────────
        $trendRows = Order::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(subtotal) as revenue, COUNT(*) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $row = $trendRows->get($key);
            $monthlyTrend->put($key, [
                'label' => now()->subMonths($i)->format('M y'),
                'revenue' => $row ? (float) $row->revenue : 0,
                'orders' => $row ? (int) $row->orders : 0,
            ]);
        }

        // ── User registrations — last 6 months ──────────────────────
        $regRows = User::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $registrationTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $row = $regRows->get($key);
            $registrationTrend->put($key, [
                'label' => now()->subMonths($i)->format('M y'),
                'count' => $row ? (int) $row->count : 0,
            ]);
        }

        // ── Pending listings (awaiting approval) ────────────────────
        $pendingListings = Listing::where('status', 'pending')
            ->with(['user:id,name,email', 'category:id,name', 'primaryImage'])
            ->latest()
            ->take(8)
            ->get();

        // ── Recent orders ────────────────────────────────────────────
        $recentOrders = Order::with(['listing:id,title,slug', 'buyer:id,name'])
            ->latest()
            ->take(8)
            ->get();

        // ── Recent users ─────────────────────────────────────────────
        $recentUsers = User::latest()->take(6)->get();

        // ── Top sellers (by completed order revenue) ─────────────────
        $topSellers = Order::completed()
            ->selectRaw('seller_id, COUNT(*) as sales_count, SUM(subtotal) as total_revenue')
            ->groupBy('seller_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->with('seller:id,name,email')
            ->get();

        return view('admin.dashboard.index', compact(
            'userStats',
            'listingStats',
            'orderStats',
            'monthlyTrend',
            'registrationTrend',
            'pendingListings',
            'recentOrders',
            'recentUsers',
            'topSellers',
        ));
    }
}
