<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->guard('admin')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Check if user's current_role matches any of the required roles
        if (!in_array($user->current_role, $roles)) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $next($request);
    }
}