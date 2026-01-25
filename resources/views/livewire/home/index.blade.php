<div>
    <livewire:components.story />

    <div class="flex gap-4">
        <!-- باکس سفید: سبد تخفیف -->
        <div class="w-3/12 aspect-[2.5] hidden lg:flex bg-yellow-300 shadow-md overflow-hidden rounded-xl text-center flex-col">
            <div class="w-full py-4 font-bold ">
                سبد تخفیف
            </div>
            <div x-data="productCarousel(@js($productsForJs))"
                 class="relative flex-1 min-h-[50vh] flex flex-col justify-center">
                <div class="absolute top-0 left-0 right-0 h-0.5  overflow-hidden z-10">
                    <div
                        x-ref="progress"
                        class="h-full bg-red-500 transition-none"
                        style="width: 0%;"
                        x-bind:style="progressWidth"
                    ></div>
                </div>

                <!-- محتوای محصول — کل آیتم قابل کلیک -->
                <div class="relative w-[90%] h-[90%] mx-auto flex items-center justify-center ">
                    <template x-for="(product, index) in products" :key="index">
                        <div
                            x-show="currentIndex === index"
                            x-transition:enter="transition duration-500"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0 flex flex-col items-center justify-center px-4"
                            @click="window.location.href = product.link"
                            wire:navigate
                            style="cursor: pointer;"
                        >
                            <!-- عکس محصول — بزرگتر -->
                            <div class="block w-[80%] h-auto mx-auto mb-5 rounded-2xl overflow-hidden shadow-2xl">
                                <img :src="product.image" alt="" class="w-full h-full rounded-2xl border border-gray-300">
                            </div>

                            <!-- نام محصول -->
                            <div class="block font-bold text-base line-clamp-2 max-w-full " x-text="product.name"></div>

                            <!-- قیمت اصلی (خط خورده) -->
                            <div class="text-sm line-through text-gray-400 mt-4"
                                 x-text="formatPrice(product.price)"></div>

                            <!-- قیمت تخفیفی -->
                            <div class="text-lg font-bold text-green-600"
                                 x-text="formatPrice(product.discounted_price)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <!-- کاروسل اصلی -->
        <div class="w-full lg:w-9/12 relative ">
            <div x-data="carousel()" class="relative w-full aspect-[2.5] overflow-hidden rounded-2xl shadow">
                <!-- اسلایدها -->
                <div class="relative h-full">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div
                            x-show="currentIndex === index"
                            x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0 translate-x-full"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-full"
                            class="absolute inset-0 w-full h-full"
                        >
                            <a :href="slide[1]">
                                <img :src="slide[0]" alt="" class="w-full h-full object-cover" loading="lazy">
                            </a>
                        </div>
                    </template>

                </div>

                <!-- دکمه قبلی -->
                <button @click="prev()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-pars-800 cursor-pointer p-1 lg:p-2 rounded-full shadow-xl transition-all hover:scale-110 ">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- دکمه بعدی -->
                <button @click="next()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-pars-800 cursor-pointer p-1 lg:p-2 rounded-full shadow-xl transition-all hover:scale-110 ">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- دات‌های پایین -->
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex flex-row-reverse gap-3 ">
                    <template x-for="(slide, index) in slides">
                        <button @click="currentIndex = index; resetInterval()"
                                :class="currentIndex === index ? 'bg-white w-12 h-3 ' : 'bg-white/60 hover:bg-white/90 w-3 h-3'"
                                class="rounded-full transition-all duration-300 shadow-lg cursor-pointer">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 sm:px-4 my-8">

        <div class="flex items-center bg-white rounded-2xl shadow-[0_2px_6px_rgba(0,0,0,0.1)] p-3">
            <img src="{{ asset('images/price.png') }}" class="w-10 h-10 flex-shrink-0" alt="">
            <div class="mr-3">
                <strong class="block text-sm lg:text-base text-black">بهترین قیمت بازار</strong>
                <span class="block text-xs text-gray-600">قیمت پایین در کنار حفظ کیفیت</span>
            </div>
        </div>

        <div class="flex items-center bg-white rounded-2xl shadow-[0_2px_6px_rgba(0,0,0,0.1)] p-3">
            <img src="{{ asset('images/guarantee.png') }}" class="w-10 h-10 flex-shrink-0" alt="">
            <div class="mr-3">
                <strong class="block text-sm lg:text-base text-black">ضمانت مرجوعی</strong>
                <span class="block text-xs text-gray-600">امکان مرجوعی کالا تا ۴۸ ساعت</span>
            </div>
        </div>

        <div class="flex items-center bg-white rounded-2xl shadow-[0_2px_6px_rgba(0,0,0,0.1)] p-3">
            <img src="{{ asset('images/delivery.png') }}" class="w-10 h-10 flex-shrink-0" alt="">
            <div class="mr-3">
                <strong class="block text-sm lg:text-base text-black">ارسال فوری</strong>
                <span class="block text-xs text-gray-600">تحویل به پست بصورت روزانه</span>
            </div>
        </div>

        <div class="flex items-center bg-white rounded-2xl shadow-[0_2px_6px_rgba(0,0,0,0.1)] p-3">
            <img src="{{ asset('images/support.png') }}" class="w-10 h-10 flex-shrink-0" alt="">
            <div class="mr-3">
                <strong class="block text-sm lg:text-base text-black">پشتیبانی قوی</strong>
                <span class="block text-xs text-gray-600">پاسخ دهی در کوتاه‌ترین زمان</span>
            </div>
        </div>

    </div>

    <div class="bg-[radial-gradient(circle_at_center,_#ff7b00_0%,_#ff4500_40%,_#e11d48_100%)] my-8 px-6 py-6 rounded-2xl shadow-2xl relative"
         x-data="{
        current: 0,
        itemWidth: 0,
        init() {
            this.$nextTick(() => {
                this.itemWidth = this.$refs.item0.offsetWidth + 16; // فاصله gap
            });
        },
        next(total) {
            if (this.current < total - 1) this.current++;
        },
        prev() {
            if (this.current > 0) this.current--;
        }
    }">
        <p class="text-lg md:text-2xl text-white font-bold mb-4 text-right text-shadow-sm text-shadow-black">لوازم نقاشی و رنگ آمیزی
            <a href="{{ route('category-page' , ['dashed' => 'لوازم-نقاشی-رنگ-آمیزی']) }}"><small>(مشاهده همه)</small></a></p>
        <div class="overflow-hidden relative">
            <button
                @click="prev()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button
                @click="next({{ count($paintProducts) }})"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div
                class="flex gap-6 transition-transform duration-300 px-8"
                :style="`transform: translateX(${current * itemWidth}px)`">
                @foreach($paintProducts as $index => $product)
                    <div x-ref="item{{ $index }}" class="min-w-[180px] md:min-w-[230px]">
                        <a href="{{ route('product-page', ['title' => $product->dashed_url]) }}"
                           wire:navigate
                           class="flex flex-col items-center bg-white rounded-xl border border-red-500  cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl ">
                                <img
                                    class="w-full aspect-square hover:scale-105 transition-transform"
                                    src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                    alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                        تخفیف ویژه!
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 py-3 text-center">
                                <h5 class="text-md font-semibold mb-1">
                                    {{ english_to_persian_num($product->title) }}
                                </h5>
                                @if($product->discounted_price)
                                    <h5 class="text-xs text-gray-400 line-through">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->discounted_price)) }} تومان
                                    </h5>
                                @else
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

{{--    <div class="grid grid-cols-2 gap-4 px-4 my-8 lg:grid-cols-4">--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--    </div>--}}


    <div class="bg-[radial-gradient(circle_at_center,_#F783A3_0%,_#FCB404_40%,_#FCB404_100%)] my-8 px-6 py-6 rounded-2xl shadow-md relative"
         x-data="{
        current: 0,
        itemWidth: 0,
        init() {
            this.$nextTick(() => {
                this.itemWidth = this.$refs.item0.offsetWidth + 16; // فاصله gap
            });
        },
        next(total) {
            if (this.current < total - 1) this.current++;
        },
        prev() {
            if (this.current > 0) this.current--;
        }
    }">
        <p class="text-lg md:text-2xl text-white font-bold mb-4 text-right text-shadow-sm text-shadow-black">پرفروش های هفته</p>
        <div class="overflow-hidden relative">
            <button
                @click="prev()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button
                @click="next({{ count($topProducts) }})"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div
                class="flex gap-6 transition-transform duration-300 px-8"
                :style="`transform: translateX(${current * itemWidth}px)`">
                @foreach($topProducts as $index => $product)
                    <div x-ref="item{{ $index }}" class="min-w-[180px] md:min-w-[230px]">
                        <a href="{{ route('product-page', ['title' => $product->dashed_url]) }}"
                           wire:navigate
                           class="flex flex-col items-center bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img
                                    class="w-full aspect-square hover:scale-105 transition-transform"
                                    src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                    alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                        تخفیف ویژه!
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 py-3 text-center">
                                <h5 class="text-md font-semibold mb-1">
                                    {{ english_to_persian_num($product->title) }}
                                </h5>
                                @if($product->discounted_price)
                                    <h5 class="text-xs text-gray-400 line-through">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->discounted_price)) }} تومان
                                    </h5>
                                @else
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                @endif
                            </div>
                        </a>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

{{--    <div class="grid grid-cols-2 gap-4 px-4 my-8 lg:grid-cols-4">--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test33.png') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden shadow-md">--}}
{{--            <img src="{{ asset('images/test001.jpg') }}" class="hover:scale-105 transition w-full" alt="">--}}
{{--        </a>--}}
{{--    </div>--}}

    <div class="bg-[radial-gradient(circle_at_center,_#D6794D_0%,_#C35627_40%,_#D6794D_100%)] my-8 px-6 py-6 rounded-2xl shadow-md relative"
         x-data="{
        current: 0,
        itemWidth: 0,
        init() {
            this.$nextTick(() => {
                this.itemWidth = this.$refs.item0.offsetWidth + 16; // فاصله gap
            });
        },
        next(total) {
            if (this.current < total - 1) this.current++;
        },
        prev() {
            if (this.current > 0) this.current--;
        }
    }">
        <p class="text-lg md:text-2xl text-white font-bold mb-4 text-right text-shadow-sm text-shadow-black">لوازم اداری</p>
        <div class="overflow-hidden relative">
            <button
                @click="prev()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button
                @click="next({{ count($officeProducts) }})"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-white text-gray-700 p-3 rounded-full shadow hover:bg-gray-100 transition z-10 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div
                class="flex gap-6 transition-transform duration-300 px-8"
                :style="`transform: translateX(${current * itemWidth}px)`">
                @foreach($officeProducts as $index => $product)
                    <div x-ref="item{{ $index }}" class="min-w-[180px] md:min-w-[230px]">
                        <a href="{{ route('product-page', ['title' => $product->dashed_url]) }}"
                           wire:navigate
                           class="flex flex-col items-center bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img
                                    class="w-full aspect-square hover:scale-105 transition-transform"
                                    src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                    alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                        تخفیف ویژه!
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 py-3 text-center">
                                <h5 class="text-md font-semibold mb-1">
                                    {{ english_to_persian_num($product->title) }}
                                </h5>
                                @if($product->discounted_price)
                                    <h5 class="text-xs text-gray-400 line-through">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->discounted_price)) }} تومان
                                    </h5>
                                @else
                                    <h5 class="text-sm font-bold mt-1">
                                        {{ english_to_persian_num(number_format($product->price)) }} تومان
                                    </h5>
                                @endif
                            </div>
                        </a>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

{{--    <div class="lg:flex gap-2">--}}
{{--        <img class="lg:w-1/2 rounded-2xl shadow shadow-black" src="{{ asset('images/test-1.webp') }}" alt="">--}}
{{--        <img class="lg:w-1/2 rounded-2xl shadow shadow-black" src="{{ asset('images/test-2.webp') }}" alt="">--}}
{{--    </div>--}}

    <script>
        function carousel() {
            return {
                slides: [
                    ["{{ asset('images/banner1.webp') }}" , '/category/دفتر-دفترچه'],
                    ["{{ asset('images/banner2.webp') }}", '/category/مداد-رنگی'],
                ],
                currentIndex: 0,
                interval: null,

                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.slides.length
                    this.resetInterval()
                },
                prev() {
                    this.currentIndex = this.currentIndex === 0
                        ? this.slides.length - 1
                        : this.currentIndex - 1
                    this.resetInterval()
                },
                resetInterval() {
                    clearInterval(this.interval)
                    this.startAutoPlay()
                },
                startAutoPlay() {
                    this.interval = setInterval(() => this.next(), 4500)
                },
                init() {
                    this.startAutoPlay()
                }
            }
        }

        function productCarousel(initialProducts = []) {
            return {
                products: Array.isArray(initialProducts) ? initialProducts : [],
                currentIndex: 0,
                progressInterval: null,
                progressWidth: 'width: 0%',

                next() {
                    if (this.products.length === 0) return
                    this.currentIndex = (this.currentIndex + 1) % this.products.length
                    this.resetProgress()
                },

                resetProgress() {
                    this.progressWidth = 'width: 0%'
                    cancelAnimationFrame(this.progressInterval)
                    this.startProgress()
                },

                startProgress() {
                    if (this.products.length === 0) return
                    const duration = 4000
                    const startTime = performance.now()

                    const step = (currentTime) => {
                        const elapsed = currentTime - startTime
                        const progress = Math.min(elapsed / duration, 1)
                        const width = progress * 100

                        this.progressWidth = `width: ${width}%`

                        if (progress < 1) {
                            this.progressInterval = requestAnimationFrame(step)
                        } else {
                            this.next()
                        }
                    }

                    this.progressInterval = requestAnimationFrame(step)
                },

                formatPrice(price) {
                    return Number(price).toLocaleString('fa-IR') + ' تومان'
                },

                init() {
                    if (this.products.length > 0) {
                        this.startProgress()
                    }
                }
            }
        }
    </script>
</div>
