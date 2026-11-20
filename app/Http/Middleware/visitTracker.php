<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use App\Helpers\Site;
use App\Helpers\BotDetector;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class visitTracker
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userAgent = $request->userAgent();
            $ip = $request->ip();

            // تشخیص ربات
            $bot = BotDetector::detect($userAgent);

            $blockedIpRanges = [
                '81.12.31.*',
            ];

            $blockedUserAgents = [
                'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36',
                'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)',
            ];

            foreach ($blockedIpRanges as $range) {
                if ($ip && fnmatch($range, $ip)) {
                    return $next($request);
                }
            }

            foreach ($blockedUserAgents as $blockedUA) {
                if ($userAgent && stripos($userAgent, $blockedUA) !== false) {
                    return $next($request);
                }
            }

            if (Auth::guest() || (Auth::check() && Auth::id() !== 1)) {
                Visit::query()->create([
                    'ip' => $ip,
                    'is_ghesti' => Site::isGhesti(),
                    'user_id' => Auth::id(),
                    'url' => rawurldecode($request->path() ?? ''),
                    'referrer' => $request->headers->get('referer')
                        ? rawurldecode($request->headers->get('referer'))
                        : null,
                    'is_bot' => $bot['is_bot'],
                    'bot_name' => $bot['name'],
                    'bot_category' => $bot['category'],
                    'user_agent' => $userAgent,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Visit tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}