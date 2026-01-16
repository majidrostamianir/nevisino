<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class visitTracker
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $ip = $request->ip();
            if (Auth::guest() || (Auth::check() && Auth::user()->id != 1)) {
                Visit::query()->create([
                    'ip' => $ip ?? null,
                    'user_id' => auth()->id() ?? null,
                    'url' => urldecode($request->path() ?? ''),
                    'referrer' => $request->headers->get('referer') ? urldecode($request->headers->get('referer')) : null,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Visit tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
