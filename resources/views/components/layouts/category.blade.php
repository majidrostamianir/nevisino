<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }} | نویسینو</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">
    @stack('seo-meta-tags')

    @vite(['resources/css/app.css','resources/js/app.js'])

    @livewireStyles

</head>
<body>


<livewire:components.header/>
<livewire:components.toast />

<div class="mx-4 sm:mr-20 sm:ml-4 mt-32 sm:mt-20  min-h-[65vh]">
    {{ $slot }}
</div>

<livewire:components.footer />

@livewireScripts
</body>
</html>
