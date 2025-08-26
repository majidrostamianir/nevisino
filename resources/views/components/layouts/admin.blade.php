<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'پنل نویسینو' }}</title>

    @vite('resources/css/app.css')
    @livewireStyles

</head>
<body>
<div class="w-full flex fixed top-0 right-0 z-50 px-4 py-2 justify-between bg-pars-100 shadow-sm">
    <div class="w-2/5 flex ">
        <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar"
                aria-controls="sidebar-multi-level-sidebar" type="button"
                class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" fill-rule="evenodd"
                      d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
        </button>
        <a href="{{ route('home') }}" class="self-center" wire:navigate>
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
        </a>
{{--        <input type="text" class="text-sm bg-gray-100 w-full mr-3 rounded-md pr-4" placeholder="جستجو ...">--}}

    </div>
    <div class="w-fit flex ">
        <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2" href="{{ route('register') }}"
           wire:navigate>
            <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
            @if(Auth::check())
                <span class="text-sm">{{ Auth::user()->mobile }}</span>
                {{--                <a href="{{ Auth::logout() }}"  >logout</a>--}}
            @else
                <span class="text-sm">ورود به حساب کاربری</span>
            @endif
        </a>
    </div>
</div>

<aside id="sidebar-multi-level-sidebar"
       class="fixed top-0 right-0 group z-40 pt-16 w-64 sm:w-16 hover:w-64 shadow-sm h-screen !transition-all translate-x-full sm:translate-x-0"
       aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-pars-100">
        <ul class="space-y-2 font-medium overflow-x-hidden ">
            <li>
                <a href="{{ route('admin.url.index') }}"
                   class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                    <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="URL provider icon">
                        <g fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="6" width="20" height="18" rx="3" ry="3"/>
                            <line x1="5" y1="11" x2="25" y2="11"/>
                            <path d="M17 14h5v5"/>
                            <path d="M22 14l-5 5"/>
                        </g>
                    </svg>
                    <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">آدرس</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.product.save') }}"
                   class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                    <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="URL provider icon">
                        <g fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="6" width="20" height="18" rx="3" ry="3"/>
                            <line x1="5" y1="11" x2="25" y2="11"/>
                            <path d="M17 14h5v5"/>
                            <path d="M22 14l-5 5"/>
                        </g>
                    </svg>
                    <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">محصول جدید</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.product.index') }}"
                   class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                    <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="URL provider icon">
                        <g fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="6" width="20" height="18" rx="3" ry="3"/>
                            <line x1="5" y1="11" x2="25" y2="11"/>
                            <path d="M17 14h5v5"/>
                            <path d="M22 14l-5 5"/>
                        </g>
                    </svg>
                    <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">محصولات</span>
                </a>
            </li>


        </ul>
    </div>
</aside>

<div class="p-4 sm:mr-16 mt-16">
    {{ $slot }}
</div>

@livewireScripts


<script>
    document.addEventListener("DOMContentLoaded", function () {
        setupMenu();
    });
    document.addEventListener("livewire:navigated", function () {
        setupMenu();
    });

    function setupMenu(){

        const sidebar = document.getElementById("sidebar-multi-level-sidebar");
        const toggleButton = document.querySelector("[data-drawer-toggle='sidebar-multi-level-sidebar']");
        let isSidebarOpen = false;

        toggleButton.addEventListener("click", function () {
            if (isSidebarOpen) {
                sidebar.classList.add("translate-x-full");
            } else {
                sidebar.classList.remove("translate-x-full");
            }
            isSidebarOpen = !isSidebarOpen;
        });

        sidebar.addEventListener("mouseleave", function () {
            document.querySelectorAll("ul[id^='dropdown-']").forEach(dropdown => {
                dropdown.style.maxHeight = "0px";
            });
        });
    }



</script>
</body>
</html>
