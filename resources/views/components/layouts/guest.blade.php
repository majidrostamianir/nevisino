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
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 ">
    <div class="w-full relative sm:w-4/12  p-4 h-[85vh] bg-pars-100  shadow-md overflow-hidden sm:rounded-lg">
        <a class="absolute top-4 left-4 bg-pars-700 text-white text-sm py-1 px-3 rounded-2xl" href="/" wire:navigate>
            بازگشت
        </a>
        <div class="w-full">
            <a href="/" wire:navigate>
                <img class="w-40 justify-self-center" src="{{ asset('images/logo2.png') }}">
            </a>
        </div>
        {{ $slot }}
    </div>
</div>

@livewireScripts



</body>
</html>
