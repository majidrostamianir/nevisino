<div>
    <aside id="sidebar-multi-level-sidebar"
           class="fixed top-10 sm:top-0 right-0 group z-40 pt-16 w-64 sm:w-16 hover:w-64 shadow-sm h-screen !transition-all translate-x-full sm:translate-x-0"
           aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-pars-100">
            <ul class="space-y-2 font-medium overflow-x-hidden ">
                <li>
                    <button type="button"
                            data-target="dropdown-1"
                            onclick="toggleDropdown(this)"
                            class="flex items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "
                            aria-expanded="false">
                        <svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Pencil and sharpener icon">
                            <g fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <!-- مداد -->
                                <path d="M8 20l8-8 3 3-8 8-3 1z"/>
                                <path d="M16 12l2-2 3 3-2 2z"/>
                                <!-- تراش پایین -->
                                <rect x="20" y="18" width="6" height="6" rx="1" ry="1"/>
                                <line x1="23" y1="18" x2="23" y2="24"/>
                                <circle cx="23" cy="21" r="1"/>
                            </g>
                        </svg>
                        <span class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">نوشت افزار</span>
                        <svg class="svg w-3 h-3 transition-transform transform rotate-0 duration-300" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-1"
                        class="overflow-hidden transition-all duration-300 max-h-0 pr-8">
                        @foreach(\App\Models\Category::query()->where('parent_id',1)->get() as $value )
                            <li>
                                <a {{--href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}"--}} wire:navigate
                                   class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                {{--<li>
                    <button type="button"
                            data-target="dropdown-2"
                            onclick="toggleDropdown(this)"
                            class="flex items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "
                            aria-expanded="false">
                        <img src="{{ asset('images/banner.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">بنر</span>
                        <svg class="w-3 h-3 transition-transform transform rotate-0 duration-300" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-2"
                        class="overflow-hidden transition-all duration-300 max-h-0 pr-8">
                        @foreach(\App\Models\Url::query()->where('category_id',2)->get() as $value )
                            <li>
                                <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}" wire:navigate
                                   class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <button type="button"
                            data-target="dropdown-3"
                            onclick="toggleDropdown(this)"
                            class="flex items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "
                            aria-expanded="false">
                        <img src="{{ asset('images/flyer.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">تراکت</span>
                        <svg class="w-3 h-3 transition-transform transform rotate-0 duration-300" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-3"
                        class="overflow-hidden transition-all duration-300 max-h-0 pr-8">
                        @foreach(\App\Models\Url::query()->where('category_id',3)->get() as $value )
                            <li>
                                <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}" wire:navigate
                                   class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <button type="button"
                            data-target="dropdown-4"
                            onclick="toggleDropdown(this)"
                            class="flex items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "
                            aria-expanded="false">
                        <img src="{{ asset('images/vector.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">وکتور</span>
                        <svg class="w-3 h-3 transition-transform transform rotate-0 duration-300" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-4"
                        class="overflow-hidden transition-all duration-300 max-h-0 pr-8">
                        @foreach(\App\Models\Url::query()->where('category_id',4)->get() as $value )
                            <li>
                                <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}" wire:navigate
                                   class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <button type="button"
                            data-target="dropdown-5"
                            onclick="toggleDropdown(this)"
                            class="flex items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "
                            aria-expanded="false">
                        <img src="{{ asset('images/head.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">سربرگ و فاکتور</span>
                        <svg class="w-3 h-3 transition-transform transform rotate-0 duration-300" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-5"
                        class="overflow-hidden transition-all duration-300 max-h-0 pr-8">
                        @foreach(\App\Models\Url::query()->where('category_id',5)->get() as $value )
                            <li>
                                <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}" wire:navigate
                                   class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <a href="#"
                       class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                        <img src="{{ asset('images/menu.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">منو رستوران</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('category-page' , ['dashed' => 'شیت-لایه-باز']) }}"
                       class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                        <img src="{{ asset('images/sheet.png') }}" width="35" alt="">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">شیت معماری</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                        <img src="{{ asset('images/insta.png') }}" width="35">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">قالب اینستاگرام</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                       class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                        <img src="{{ asset('images/prescription.png') }}" width="35">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">نسخه پزشک</span>
                    </a>
                </li>
                <li class="pb-10">
                    <a href="#"
                       class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">
                        <img src="{{ asset('images/obituary.png') }}" width="35">
                        <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">آگهی ترحیم</span>
                    </a>
                </li>--}}
            </ul>
        </div>
    </aside>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setupMenu();
        });
        document.addEventListener("livewire:navigated", function () {
            setupMenu();
        });

        function setupMenu() {

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
                document.querySelectorAll("button[data-target]").forEach(btn => {
                    const icon = btn.querySelector("svg");
                    icon.classList.remove("rotate-180");
                });
            });
        }


        function toggleDropdown(button) {
            const targetId = button.getAttribute("data-target");
            const dropdown = document.getElementById(targetId);
            const isOpen = dropdown.style.maxHeight && dropdown.style.maxHeight !== "0px";
            const svgIcon = button.querySelector(".svg");

            document.querySelectorAll("ul[id^='dropdown-']").forEach(dropdown => {
                dropdown.style.maxHeight = "0px";
            });
            document.querySelectorAll("button[data-target]").forEach(btn => {
                const icon = btn.querySelector(".svg");
                icon.classList.remove("rotate-180");
            });

            if (!isOpen) {
                dropdown.style.maxHeight = dropdown.scrollHeight + "px";
                svgIcon.classList.add("rotate-180");

            }
        }
    </script>
</div>
