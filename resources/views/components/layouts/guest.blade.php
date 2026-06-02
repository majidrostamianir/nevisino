<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? 'نویسینو' }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="text-gray-900 antialiased">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
    <div class="w-full relative sm:w-4/12 p-4 h-[85vh] bg-white border border-gray-100 shadow-sm overflow-hidden sm:rounded-2xl">
        <a class="absolute top-4 left-4 flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm py-1.5 px-3 rounded-xl transition-all duration-200"
           href="/" wire:navigate>
            بازگشت
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="w-full flex justify-center mt-4">
            <a href="/" wire:navigate>
                <img class="w-40" src="{{ asset('images/logo2.png') }}" alt="نویسینو">
            </a>
        </div>
        {{ $slot }}
    </div>
</div>

@livewireScripts
</body>
</html>