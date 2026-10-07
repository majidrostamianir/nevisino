<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'digipay' => [
        'base_url' => env('DIGIPAY_BASE_URL', 'https://api.mydigipay.com/digipay/api'),
        'client_id' => env('DIGIPAY_CLIENT_ID'),
        'client_secret' => env('DIGIPAY_CLIENT_SECRET'),
        'username' => env('DIGIPAY_USERNAME'),
        'password' => env('DIGIPAY_PASSWORD'),
        'version' => env('DIGIPAY_VERSION', '2022-02-02'),
        'agent' => env('DIGIPAY_AGENT', 'WEB'),
    ],
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_ids'  => array_filter(array_map('trim', explode(',', env('TELEGRAM_CHAT_IDS', '')))),
    ],
];
