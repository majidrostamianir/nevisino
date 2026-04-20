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
                'full' => $userAgent
            ];
        }

        // تشخیص بات
        if (preg_match('/bot|crawler|spider|scraper|curl|wget|python|java/i', $userAgent)) {
            return [
                'device' => 'بات',
                'browser' => 'بات',
                'os' => 'بات',
                'is_mobile' => false,
                'is_bot' => true,
                'icon' => '🤖',
                'full' => $userAgent
            ];
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

        // تشخیص مرورگر
        $browser = 'نامشخص';
        if (preg_match('/Chrome\/(\d+)/i', $userAgent, $match)) {
            $browser = "Chrome {$match[1]}";
        } elseif (preg_match('/Safari\/(\d+)/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = "Safari";
        } elseif (preg_match('/Firefox\/(\d+)/i', $userAgent, $match)) {
            $browser = "Firefox {$match[1]}";
        } elseif (preg_match('/SamsungBrowser\/(\d+)/i', $userAgent, $match)) {
            $browser = "Samsung Browser {$match[1]}";
        } elseif (preg_match('/Edg\/(\d+)/i', $userAgent, $match)) {
            $browser = "Edge {$match[1]}";
        } elseif (preg_match('/OPR\/(\d+)/i', $userAgent, $match)) {
            $browser = "Opera {$match[1]}";
        }

        // تشخیص سیستم عامل
        $os = 'نامشخص';
        if (preg_match('/iPhone OS (\d+)[_\.](\d+)/i', $userAgent, $match)) {
            // بررسی وجود نسخه دوم برای iOS
            $os = isset($match[2]) ? "iOS {$match[1]}.{$match[2]}" : "iOS {$match[1]}";
        } elseif (preg_match('/Android (\d+)(?:\.(\d+))?/i', $userAgent, $match)) {
            $os = "Android {$match[1]}" . (isset($match[2]) ? ".{$match[2]}" : '');
        } elseif (preg_match('/Windows NT (\d+\.\d+)/i', $userAgent, $match)) {
            $versions = ['10.0' => '10', '6.3' => '8.1', '6.2' => '8', '6.1' => '7'];
            $os = "Windows " . ($versions[$match[1]] ?? $match[1]);
        } elseif (preg_match('/Mac OS X (\d+)[_\.](\d+)/i', $userAgent, $match)) {
            // اصلاح شده: بررسی وجود نسخه دوم برای Mac OS
            $os = isset($match[2]) ? "Mac OS {$match[1]}.{$match[2]}" : "Mac OS {$match[1]}";
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = "Linux";
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
            'is_mobile' => $is_mobile,
            'is_bot' => false,
            'icon' => $icon,
            'full' => $userAgent,
            'simple' => trim("$icon $browser - $os")
        ];
    }
}
