<?php

namespace App\Helpers;

class BotDetector
{
    public static function detect(?string $userAgent): array
    {
        if (!$userAgent || trim($userAgent) === '') {
            return [
                'is_bot' => true,
                'name' => 'empty_user_agent',
                'category' => 'unknown',
            ];
        }

        $knownBots = [
            'googlebot'           => '/googlebot/i',
            'bingbot'             => '/bingbot/i',
            'yandexbot'           => '/yandex(bot|images|mobilebot)/i',
            'duckduckbot'         => '/duckduckbot/i',
            'baiduspider'         => '/baiduspider/i',
            'applebot'            => '/applebot/i',
            'facebookexternalhit' => '/facebookexternalhit|facebot/i',
            'twitterbot'          => '/twitterbot/i',
            'linkedinbot'         => '/linkedinbot/i',
            'telegrambot'         => '/telegrambot/i',
            'whatsapp'            => '/whatsapp/i',
            'slackbot'            => '/slackbot/i',
            'discordbot'          => '/discordbot/i',
            'semrushbot'          => '/semrushbot/i',
            'ahrefsbot'           => '/ahrefsbot/i',
            'mj12bot'             => '/mj12bot/i',
            'dotbot'              => '/dotbot/i',
            'petalbot'            => '/petalbot/i',
            'bingpreview'         => '/bingpreview/i',
            'google-read-aloud'   => '/google-read-aloud/i',
            'headlesschrome'      => '/headlesschrome/i',
            'phantomjs'           => '/phantomjs/i',
            'scrapy'              => '/scrapy/i',
            'curl'                => '/^curl\//i',
            'wget'                => '/^wget\//i',
            'python-requests'     => '/python-requests/i',
            'python-urllib'       => '/python-urllib/i',
            'go-http-client'      => '/go-http-client/i',
            'okhttp'              => '/okhttp/i',
            'java'                => '/^java\//i',
        ];

        foreach ($knownBots as $name => $regex) {
            if (preg_match($regex, $userAgent)) {
                return [
                    'is_bot' => true,
                    'name' => $name,
                    'category' => 'crawler',
                ];
            }
        }

        if (preg_match(
            '/bot\b|crawler|spider|slurp|mediapartners|preview|fetcher|monitor|uptime|pingdom|headless|phantom|selenium|puppeteer|playwright/i',
            $userAgent
        )) {
            return [
                'is_bot' => true,
                'name' => 'generic_bot',
                'category' => 'unknown',
            ];
        }

        return [
            'is_bot' => false,
            'name' => null,
            'category' => null,
        ];
    }
}