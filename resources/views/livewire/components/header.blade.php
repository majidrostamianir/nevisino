<div x-data="{ sidebarOpen: false, activeDropdown: null }">
    <div class="w-full fixed top-0 right-0 z-50 px-4 py-2 bg-pars-100 shadow-sm">

        <!-- موبایل -->
        <div class="flex flex-col sm:hidden">
            <!-- ردیف اول در موبایل -->
            <div class="flex justify-between items-center mb-2">
                <a href="{{ route('home') }}" class="self-center" wire:navigate>
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
                </a>
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
                            <span class="text-sm py-2.5">ورود به حساب</span>
                        </a>
                    @endif
                </div>
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
    <aside
        class="fixed top-10 sm:top-0 right-0 group z-40 pt-16 w-64 sm:w-16 hover:w-64 shadow-sm h-screen
           bg-pars-100 transition-all duration-200 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full sm:translate-x-0'"
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
                                class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">{{ $value->title }}</span>
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
                            class="pl-8 text-xs overflow-hidden transition-all duration-300 ease-in-out"
                        >
                        @foreach(\App\Models\Category::query()->where('parent_id',$value->id)->get() as $value )
                                <li>
                                    <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}"
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
                         <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">قالب اینستاگرام</span>
                     </a>
                 </li>--}}

            </ul>
        </div>
    </aside>

</div>
