<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class visitTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $url = $request->fullUrl() ?? '';

            Visit::query()->create([
                'ip' => $request->ip() ?? null,
                'user_id' => auth()->id() ?? null,
                'url' => urldecode($request->path() ?? ''), // فقط مسیر بدون دامنه
            ]);
        } catch (\Throwable $e) {
            \Log::error('Visit tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
