<div class="w-full fixed top-0 right-0 z-50 px-4 py-2 bg-pars-100 shadow-sm">

    <!-- موبایل -->
    <div class="flex flex-col sm:hidden">
        <!-- ردیف اول در موبایل -->
        <div class="flex justify-between items-center mb-2">
            <a href="{{ route('home') }}" class="self-center" wire:navigate>
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
            </a>
            <div class="flex">
                <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2 ml-2"
                   href="{{ route('cart') }}" wire:navigate>
                    <img src="{{ asset('images/cart.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                    <span class="text-sm py-2.5">سبد خرید</span>
                </a>

                @if(Auth::check())
                    <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                       href="{{ route('dashboard') }}" wire:navigate>
                        <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                        <span class="text-sm py-2.5">پیش‌خوان</span>
                    </a>
                @else
                    <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                       href="{{ route('register') }}" wire:navigate>
                        <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                        <span class="text-sm py-2.5">ورود به حساب کاربری</span>
                    </a>
                @endif
            </div>
        </div>
        <!-- ردیف دوم در موبایل -->
        <div class="flex justify-between items-center">
            <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar"
                    aria-controls="sidebar-multi-level-sidebar" type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                          d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>

            <div class="w-full pl-4">
                <livewire:home.search/>
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
                <livewire:home.search/>
            </div>
        </div>

        <div class="flex">
            <a class="flex relative bg-pars-500 hover:bg-pars-600 text-white rounded-md items-center px-2 ml-2"
               href="{{ route('cart') }}" wire:navigate>
                <img src="{{ asset('images/cart.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                <span class="text-sm py-2.5">سبد خرید</span>
                @if($cartCount > 0)
                    <span
                        class="absolute -bottom-2 -right-4 bg-pars-800 px-2 border-2 border-pars-100  rounded-full">{{ english_to_persian_num($cartCount) }}</span>
                @endif
            </a>

            @if(Auth::check())
                <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                   href="{{ route('dashboard') }}" wire:navigate>
                    <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                    <span class="text-sm py-2.5">پیش‌خوان</span>
                </a>
            @else
                <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                   href="{{ route('register') }}" wire:navigate>
                    <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                    <span class="text-sm py-2.5">ورود به حساب کاربری</span>
                </a>
            @endif

        </div>
    </div>

</div>
