<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'خطا') | نویسینو</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">


    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="{{ \App\Helpers\Site::isGhesti() ? 'theme-ghesti' : '' }}">
<div x-data="{ sidebarOpen: false, activeDropdown: null }">

    <livewire:components.header/>

    @php $errorCode = trim(View::getSection('code') ?? 'default'); @endphp

    <main class="lg:mr-20 mt-20 min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm text-center">

            {{-- SVG کوچک و اختصاصی هر کد --}}
            <div class="flex justify-center mb-6">
                @switch($errorCode)

                    {{-- ۴۰۴ : ذره‌بین (پیدا نشد) --}}
                    @case('404')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g404" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6"/>
                                    <stop offset="100%" stop-color="#8b5cf6"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#eff6ff"/>
                            <circle cx="88" cy="88" r="42" fill="#fff" stroke="url(#g404)" stroke-width="9"/>
                            <line x1="120" y1="120" x2="152" y2="152" stroke="url(#g404)" stroke-width="13" stroke-linecap="round"/>
                            <circle cx="88" cy="88" r="6" fill="url(#g404)"/>
                        </svg>
                        @break

                        {{-- ۵۰۰ : سرور + هشدار --}}
                    @case('500')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g500" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#ef4444"/>
                                    <stop offset="100%" stop-color="#f97316"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#fef2f2"/>
                            <rect x="52" y="72" width="96" height="30" rx="6" fill="#fff" stroke="url(#g500)" stroke-width="5"/>
                            <rect x="52" y="112" width="96" height="30" rx="6" fill="#fff" stroke="url(#g500)" stroke-width="5"/>
                            <circle cx="66" cy="87" r="4" fill="#10b981"/>
                            <circle cx="66" cy="127" r="4" fill="#ef4444"/>
                            <line x1="85" y1="87" x2="135" y2="87" stroke="#fecaca" stroke-width="4" stroke-linecap="round"/>
                            <line x1="85" y1="127" x2="135" y2="127" stroke="#fecaca" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                        @break

                        {{-- ۴۰۳ : قفل (دسترسی ممنوع) --}}
                    @case('403')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g403" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#f59e0b"/>
                                    <stop offset="100%" stop-color="#ef4444"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#fffbeb"/>
                            <path d="M75 95 v-18 a25 25 0 0 1 50 0 v18" fill="none" stroke="url(#g403)" stroke-width="10" stroke-linecap="round"/>
                            <rect x="65" y="95" width="70" height="55" rx="10" fill="url(#g403)"/>
                            <circle cx="100" cy="118" r="7" fill="#fff"/>
                            <rect x="97" y="122" width="6" height="15" rx="2" fill="#fff"/>
                        </svg>
                        @break

                        {{-- ۴۰۱ : کاربر + قفل (احراز هویت) --}}
                    @case('401')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <circle cx="100" cy="100" r="92" fill="#fffbeb"/>
                            <circle cx="92" cy="82" r="24" fill="#f59e0b"/>
                            <path d="M55 152 Q55 116 92 116 Q129 116 129 152 Z" fill="#f59e0b"/>
                            <g transform="translate(148 148)">
                                <circle r="24" fill="#ef4444"/>
                                <path d="M-7 -2 v-4 a7 7 0 0 1 14 0 v4" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                <rect x="-11" y="-2" width="22" height="16" rx="3" fill="#fff"/>
                                <circle cx="0" cy="6" r="2" fill="#ef4444"/>
                            </g>
                        </svg>
                        @break

                        {{-- ۴۱۹ : ساعت شنی (نشست منقضی) --}}
                    @case('419')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g419" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#8b5cf6"/>
                                    <stop offset="100%" stop-color="#3b82f6"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#f5f3ff"/>
                            <rect x="72" y="52" width="56" height="10" rx="4" fill="url(#g419)"/>
                            <rect x="72" y="138" width="56" height="10" rx="4" fill="url(#g419)"/>
                            <path d="M80 62 L120 62 L100 100 L120 138 L80 138 L100 100 Z" fill="#fff" stroke="url(#g419)" stroke-width="5" stroke-linejoin="round"/>
                            <path d="M87 69 L113 69 L100 92 Z" fill="url(#g419)" opacity="0.7"/>
                            <path d="M87 131 L113 131 L100 108 Z" fill="url(#g419)" opacity="0.9"/>
                        </svg>
                        @break

                        {{-- ۴۲۹ : ساعت (درخواست زیاد) --}}
                    @case('429')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g429" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#f59e0b"/>
                                    <stop offset="100%" stop-color="#ef4444"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#fffbeb"/>
                            <circle cx="100" cy="100" r="52" fill="#fff" stroke="url(#g429)" stroke-width="7"/>
                            <line x1="100" y1="60" x2="100" y2="72" stroke="url(#g429)" stroke-width="5" stroke-linecap="round"/>
                            <line x1="100" y1="128" x2="100" y2="140" stroke="url(#g429)" stroke-width="5" stroke-linecap="round"/>
                            <line x1="60" y1="100" x2="72" y2="100" stroke="url(#g429)" stroke-width="5" stroke-linecap="round"/>
                            <line x1="128" y1="100" x2="140" y2="100" stroke="url(#g429)" stroke-width="5" stroke-linecap="round"/>
                            <line x1="100" y1="100" x2="100" y2="68" stroke="url(#g429)" stroke-width="6" stroke-linecap="round"/>
                            <line x1="100" y1="100" x2="125" y2="100" stroke="url(#g429)" stroke-width="6" stroke-linecap="round"/>
                            <circle cx="100" cy="100" r="7" fill="url(#g429)"/>
                        </svg>
                        @break

                        {{-- ۵۰۳ : آچار (در دسترس نیست) --}}
                    @case('503')
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="g503" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6"/>
                                    <stop offset="100%" stop-color="#6366f1"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#eff6ff"/>
                            <g transform="translate(100 100) rotate(-40)">
                                <rect x="-9" y="-50" width="18" height="100" rx="5" fill="url(#g503)"/>
                                <circle cx="0" cy="-50" r="22" fill="url(#g503)"/>
                                <circle cx="0" cy="-50" r="11" fill="#eff6ff"/>
                            </g>
                        </svg>
                        @break

                        {{-- پیش‌فرض : مثلث هشدار --}}
                    @default
                        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 h-auto">
                            <defs>
                                <linearGradient id="gDef" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#f59e0b"/>
                                    <stop offset="100%" stop-color="#ef4444"/>
                                </linearGradient>
                            </defs>
                            <circle cx="100" cy="100" r="92" fill="#fffbeb"/>
                            <path d="M100 58 L155 148 L45 148 Z" fill="url(#gDef)" stroke-linejoin="round"/>
                            <rect x="96" y="88" width="8" height="35" rx="4" fill="#fff"/>
                            <circle cx="100" cy="134" r="5" fill="#fff"/>
                        </svg>
                @endswitch
            </div>

            {{-- کد خطا به صورت کوچک --}}
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest bg-gray-100 text-gray-500">
                خطای @yield('code')
            </span>

            <h1 class="mt-4 text-lg sm:text-xl font-bold text-gray-800">
                @yield('message')
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                نگران نباشید، می‌توانید از همین‌جا ادامه دهید.
            </p>

            {{-- فقط دکمه‌ی صفحه اصلی --}}
            <div class="mt-8">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition shadow-lg shadow-blue-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    بازگشت به صفحه اصلی
                </a>
            </div>
        </div>
    </main>

    <livewire:components.footer/>
    <livewire:components.bottom-bar/>

</div>

@livewireScripts
</body>
</html>