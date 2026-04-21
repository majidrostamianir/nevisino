<?php

namespace App\Helpers;

class UserAgentParser
{
    public static function parse($userAgent)
    {
        if (empty($userAgent)) {
            return [
                'device' => 'ناشناس',
                'browser' => 'ناشناس',
                'os' => 'ناشناس',
                'is_mobile' => false,
                'is_bot' => false,
                'icon' => '🖥️',
                'full' => $userAgent,
                'simple' => '🖥️ ناشناس'
            ];
        }

        // تشخیص بات با جزئیات کامل
        if (preg_match('/bot|crawler|spider|scraper|curl|wget|python|java/i', $userAgent)) {
            return self::parseBot($userAgent);
        }

        // تشخیص دستگاه
        $is_mobile = false;
        $device = 'کامپیوتر';
        $icon = '🖥️';

        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $device = 'Apple';
            $icon = '📱';
            $is_mobile = true;
        } elseif (preg_match('/Android/i', $userAgent)) {
            $device = 'Android';
            $icon = '📱';
            $is_mobile = true;
        } elseif (preg_match('/Mobile/i', $userAgent)) {
            $device = 'موبایل';
            $icon = '📱';
            $is_mobile = true;
        }

        // تشخیص مرورگر با اعتبارسنجی نسخه
        $browser = self::parseBrowser($userAgent);

        // تشخیص سیستم عامل
        $os = self::parseOS($userAgent);

        $simple = trim("$icon $browser - $os");

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
            'is_mobile' => $is_mobile,
            'is_bot' => false,
            'icon' => $icon,
            'full' => $userAgent,
            'simple' => $simple
        ];
    }

    private static function parseBot($userAgent)
    {
        $bot_name = 'بات ناشناس';
        $icon = '🤖';

        // ربات‌های موتورهای جستجو
        if (preg_match('/Googlebot/i', $userAgent)) {
            $bot_name = 'Googlebot';
            if (preg_match('/Googlebot-Image/i', $userAgent)) $bot_name = 'Googlebot (تصاویر)';
            if (preg_match('/Googlebot-News/i', $userAgent)) $bot_name = 'Googlebot (اخبار)';
            if (preg_match('/Googlebot-Video/i', $userAgent)) $bot_name = 'Googlebot (ویدئو)';
        }
        elseif (preg_match('/Bingbot/i', $userAgent)) $bot_name = 'Bingbot (بینگ)';
        elseif (preg_match('/Yahoo! Slurp/i', $userAgent)) $bot_name = 'Yahoo Slurp';
        elseif (preg_match('/DuckDuckBot/i', $userAgent)) $bot_name = 'DuckDuckGo Bot';
        elseif (preg_match('/baiduspider/i', $userAgent)) $bot_name = 'Baidu Spider (چین)';
        elseif (preg_match('/YandexBot/i', $userAgent)) $bot_name = 'Yandex Bot (روسیه)';
        elseif (preg_match('/Sogou/i', $userAgent)) $bot_name = 'Sogou (چین)';
        elseif (preg_match('/Exabot/i', $userAgent)) $bot_name = 'Exabot';

        // ربات‌های شبکه‌های اجتماعی
        elseif (preg_match('/facebookexternalhit/i', $userAgent)) $bot_name = 'Facebook Crawler';
        elseif (preg_match('/Twitterbot/i', $userAgent)) $bot_name = 'Twitter Bot';
        elseif (preg_match('/WhatsApp/i', $userAgent)) $bot_name = 'WhatsApp Bot';
        elseif (preg_match('/TelegramBot/i', $userAgent)) $bot_name = 'Telegram Bot';
        elseif (preg_match('/Discordbot/i', $userAgent)) $bot_name = 'Discord Bot';
        elseif (preg_match('/Slackbot/i', $userAgent)) $bot_name = 'Slack Bot';
        elseif (preg_match('/Applebot/i', $userAgent)) $bot_name = 'Apple Bot';
        elseif (preg_match('/LinkedInBot/i', $userAgent)) $bot_name = 'LinkedIn Bot';
        elseif (preg_match('/Pinterest/i', $userAgent)) $bot_name = 'Pinterest Bot';
        elseif (preg_match('/Instagram/i', $userAgent)) $bot_name = 'Instagram Bot';

        // ربات‌های سئو و آنالیز
        elseif (preg_match('/AhrefsBot/i', $userAgent)) $bot_name = 'Ahrefs (سئو)';
        elseif (preg_match('/SemrushBot/i', $userAgent)) $bot_name = 'Semrush (سئو)';
        elseif (preg_match('/MJ12bot/i', $userAgent)) $bot_name = 'Majestic (سئو)';
        elseif (preg_match('/DotBot/i', $userAgent)) $bot_name = 'DotBot (سئو)';
        elseif (preg_match('/rogerbot/i', $userAgent)) $bot_name = 'Rogerbot (سئو)';

        // ابزارهای درخواست دستی
        elseif (preg_match('/cURL/i', $userAgent)) $bot_name = 'cURL (درخواست دستی)';
        elseif (preg_match('/Wget/i', $userAgent)) $bot_name = 'Wget (دانلودر)';
        elseif (preg_match('/python/i', $userAgent)) $bot_name = 'Python Script';
        elseif (preg_match('/Java/i', $userAgent)) $bot_name = 'Java Client';
        elseif (preg_match('/Go-http-client/i', $userAgent)) $bot_name = 'Go Client';
        elseif (preg_match('/Ruby/i', $userAgent)) $bot_name = 'Ruby Script';
        elseif (preg_match('/PHP/i', $userAgent)) $bot_name = 'PHP Script';
        elseif (preg_match('/Postman/i', $userAgent)) $bot_name = 'Postman (API)';
        elseif (preg_match('/Insomnia/i', $userAgent)) $bot_name = 'Insomnia (API)';

        // ربات‌های دیگر
        elseif (preg_match('/Bytespider/i', $userAgent)) $bot_name = 'ByteDance (تیک‌تاک)';
        elseif (preg_match('/petalbot/i', $userAgent)) $bot_name = 'Petal Bot (هواوی)';
        elseif (preg_match('/SeznamBot/i', $userAgent)) $bot_name = 'Seznam (چک)';

        // استخراج نسخه ربات اگر داشت
        if (preg_match('/(\d+\.\d+)/i', $userAgent, $version_match)) {
            $bot_name .= " v{$version_match[1]}";
        }

        return [
            'device' => $bot_name,
            'browser' => $bot_name,
            'os' => 'بات',
            'is_mobile' => false,
            'is_bot' => true,
            'icon' => $icon,
            'full' => $userAgent,
            'simple' => "$icon $bot_name"
        ];
    }

    private static function parseBrowser($userAgent)
    {
        $browser = 'نامشخص';

        // تشخیص کروم با اعتبارسنجی نسخه
        if (preg_match('/Chrome\/(\d+)/i', $userAgent, $match)) {
            $version = (int)$match[1];
            $current_max = 130; // حداکثر نسخه منطقی برای سال جاری

            if ($version > $current_max) {
                $browser = "Chrome {$version} ⚠️(نامعتبر)";
            } elseif ($version < 100) {
                $browser = "Chrome {$version} 🪦(قدیمی)";
            } else {
                $browser = "Chrome {$version}";
            }
        }
        // تشخیص سافاری (بدون کروم)
        elseif (preg_match('/Safari\/(\d+)/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = "Safari";
            if (preg_match('/Version\/(\d+)/i', $userAgent, $v)) {
                $browser .= " {$v[1]}";
            }
        }
        // تشخیص فایرفاکس
        elseif (preg_match('/Firefox\/(\d+)/i', $userAgent, $match)) {
            $version = (int)$match[1];
            if ($version < 100) {
                $browser = "Firefox {$version} 🪦(قدیمی)";
            } else {
                $browser = "Firefox {$version}";
            }
        }
        // تشخیص سامسونگ اینترنت
        elseif (preg_match('/SamsungBrowser\/(\d+)/i', $userAgent, $match)) {
            $browser = "Samsung Browser {$match[1]}";
        }
        // تشخیص اج
        elseif (preg_match('/Edg\/(\d+)/i', $userAgent, $match)) {
            $version = (int)$match[1];
            if ($version > $current_max ?? 130) {
                $browser = "Edge {$version} ⚠️(نامعتبر)";
            } else {
                $browser = "Edge {$version}";
            }
        }
        // تشخیص اپرا
        elseif (preg_match('/OPR\/(\d+)/i', $userAgent, $match)) {
            $browser = "Opera {$match[1]}";
        }
        // تشخیص Brave
        elseif (preg_match('/Brave\/(\d+)/i', $userAgent, $match)) {
            $browser = "Brave {$match[1]}";
        }
        // تشخیص Vivaldi
        elseif (preg_match('/Vivaldi\/(\d+)/i', $userAgent, $match)) {
            $browser = "Vivaldi {$match[1]}";
        }

        return $browser;
    }

    private static function parseOS($userAgent)
    {
        $os = 'نامشخص';

        // iOS
        if (preg_match('/iPhone OS (\d+)[_\.](\d+)/i', $userAgent, $match)) {
            $os = isset($match[2]) ? "iOS {$match[1]}.{$match[2]}" : "iOS {$match[1]}";
        }
        // iOS جدیدتر (مثل 15_6_1)
        elseif (preg_match('/iPhone OS (\d+)[_\.](\d+)[_\.](\d+)/i', $userAgent, $match)) {
            $os = "iOS {$match[1]}.{$match[2]}.{$match[3]}";
        }
        // اندروید
        elseif (preg_match('/Android (\d+)(?:\.(\d+))?(?:\.(\d+))?/i', $userAgent, $match)) {
            $os = "Android {$match[1]}";
            if (isset($match[2])) $os .= ".{$match[2]}";
            if (isset($match[3])) $os .= ".{$match[3]}";
        }
        // ویندوز
        elseif (preg_match('/Windows NT (\d+\.\d+)/i', $userAgent, $match)) {
            $versions = [
                '10.0' => '10/11',
                '6.3' => '8.1',
                '6.2' => '8',
                '6.1' => '7',
                '6.0' => 'Vista',
                '5.2' => 'XP x64',
                '5.1' => 'XP',
                '5.0' => '2000'
            ];
            $os = "Windows " . ($versions[$match[1]] ?? $match[1]);
        }
        // مک
        elseif (preg_match('/Mac OS X (\d+)[_\.](\d+)(?:[_\.](\d+))?/i', $userAgent, $match)) {
            $os = "Mac OS {$match[1]}.{$match[2]}";
            if (isset($match[3])) $os .= ".{$match[3]}";
        }
        // لینوکس
        elseif (preg_match('/Linux/i', $userAgent)) {
            // تشخیص توزیع‌های خاص
            if (preg_match('/Ubuntu/i', $userAgent)) {
                $os = "Ubuntu Linux";
            } elseif (preg_match('/Debian/i', $userAgent)) {
                $os = "Debian Linux";
            } elseif (preg_match('/Fedora/i', $userAgent)) {
                $os = "Fedora Linux";
            } elseif (preg_match('/CentOS/i', $userAgent)) {
                $os = "CentOS Linux";
            } else {
                $os = "Linux";
            }
        }
        // کروم اواس
        elseif (preg_match('/CrOS/i', $userAgent)) {
            $os = "Chrome OS";
        }

        return $os;
    }
}
