<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpAllowlist
{
    /**
     * Block requests to the admin panel from IPs not in the allowlist.
     * If ADMIN_IP_ALLOWLIST is empty, all IPs are permitted (auth handles access).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowlist = config('admin.ip_allowlist', []);

        if (empty($allowlist)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        if (! in_array($clientIp, $allowlist, true)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
