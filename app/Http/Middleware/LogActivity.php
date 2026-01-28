<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->guard('admin')->check() && !in_array($request->method(), ['GET', 'HEAD'])) {
            activity()
                ->causedBy(auth()->guard('admin')->user())
                ->withProperties([
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ])
                ->log('Admin action performed');
        }

        return $response;
    }
}