<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->subscribed('default')) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => 'An active subscription is required to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
