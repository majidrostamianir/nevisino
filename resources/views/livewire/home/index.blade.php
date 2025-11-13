<div>
    <div class="flex px-4 sm:px-8 ">
        <div class="w-3/12 ml-2 hidden  sm:block">
            <div class="bg-white  place-items-center border border-pars-500/50 h-56  md:h-96 shadow overflow-hidden rounded-xl text-center">
                <div class="w-full my-4 font-bold">
                    سبد تخفیف
                </div>
                <div class="w-2/4">
                    <div id="controls-carousel" class="relative w-full" data-carousel="slide">
                        <div class="relative h-56 overflow-hidden rounded-lg md:h-80">
                            @foreach(\App\Models\Product::query()->whereNotNull('discounted_price')->limit(5)->get() as $product)
                                <div class="hidden duration-700 ease-in-out" data-carousel-item wire:key="{{ $product->id }}">
                                    <div class="absolute bg-white block rounded-xl w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                        <img src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                           class="mb-4 rounded-xl">
                                        <strong class="text-sm w-full">{{ english_to_persian_num($product->title) }}</strong>
                                        <p class="text-xs w-full line-through mt-0.5">{{ english_to_persian_num(number_format( $product->price))  }} تومان</p>
                                        <strong class="text-sm w-full">{{ english_to_persian_num(number_format($product->discounted_price)) }} تومان</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{--                        <button type="button"--}}
                        {{--                                class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"--}}
                        {{--                                data-carousel-prev>--}}
                        {{--                            <span--}}
                        {{--                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 dark:bg-gray-800/30 group-hover:bg-black/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">--}}
                        {{--                                <svg class="w-4 h-4 text-white dark:text-gray-800 rotate-180" aria-hidden="true"--}}
                        {{--                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">--}}
                        {{--                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"--}}
                        {{--                                          stroke-width="2"--}}
                        {{--                                          d="M5 1 1 5l4 4"/>--}}
                        {{--                                </svg>--}}
                        {{--                                <span class="sr-only">Previous</span>--}}
                        {{--                            </span>--}}
                        {{--                        </button>--}}
                        {{--                        <button type="button"--}}
                        {{--                                class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"--}}
                        {{--                                data-carousel-next>--}}
                        {{--                            <span--}}
                        {{--                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 dark:bg-gray-800/30 group-hover:bg-black/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">--}}
                        {{--                                <svg class="w-4 h-4 text-white dark:text-gray-800 rotate-180" aria-hidden="true"--}}
                        {{--                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">--}}
                        {{--                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"--}}
                        {{--                                          stroke-width="2"--}}
                        {{--                                          d="m1 9 4-4-4-4"/>--}}
                        {{--                                </svg>--}}
                        {{--                                <span class="sr-only">Next</span>--}}
                        {{--                            </span>--}}
                        {{--                        </button>--}}
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full sm:w-9/12 relative" data-carousel="slide" id="default-carousel">
            <div class="relative  h-48 md:h-96 sm:mr-2 overflow-hidden rounded-xl ">
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/test1.webp') }}"
                         class="absolute block h-full rounded-xl -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                         alt="...">
                </div>
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/test2.webp') }}"
                         class="absolute block h-full rounded-xl -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                         alt="...">
                </div>
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/test3.webp') }}"
                         class="absolute block h-full rounded-xl -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                         alt="...">
                </div>
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/test4.webp') }}"
                         class="absolute block h-full rounded-xl -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                         alt="...">
                </div>
            </div>
            <div
                class="absolute z-30 flex bg-black/30 p-1 rounded-xl -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-full cursor-pointer" aria-current="true"
                        aria-label="Slide 1"
                        data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full cursor-pointer" aria-current="false"
                        aria-label="Slide 2"
                        data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full cursor-pointer" aria-current="false"
                        aria-label="Slide 3"
                        data-carousel-slide-to="2"></button>
                <button type="button" class="w-3 h-3 rounded-full cursor-pointer" aria-current="false"
                        aria-label="Slide 4"
                        data-carousel-slide-to="3"></button>
            </div>
            <button type="button"
                    class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev>
        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 dark:bg-gray-800/30 group-hover:bg-black/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white  rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 1 1 5l4 4"/>
            </svg>
            <span class="sr-only">Previous</span>
        </span>
            </button>
            <button type="button"
                    class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next>
        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 dark:bg-gray-800/30 group-hover:bg-black/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white  rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="m1 9 4-4-4-4"/>
            </svg>
            <span class="sr-only">Next</span>
        </span>
            </button>
        </div>
    </div>
</div>
