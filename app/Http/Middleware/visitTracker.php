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

            $userAgent = $request->userAgent();
            $ip = $request->ip();

            // تشخیص ربات
            $isBot = false;
            if ($userAgent) {
                $botKeywords = ['bot', 'crawl', 'slurp', 'spider', 'mediapartners'];
                foreach ($botKeywords as $keyword) {
                    if (stripos($userAgent, $keyword) !== false) {
                        $isBot = true;
                        break;
                    }
                }
            }

            // رنج آی‌پی‌هایی که نمیخوایم ذخیره بشن
            $blockedIpRanges = [
                '81.12.31.*', // همه آی‌پی‌های 81.12.31.0 تا 81.12.31.255
            ];

            // یوزر ایجنت‌های بلاک
            $blockedUserAgents = [
                'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36',
                'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)'
            ];

            // چک کردن رنج آی‌پی
            foreach ($blockedIpRanges as $range) {
                if (fnmatch($range, $ip)) {
                    return $next($request); // ذخیره نکن
                }
            }

            // چک کردن یوزر ایجنت
            foreach ($blockedUserAgents as $blockedUA) {
                if (stripos($userAgent, $blockedUA) !== false) {
                    return $next($request); // ذخیره نکن
                }
            }

            // ذخیره بازدید برای کاربران عادی
            if (Auth::guest() || (Auth::check() && Auth::user()->id != 1)) {
                Visit::query()->create([
                    'ip' => $ip ?? null,
                    'user_id' => auth()->id() ?? null,
                    'url' => urldecode($request->path() ?? ''),
                    'referrer' => $request->headers->get('referer') ? urldecode($request->headers->get('referer')) : null,
                    'is_bot' => $isBot,
                    'user_agent' => $userAgent,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Visit tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }

}
