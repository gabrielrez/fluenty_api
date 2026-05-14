<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isAdmin()) {
            return response()->json([
                'error' => 'access_denied',
                'message' => 'You are not authorized to access this area',
            ], 403);
        }

        return $next($request);
    }
}
