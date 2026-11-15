<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->active_business_id) {
            return response()->json([
                'success' => false,
                'message' => 'No active business selected.',
            ], 403);
        }

        $business = $user->businesses()
            ->where('businesses.id', $user->active_business_id)
            ->where('businesses.is_active', true)
            ->first();

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to the active business.',
            ], 403);
        }

        $request->attributes->set('activeBusiness', $business);

        return $next($request);
    }
}
