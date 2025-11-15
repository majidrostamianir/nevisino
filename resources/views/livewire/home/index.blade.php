<div>
    <div class="flex  gap-4">
        <!-- باکس سفید: سبد تخفیف -->
        <div class="w-3/12 ml-2 hidden sm:flex bg-white  shadow overflow-hidden rounded-xl text-center flex-col">
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
                <div class="relative w-[90%] h-[90%] mx-auto flex items-center justify-center">
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
                            <div class="block w-[80%] h-fit mx-auto mb-5 rounded overflow-hidden">
                                <img :src="product.image" alt="" class="w-full h-full rounded s">
                            </div>

                            <!-- نام محصول -->
                            <div class="block font-bold text-base line-clamp-2 max-w-full" x-text="product.name"></div>

                            <!-- قیمت اصلی (خط خورده) -->
                            <div class="text-sm line-through text-gray-400 mt-4" x-text="formatPrice(product.price)"></div>

                            <!-- قیمت تخفیفی -->
                            <div class="text-lg font-bold text-green-600" x-text="formatPrice(product.discounted_price)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- کاروسل اصلی -->
        <div class="w-full sm:w-9/12 relative flex-1 min-h-[50vh]">
            <div x-data="carousel()" class="relative w-full h-full overflow-hidden rounded-2xl shadow">
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
                            <img :src="slide" alt="" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    </template>
                </div>

                <!-- دکمه قبلی -->
                <button @click="prev()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-pars-800 cursor-pointer p-2 rounded-full shadow-xl transition-all hover:scale-110 z-10">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- دکمه بعدی -->
                <button @click="next()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-pars-800 cursor-pointer p-2 rounded-full shadow-xl transition-all hover:scale-110 z-10">
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

    <div class="bg-fuchsia-600 mt-8 px-6 py-6 rounded-2xl shadow-md relative"
        x-data="{
        current: 0,
        itemWidth: 0,
        init() {
            this.$nextTick(() => {
                this.itemWidth = this.$refs.item0.offsetWidth + 16; // فاصله gap
            });
        },
        next(total) {
            if (this.current < total - 6) this.current++;
        },
        prev() {
            if (this.current > 0) this.current--;
        }
    }">
        <p class="text-2xl text-white font-bold mb-4 text-right">پرفروش های هفته</p>
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
                    <div x-ref="item{{ $index }}" class="min-w-[150px] md:min-w-[180px]">
                        <a href="{{ route('product-page', ['title' => $product->dashed_title]) }}"
                            wire:navigate
                            class="flex flex-col items-center bg-white rounded-xl shadow hover:shadow-lg cursor-pointer h-full overflow-hidden"                        >
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img
                                    class="w-full aspect-square hover:scale-105 transition-transform"
                                    src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                    alt="{{ $product->title }}">
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

    <script>
        function carousel() {
            return {
                slides: [
                    "{{ asset('images/test1.webp') }}",
                    "{{ asset('images/test3.webp') }}",
                    "{{ asset('images/test4.webp') }}",
                    "{{ asset('images/test2.webp') }}",
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
                    this.interval = setInterval(() => this.next(), 3000)
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
