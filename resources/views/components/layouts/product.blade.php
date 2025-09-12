<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">


    <title>{{ $title ?? 'نویسینو' }}</title>

    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body>


<livewire:components.header/>

<livewire:components.side-bar/>

<div class="mx-4 sm:mr-20 sm:ml-4 mt-32 sm:mt-20 place-items-center ">
    {{ $slot }}
</div>

<livewire:components.toast />


<livewire:components.footer />

@livewireScripts



@stack('scripts')

</body>
</html>
