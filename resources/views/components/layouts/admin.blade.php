<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/logo.png">

    <title>{{ $title ?? 'پنل نویسینو' }}</title>

    @stack('editor')

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles

</head>
<body>
<div x-data="{ sidebarOpen: false, activeDropdown: null }">
    <div class="w-full fixed top-0 right-0 z-50 px-4 py-2 bg-pars-100 shadow-sm">

        <!-- موبایل -->
        <div class="flex flex-col sm:hidden">
            <!-- ردیف اول در موبایل -->
            <div class="flex justify-between items-center mb-2">
                <a href="{{ route('home') }}" class="self-center" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
                </a>
            </div>
            <!-- ردیف دوم در موبایل -->
            <div class="flex justify-between items-center">
                <button  @click="sidebarOpen = !sidebarOpen; if(!sidebarOpen) activeDropdown=null"
                         type="button"
                         class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                              d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                    </svg>
                </button>

                <div class="w-full pl-4">
                    <livewire:components.search/>
                </div>

            </div>
        </div>

        <!-- دسکتاپ -->
        <div class="hidden sm:flex justify-between items-center">
            <div class="flex items-center w-2/5">
                <a href="{{ route('home') }}" class="w-fit" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
                </a>
                <div class="w-full">
                    <livewire:components.search/>
                </div>
            </div>
        </div>
    </div>
    <aside
        class="fixed top-10 sm:top-0 right-0 group z-40 pt-16 w-64 sm:w-16 hover:w-64 shadow-sm h-screen
           bg-pars-100 transition-all duration-200 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full sm:translate-x-0'"
        @mouseover="if(window.innerWidth>=640) sidebarOpen=true"
        @mouseleave="if(window.innerWidth>=640) sidebarOpen=false; activeDropdown=null">

        <div class="h-full px-3 py-4 overflow-y-auto bg-pars-100">
            <ul class="space-y-2 font-medium overflow-x-hidden ">
                <li>
                    <a href="{{ route('admin.user.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        👤
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">کاربران</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.visit.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        👁️
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">بازدیدها</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.static.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        📈
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">آمار</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.order.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        📦
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">سفارشات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.setting.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        ⚙️
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">تنظیمات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.category.index') }}"
                       class="flex items-center p-2 pr-3 rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        ☰
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">منو و دسته بندی</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.url.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        📑
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">سئو صفحات دسته بندی</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.product.save') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        🆕
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">محصول جدید</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.product.index') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        🛍️
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">محصولات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.product.attr') }}"
                       class="flex items-center p-2  rounded-lg  hover:bg-pars-400 hover:text-pars-500 group">
                        ✔️
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">ویژگی محصولات</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
</div>

<livewire:components.toast />

<div class="mx-4 sm:mr-20 sm:ml-4 mt-32 sm:mt-20  min-h-[65vh]">
    {{ $slot }}
</div>

@livewireScripts
</body>
</html>
