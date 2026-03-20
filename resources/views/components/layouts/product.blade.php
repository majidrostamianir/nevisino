<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">


    <title>{{ $title ?? 'نویسینو' }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles

    @stack('torob-meta-tags') {{-- متاتگ‌های مخصوص ترب  --}}

</head>
<body>

<div x-data="{ sidebarOpen: false, activeDropdown: null }">

    <livewire:components.header/>
    <livewire:components.toast/>


    <div class="mx-4 lg:mr-20 lg:ml-4 mt-4 lg:mt-20  min-h-[65vh]">
        {{ $slot }}
    </div>


    <livewire:components.footer/>
    <livewire:components.bottom-bar/>

</div>
@livewireScripts

@persist('goftino')
<script type="text/javascript">
    ["keydown","touchmove","touchstart","mouseover"].forEach(function(v){window.addEventListener(v,function(){if(!window.isGoftinoAdded){window.isGoftinoAdded=1;var i="YDxx5N",d=document,g=d.createElement("script"),s="https://www.goftino.com/widget/"+i,l=localStorage.getItem("goftino_"+i);g.type="text/javascript",g.async=!0,g.src=l?s+"?o="+l:s;d.getElementsByTagName("head")[0].appendChild(g);}})});
</script>
@endpersist

</body>
</html>
