<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginHistory::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%'.$request->ip_address.'%');
        }

        if ($request->filled('is_suspicious')) {
            $query->where('is_suspicious', $request->is_suspicious === 'yes');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('logged_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('logged_in_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $loginHistories = $query->latest('logged_in_at')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total' => LoginHistory::count(),
            'today' => LoginHistory::whereDate('logged_in_at', today())->count(),
            'suspicious' => LoginHistory::where('is_suspicious', true)->count(),
            'unique_ips' => LoginHistory::distinct('ip_address')->count('ip_address'),
        ];

        $suspiciousIps = LoginHistory::where('is_suspicious', true)
            ->select('ip_address')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.login-histories.index', compact('loginHistories', 'stats', 'suspiciousIps'));
    }

    public function show(LoginHistory $loginHistory)
    {
        $loginHistory->load('user');

        $sameIpLogins = LoginHistory::where('ip_address', $loginHistory->ip_address)
            ->where('id', '!=', $loginHistory->id)
            ->with('user')
            ->latest('logged_in_at')
            ->limit(10)
            ->get();

        $userRecentLogins = LoginHistory::where('user_id', $loginHistory->user_id)
            ->where('id', '!=', $loginHistory->id)
            ->latest('logged_in_at')
            ->limit(10)
            ->get();

        return view('admin.login-histories.show', compact('loginHistory', 'sameIpLogins', 'userRecentLogins'));
    }

    public function userHistory(User $user)
    {
        $loginHistories = LoginHistory::where('user_id', $user->id)
            ->latest('logged_in_at')
            ->paginate(50);

        $stats = [
            'total' => LoginHistory::where('user_id', $user->id)->count(),
            'suspicious' => LoginHistory::where('user_id', $user->id)->where('is_suspicious', true)->count(),
            'unique_ips' => LoginHistory::where('user_id', $user->id)->distinct('ip_address')->count('ip_address'),
            'last_login' => LoginHistory::where('user_id', $user->id)->latest('logged_in_at')->first()?->logged_in_at,
        ];

        return view('admin.login-histories.user', compact('user', 'loginHistories', 'stats'));
    }

    public function blockIp(Request $request)
    {
        $request->validate(['ip_address' => 'required|ip']);

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['ip_address' => $request->ip_address])
            ->log('IP address blocked');

        return redirect()
            ->back()
            ->with('success', "IP address {$request->ip_address} has been flagged. Implement firewall rules as needed.");
    }
}
