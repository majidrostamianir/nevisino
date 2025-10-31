{{--<div>--}}
{{--    <aside id="sidebar-multi-level-sidebar"--}}
{{--           class="fixed top-10 sm:top-0 right-0 group z-40 pt-16 w-64 sm:w-16 hover:w-64 shadow-sm h-screen !transition-all translate-x-full sm:translate-x-0"--}}
{{--           aria-label="Sidebar">--}}
{{--        <div class="h-full px-3 py-4 overflow-y-auto bg-pars-100">--}}
{{--            <ul class="space-y-2 font-medium overflow-x-hidden ">--}}
{{--               @foreach(\App\Models\Category::query()->whereNull('parent_id')->get() as $key=> $value)--}}
{{--                    <li>--}}
{{--                        <button type="button"--}}
{{--                                data-target="dropdown-{{ $key }}"--}}
{{--                                onclick="toggleDropdown(this)"--}}
{{--                                class="flex cursor-pointer items-center w-full p-2 text-base  transition duration-75 rounded-lg group hover:bg-pars-400 hover:text-pars-500 "--}}
{{--                                aria-expanded="false">--}}
{{--                            <img src="{{ asset('images/icon/'.$key +1 .'.png') }}" width="30">--}}
{{--                            <span--}}
{{--                                class="flex-1 ms-3 sm:hidden group-hover:block text-right rtl:text-right whitespace-nowrap">{{ $value->title }}</span>--}}
{{--                            <svg class="svg w-3 h-3 transition-transform transform rotate-0 duration-300"--}}
{{--                                 aria-hidden="true"--}}
{{--                                 xmlns="http://www.w3.org/2000/svg" fill="none"--}}
{{--                                 viewBox="0 0 10 6">--}}
{{--                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                      stroke-width="2"--}}
{{--                                      d="m1 1 4 4 4-4"/>--}}
{{--                            </svg>--}}
{{--                        </button>--}}
{{--                        <ul id="dropdown-{{ $key }}"--}}
{{--                            class="overflow-hidden transition-all duration-300 max-h-0 pr-8">--}}
{{--                            @foreach(\App\Models\Category::query()->where('parent_id',$value->id)->get() as $value )--}}
{{--                                <li>--}}
{{--                                    <a href="{{ route('category-page', ['dashed' => $value->dashed_title] ) }}"--}}
{{--                                       wire:navigate--}}
{{--                                       class="flex items-center w-full p-2  transition duration-75 rounded-lg pl-11 group hover:bg-pars-400 hover:text-pars-500  text-xs text-nowrap">{{ $value->title }}</a>--}}
{{--                                </li>--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    </li>--}}
{{--                @endforeach--}}


{{--                --}}{{-- <li>--}}
{{--                     <a href="#"--}}
{{--                        class="flex items-center p-2  rounded-lg dark:text-white hover:bg-pars-400 hover:text-pars-500 group">--}}
{{--                         <img src="{{ asset('images/insta.png') }}" width="35">--}}
{{--                         <span class="flex-1 ms-3 sm:hidden group-hover:block whitespace-nowrap">قالب اینستاگرام</span>--}}
{{--                     </a>--}}
{{--                 </li>--}}

{{--            </ul>--}}
{{--        </div>--}}
{{--    </aside>--}}


{{--    <script>--}}
{{--        document.addEventListener("DOMContentLoaded", function () {--}}
{{--            setupMenu();--}}
{{--        });--}}
{{--        document.addEventListener("livewire:navigated", function () {--}}
{{--            setupMenu();--}}
{{--        });--}}

{{--        function setupMenu() {--}}

{{--            const sidebar = document.getElementById("sidebar-multi-level-sidebar");--}}
{{--            const toggleButton = document.querySelector("[data-drawer-toggle='sidebar-multi-level-sidebar']");--}}
{{--            let isSidebarOpen = false;--}}

{{--            toggleButton.addEventListener("click", function () {--}}
{{--                if (isSidebarOpen) {--}}
{{--                    sidebar.classList.add("translate-x-full");--}}
{{--                } else {--}}
{{--                    sidebar.classList.remove("translate-x-full");--}}
{{--                }--}}
{{--                isSidebarOpen = !isSidebarOpen;--}}
{{--            });--}}

{{--            sidebar.addEventListener("mouseleave", function () {--}}
{{--                document.querySelectorAll("ul[id^='dropdown-']").forEach(dropdown => {--}}
{{--                    dropdown.style.maxHeight = "0px";--}}
{{--                });--}}
{{--                document.querySelectorAll("button[data-target]").forEach(btn => {--}}
{{--                    const icon = btn.querySelector(".svg");--}}
{{--                    icon.classList.remove("rotate-180");--}}
{{--                });--}}
{{--            });--}}
{{--        }--}}


{{--        function toggleDropdown(button) {--}}
{{--            const targetId = button.getAttribute("data-target");--}}
{{--            const dropdown = document.getElementById(targetId);--}}
{{--            const isOpen = dropdown.style.maxHeight && dropdown.style.maxHeight !== "0px";--}}
{{--            const svgIcon = button.querySelector(".svg");--}}

{{--            document.querySelectorAll("ul[id^='dropdown-']").forEach(dropdown => {--}}
{{--                dropdown.style.maxHeight = "0px";--}}
{{--            });--}}
{{--            document.querySelectorAll("button[data-target]").forEach(btn => {--}}
{{--                const icon = btn.querySelector(".svg");--}}
{{--                icon.classList.remove("rotate-180");--}}
{{--            });--}}

{{--            if (!isOpen) {--}}
{{--                dropdown.style.maxHeight = dropdown.scrollHeight + "px";--}}
{{--                svgIcon.classList.add("rotate-180");--}}

{{--            }--}}
{{--        }--}}
{{--    </script>--}}
{{--</div>--}}
