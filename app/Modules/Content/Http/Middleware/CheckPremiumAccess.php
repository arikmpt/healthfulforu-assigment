<?php

namespace Modules\Content\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Modules\Content\Models\Content;

class CheckPremiumAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Get content from route (can be slug or model)
        $contentParam = $request->route('content') ?? $request->route('slug');

        // Resolve content
        if (is_string($contentParam)) {
            $content = Content::where('slug', $contentParam)->first();
        } elseif ($contentParam instanceof Content) {
            $content = $contentParam;
        } else {
            return $next($request);
        }

        // If content not found or is free, allow access
        if (!$content || !$content->isPremium()) {
            return $next($request);
        }

        // Check if user has active premium subscription
        if (!$user || !$user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'Premium subscription required to access this content.',
                'code' => 'PREMIUM_REQUIRED',
            ], 403);
        }

        return $next($request);
    }
}
