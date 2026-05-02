<div>
    <div class="w-full lg:fixed top-0 right-0 z-50 px-4 py-2 bg-pars-100 shadow-sm">
        <div class="flex flex-col lg:hidden">
            <div class="flex justify-between items-center ">
                <a href="{{ route('home') }}" class="self-center mt-1" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
                </a>
                <div class="w-full pl-4">
                    <livewire:components.search/>
                </div>
            </div>
        </div>
        <div class="hidden lg:flex justify-between items-center">
            <div class="flex items-center w-2/5">
                <a href="{{ route('home') }}" class="w-fit" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12 mt-1">
                </a>
                <div class="w-full">
                    <livewire:components.search/>
                </div>
            </div>

            <div class="flex">
                <a class="flex relative bg-pars-500 hover:bg-pars-600 text-white rounded-md items-center px-2 ml-2"
                   href="{{ route('cart') }}" wire:navigate>
                    <img src="{{ asset('images/cart.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                    <span class="text-xs lg:text-sm py-2.5">سبد خرید</span>
                    @if((int)$cartCount > 0)
                        <span
                            class="absolute -bottom-2 -right-4 bg-red-500 w-7 h-7 flex items-center justify-center text-sm border-2 border-pars-100 rounded-full">
                                {{ english_to_persian_num($cartCount) }}
                            </span>
                    @elseif(Auth::user()->orders()->where('status','pending')->exists())
                        <span
                            class="absolute -bottom-2 -right-4 bg-amber-500 w-7 h-7 flex items-center justify-center text-sm border-2 border-pars-100 rounded-full">
                                {{ english_to_persian_num(Auth::user()->orders()->where('status','pending')->count()) }}
                            </span>
                    @endif
                </a>

                @if(Auth::check())
                    <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                       href="{{ route('dashboard') }}" wire:navigate>
                        <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                        <span class="text-xs lg:text-sm py-2.5">پیش‌خوان</span>
                    </a>
                @else
                    <a class="flex bg-pars-700 hover:bg-pars-800 text-white rounded-md items-center px-2"
                       href="{{ route('register') }}" wire:navigate>
                        <img src="{{ asset('images/user.png') }}" class="w-[16px] h-[16px] ml-1" alt="">
                        <span class="text-xs lg:text-sm py-2.5">ورود به حساب کاربری</span>
                    </a>
                @endif

            </div>
        </div>
    </div>
    <div
        x-show="sidebarOpen && window.innerWidth < 640"
        x-transition.opacity
        @click="sidebarOpen = false; activeDropdown = null"
        class="fixed bg-black/30 inset-0  z-30 lg:hidden"
    ></div>
    <aside
        x-cloak
        class="fixed top-0 lg:top-0 right-0 group z-40 lg:pt-16 w-64 lg:w-16 hover:w-64 shadow-sm h-screen
           bg-pars-100 transition-all duration-200 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
        @mouseover="if(window.innerWidth>=640) sidebarOpen=true"
        @mouseleave="if(window.innerWidth>=640) sidebarOpen=false; activeDropdown=null">

        <div class="h-full px-3 py-4 overflow-y-auto bg-pars-100">
            <ul class="space-y-2 font-medium overflow-x-hidden ">
                @foreach(\App\Models\Category::query()->whereNull('parent_id')->get() as $key=> $value)
                    <li>
                        <button type="button"
                                @click="activeDropdown = (activeDropdown === {{ $key }} ? null : {{ $key }})"
                                class="flex cursor-pointer items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 ">
                            <img src="{{ asset('images/icon/'.$key +1 .'.png') }}" width="30">
                            <span
                                class="flex-1 ms-3 lg:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">{{ $value->title }}</span>
                            <svg class="w-3 h-3 ml-auto transition-transform duration-300"
                                 :class="activeDropdown === {{ $key }} ? 'rotate-180' : 'rotate-0'"
                                 fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul
                            x-show="activeDropdown === {{ $key }}"
                            x-collapse
                            x-cloak
                            class="pr-8 text-xs overflow-hidden transition-all duration-300 ease-in-out"
                        >
                        @foreach(\App\Models\Category::query()->where('parent_id',$value->id)->get() as $value )
                                <li>
                                    <a href="{{ route('category-page', ['dashed' => $value->dashed_url] ) }}"
                                       wire:navigate
                                       class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach


                {{-- <li>
                     <a href="#"
                        class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                         <img src="{{ asset('images/insta.png') }}" width="35">
                         <span class="flex-1 ms-3 lg:hidden group-hover:block whitespace-nowrap">قالب اینستاگرام</span>
                     </a>
                 </li>--}}

            </ul>
        </div>
    </aside>
</div>
