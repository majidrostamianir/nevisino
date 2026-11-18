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
            $clid    = substr((string) $request->query('torob_clid'), 0, 100);
            $host    = preg_replace('/^www\./', '', $request->getHost());
            $domain  = config('session.domain'); // .nevisino.ir
            $secure  = $request->isSecure();
            $minutes = 60 * 24 * 7;

            cookie()->queue(cookie(
                name:     'torob_clid',
                value:    $clid,
                minutes:  $minutes,
                path:     '/',
                domain:   $domain,
                secure:   $secure,
                httpOnly: true,
                raw:      false,
                sameSite: 'lax',
            ));

            cookie()->queue(cookie(
                name:     'torob_shop_domain',
                value:    $host,
                minutes:  $minutes,
                path:     '/',
                domain:   $domain,
                secure:   $secure,
                httpOnly: true,
                raw:      false,
                sameSite: 'lax',
            ));
        }

        return $next($request);
    }
}