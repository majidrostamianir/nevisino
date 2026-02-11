<div
    class="fixed bottom-0 right-0 z-10 w-full h-14 bg-pars-100 flex items-center shadow-[0_-4px_10px_rgba(0,0,0,0.15)] lg:hidden">

    <button
        type="button"
        @click="sidebarOpen = true"
        class="flex flex-col items-center justify-center gap-1 px-2 rounded-md w-full">
        <img src="{{ asset('images/menu.png') }}" class="w-4 h-4" alt="">

        <span class="text-xs">دسته بندی</span>
    </button>
    {{-- <div class="relative w-full text-center">
         <a
             href="{{ route('cart') }}"
             wire:navigate
             class="flex flex-col items-center justify-center gap-1 px-2 rounded-md">
             <img src="{{ asset('images/cart-black.png') }}" class="w-4 h-4" alt="">
             <span class="text-xs lg:text-sm">پشتیبانی</span>
             @if($cartCount > 0)
                 <span
                     class="absolute top-0 left-[20%] bg-red-500 text-white px-2 text-xs border-2 border-pars-100 rounded-full">
                     {{ english_to_persian_num($cartCount) }}
                 </span>
             @endif
         </a>
     </div>--}}
    <div class="relative w-full text-center">
        <a
            href="{{ route('home') }}"
            wire:navigate
            class="flex flex-col items-center justify-center gap-1 px-2 rounded-md">
            <img src="{{ asset('images/home.png') }}" class="w-4 h-4" alt="">
            <span class="text-xs lg:text-sm">صفحه اصلی</span>
        </a>
    </div>
    <div class="relative w-full text-center">
        <a
            href="{{ route('cart') }}"
            wire:navigate
            class="flex flex-col items-center justify-center gap-1 px-2 rounded-md">
            <img src="{{ asset('images/cart-black.png') }}" class="w-4 h-4" alt="">
            <span class="text-xs lg:text-sm">سبد خرید</span>
            @if((int)$cartCount > 0)
                <span
                    class="absolute top-0 left-[20%] bg-red-500 text-white w-6 h-6 flex items-center justify-center text-xs border-2 border-pars-100 rounded-full">
                        {{ english_to_persian_num($cartCount) }}
                    </span>

            @endif
        </a>
    </div>
    <div class="relative w-full text-center">
        @if(Auth::check())
            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="flex flex-col items-center justify-center gap-1 px-2 rounded-md">
                <img src="{{ asset('images/user-black.png') }}" class="w-4 h-4" alt="">
                <span class="text-xs lg:text-sm">پیش‌خوان</span>
            </a>
        @else
            <a
                href="{{ route('register') }}"
                wire:navigate
                class="flex flex-col items-center justify-center gap-1 px-2 rounded-md">
                <img src="{{ asset('images/user-black.png') }}" class="w-4 h-4" alt="">
                <span class="text-xs lg:text-sm">ورود</span>
            </a>
        @endif

    </div>
</div>
