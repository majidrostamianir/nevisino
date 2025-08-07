<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'نویسینو' }}</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">

    @vite('resources/css/app.css')

    @livewireStyles

</head>
<body>


<livewire:home.header/>

<livewire:home.side-bar/>

<div class="mx-4 sm:mr-20 sm:ml-4 mt-32 sm:mt-20  ">
    {{ $slot }}
</div>

{{--<livewire:home.footer />--}}

@livewireScripts
</body>
</html>
