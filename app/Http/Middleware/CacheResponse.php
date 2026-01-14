<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        if ($request->user()) {
            return $next($request);
        }

        $key = $this->getCacheKey($request);

        if (Cache::has($key)) {
            $cached = Cache::get($key);
            
            return response($cached['content'], $cached['status'])
                ->withHeaders(array_merge($cached['headers'], [
                    'X-Cache' => 'HIT',
                    'X-Cache-Key' => $key,
                ]));
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ], $ttl);

            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-Key', $key);
        }

        return $response;
    }

    protected function getCacheKey(Request $request): string
    {
        $queryString = $request->getQueryString();
        $url = $request->url();
        
        return 'response:' . md5($url . ($queryString ? '?' . $queryString : ''));
    }
}