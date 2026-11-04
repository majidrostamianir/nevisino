<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureTorobClid
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('torob_clid')) {
            $clid = substr((string) $request->query('torob_clid'), 0, 100);

            cookie()->queue(
                cookie(
                    name: 'torob_clid',
                    value: $clid,
                    minutes: 60 * 24 * 7,    // ۷ روز
                    path: '/',
                    domain: null,
                    secure: $request->isSecure(),
                    httpOnly: true,          // ← JS لازم ندارد، امن‌تر
                    raw: false,
                    sameSite: 'lax'          // ← برای عبور از ریدایرکت بین‌سایتی
                )
            );
        }

        return $next($request);
    }
}