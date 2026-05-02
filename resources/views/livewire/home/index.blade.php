<div>
    <livewire:components.story/>
    
    <div class="flex gap-4 mb-8">
        <!-- باکس سفید: سبد تخفیف -->
        <div
            class="w-3/12 aspect-[2.5] hidden lg:flex bg-yellow-300 shadow-md overflow-hidden rounded-xl text-center flex-col">
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
                        <a
                            x-show="currentIndex === index"
                            x-transition:enter="transition duration-500"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0 flex flex-col items-center justify-center px-4 cursor-pointer"
                            :href="product.link"
                            wire:navigate
                        >
                            <!-- عکس محصول — بزرگتر -->
                            <div class="block w-[80%] h-auto mx-auto mb-5 rounded-2xl overflow-hidden shadow-2xl">
                                <img :src="product.image" alt=""
                                     class="w-full h-full rounded-2xl border border-gray-300">
                            </div>
                            
                            <!-- نام محصول -->
                            <div class="block font-bold text-base line-clamp-2 max-w-full " x-text="product.name"></div>
                            
                            <!-- قیمت اصلی (خط خورده) -->
                            <div class="text-sm line-through text-gray-400 mt-4"
                                 x-text="formatPrice(product.price)"></div>
                            
                            <!-- قیمت تخفیفی -->
                            <div class="text-lg font-bold text-green-600"
                                 x-text="formatPrice(product.discounted_price)"></div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
        <!-- کاروسل اصلی -->
        <div class="w-full lg:w-9/12 relative ">
            <div x-data="carousel()"
                 x-init="slides = [
        ['{{ asset('images/banner1.webp') }}', '/category/دفتر'],
        ['{{ asset('images/banner2.webp') }}', '/category/مداد-رنگی']
    ]"
                 class="relative w-full aspect-[2.5] overflow-hidden rounded-2xl shadow">
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
    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 sm:px-4 mb-8">
        
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
                <span class="block text-xs text-gray-600">امکان مرجوعی کالا تا ۷۲ ساعت</span>
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
    
    <div class="relative bg-gradient-to-r from-purple-300 to-purple-600 mb-8  shadow-lg px-4 rounded-2xl"
        x-data="{
            scrollLeft(container) {
                container.scrollBy({ left: -280, behavior: 'smooth' });
            },
            scrollRight(container) {
                container.scrollBy({ left: 280, behavior: 'smooth' });
            }
        }">
        <button
            @click="scrollRight($refs.sliderContainer)"
            class="hidden lg:flex absolute right-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        
        <button
            @click="scrollLeft($refs.sliderContainer)"
            class="hidden lg:flex absolute left-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        
        <div x-ref="sliderContainer"
             class="flex overflow-x-auto overflow-y-hidden hide-scrollbar gap-x-5 w-full scroll-smooth"
             style="scroll-behavior: smooth;">
            <div class="min-w-[180px] md:min-w-[230px] h-auto flex flex-col py-8 text-center">
                <div class="flex-grow py-4">
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        مداد رنگی اتودی
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۱۲ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۱۸ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۲۴ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۳۶ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        با نوک قابل تعویض
                    </p>
                </div>
                
                <div class="mt-auto pt-2 pb-3">
                    <a href="{{ route('category-page' , 'مداد-رنگی-اتودی') }}" class="inline-flex items-center gap-1 text-sm font-bold  text-white group">
                        <span>مشاهده همه</span>
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @foreach($cClass as $product)
                @if($product->variants->where('stock', '>', 0)->count() > 0)
                    @foreach($product->variants->where('stock', '>', 0) as $variant)
                        <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                            <a href="{{ route('product-page', array_filter([
                            'title' => $product->dashed_url,
                            'npi' => $product->id,
                            'nvi' => $variant->id
                        ])) }}"
                               wire:navigate
                               class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                                <div class="w-full relative overflow-hidden rounded-t-xl">
                                    <img class="w-full aspect-square hover:scale-125 transition-transform"
                                         src="{{ asset('storage/products/' . $product->id . '/small/' . $variant->id . '.webp') }}"
                                         alt="{{ $product->title }} - {{ $variant->name }}">
                                    @if($product->discounted_price)
                                        <div
                                            class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                            تخفیف ویژه!
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 py-3 text-center">
                                    <h5 class="text-md font-semibold mb-1">
                                        {{ english_to_persian_num($product->title) }}
                                    </h5>
                                    <p class="text-xs text-gray-500 mb-1">{{ $product->variant }}
                                        : {{ $variant->name }}</p>
                                    @if($product->discounted_price)
                                        <h5 class="text-xs text-gray-400 line-through">
                                            {{ english_to_persian_num(number_format($product->price)) }} تومان
                                        </h5>
                                        <h5 class="text-sm font-bold mt-1">
                                            {{ english_to_persian_num(number_format($product->discounted_price)) }}
                                            تومان
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
                @else
                    <div class="min-w-[180px] md:min-w-[230px] py-8">
                        <a href="{{ route('product-page', [
                        'title' => $product->dashed_url,
                        'npi' => $product->id
                    ]) }}"
                           wire:navigate
                           class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img class="w-full aspect-square hover:scale-125 transition-transform"
                                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                     alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div
                                        class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
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
                @endif
            @endforeach
        
        </div>
    </div>
    
{{--    <div class="grid grid-cols-2 gap-4 px-4 mb-8 lg:grid-cols-4">--}}
{{--        <a href="" class="rounded-2xl overflow-hidden ">--}}
{{--            <img src="{{ asset('images/test.jpg') }}" class=" transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden ">--}}
{{--            <img src="{{ asset('images/test.jpg') }}" class=" transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden ">--}}
{{--            <img src="{{ asset('images/test.jpg') }}" class=" transition w-full" alt="">--}}
{{--        </a>--}}
{{--        <a href="" class="rounded-2xl overflow-hidden ">--}}
{{--            <img src="{{ asset('images/test.jpg') }}" class=" transition w-full" alt="">--}}
{{--        </a>--}}
{{--    </div>--}}
    
    <div class="relative bg-radial from-orange-300 to-orange-500 mb-8 shadow-lg  px-4 rounded-2xl"
         x-data="{
            scrollLeft(container) {
                container.scrollBy({ left: -280, behavior: 'smooth' });
            },
            scrollRight(container) {
                container.scrollBy({ left: 280, behavior: 'smooth' });
            }
        }">
        <button
            @click="scrollRight($refs.sliderContainer)"
            class="hidden lg:flex absolute right-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        
        <button
            @click="scrollLeft($refs.sliderContainer)"
            class="hidden lg:flex absolute left-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        
        <div x-ref="sliderContainer"
             class="flex overflow-x-auto overflow-y-hidden hide-scrollbar gap-x-5 w-full scroll-smooth"
             style="scroll-behavior: smooth;">
            <div class="min-w-[180px] md:min-w-[230px] h-auto flex flex-col py-8 text-center">
                <div class="flex-grow py-4">
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        مداد رنگی آریا آرتیست
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۱۲ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۱۲ رنگ فلورسنت
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۲۴ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۳۶ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ۵۰ رنگ
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        انتخاب حرفه ای ها
                    </p>
                </div>
                
                <div class="mt-auto pt-2 pb-3">
                    <a href="{{ route('category-page' , 'مداد-رنگی-آریا-آرتیست') }}" class="inline-flex items-center gap-1 text-sm font-bold  text-white group">
                        <span>مشاهده همه</span>
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @foreach($ariaArtist as $product)
                @if($product->variants->where('stock', '>', 0)->count() > 0)
                    @foreach($product->variants->where('stock', '>', 0) as $variant)
                        <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                            <a href="{{ route('product-page', array_filter([
                            'title' => $product->dashed_url,
                            'npi' => $product->id,
                            'nvi' => $variant->id
                        ])) }}"
                               wire:navigate
                               class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                                <div class="w-full relative overflow-hidden rounded-t-xl">
                                    <img class="w-full aspect-square hover:scale-125 transition-transform"
                                         src="{{ asset('storage/products/' . $product->id . '/small/' . $variant->id . '.webp') }}"
                                         alt="{{ $product->title }} - {{ $variant->name }}">
                                    @if($product->discounted_price)
                                        <div
                                            class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                            تخفیف ویژه!
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 py-3 text-center">
                                    <h5 class="text-md font-semibold mb-1">
                                        {{ english_to_persian_num($product->title) }}
                                    </h5>
                                    <p class="text-xs text-gray-500 mb-1">{{ $product->variant }}
                                        : {{ $variant->name }}</p>
                                    @if($product->discounted_price)
                                        <h5 class="text-xs text-gray-400 line-through">
                                            {{ english_to_persian_num(number_format($product->price)) }} تومان
                                        </h5>
                                        <h5 class="text-sm font-bold mt-1">
                                            {{ english_to_persian_num(number_format($product->discounted_price)) }}
                                            تومان
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
                @else
                    <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                        <a href="{{ route('product-page', [
                        'title' => $product->dashed_url,
                        'npi' => $product->id
                    ]) }}"
                           wire:navigate
                           class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img class="w-full aspect-square hover:scale-125 transition-transform"
                                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                     alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div
                                        class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
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
                @endif
            @endforeach
        
        </div>
    </div>

{{--    <div class="bg-gray-300 rounded-2xl p-6 flex mb-8">--}}
{{--        <span class="font-bold text-red-400">نحوه بسته بندی و ارسال در نویسینو</span>--}}
{{--        <img src="{{ asset('images/post.png') }}" class="w-8 h-8 mx-4" alt="">--}}
{{--    </div>--}}
    
    <div class="relative bg-gradient-to-l from-black/80 to-black/50 mb-8 shadow-lg  px-4 rounded-2xl"
         x-data="{
            scrollLeft(container) {
                container.scrollBy({ left: -280, behavior: 'smooth' });
            },
            scrollRight(container) {
                container.scrollBy({ left: 280, behavior: 'smooth' });
            }
        }">
        <button
            @click="scrollRight($refs.sliderContainer)"
            class="hidden lg:flex absolute right-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        
        <button
            @click="scrollLeft($refs.sliderContainer)"
            class="hidden lg:flex absolute left-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        
        <div x-ref="sliderContainer"
             class="flex overflow-x-auto overflow-y-hidden hide-scrollbar gap-x-5 w-full scroll-smooth"
             style="scroll-behavior: smooth;">
            <div class="min-w-[180px] md:min-w-[230px] h-auto flex flex-col py-8 text-center">
                <div class="flex-grow py-4">
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        انواع دفتر مشق
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        جلد سخت
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        جلد طلقی
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        ته چسب
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        سیمی
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        منگنه ای
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        تعداد برگ های متنوع
                    </p>
                </div>
                
                <div class="mt-auto pt-2 pb-3">
                    <a href="{{ route('category-page' , 'دفتر-مشق') }}" class="inline-flex items-center gap-1 text-sm font-bold  text-white group">
                        <span>مشاهده همه</span>
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @foreach($daftarMashq as $product)
                @if($product->variants->where('stock', '>', 0)->count() > 0)
                    @foreach($product->variants->where('stock', '>', 0) as $variant)
                        <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                            <a href="{{ route('product-page', array_filter([
                            'title' => $product->dashed_url,
                            'npi' => $product->id,
                            'nvi' => $variant->id
                        ])) }}"
                               wire:navigate
                               class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                                <div class="w-full relative overflow-hidden rounded-t-xl">
                                    <img class="w-full aspect-square hover:scale-125 transition-transform"
                                         src="{{ asset('storage/products/' . $product->id . '/small/' . $variant->id . '.webp') }}"
                                         alt="{{ $product->title }} - {{ $variant->name }}">
                                    @if($product->discounted_price)
                                        <div
                                            class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                            تخفیف ویژه!
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 py-3 text-center">
                                    <h5 class="text-md font-semibold mb-1">
                                        {{ english_to_persian_num($product->title) }}
                                    </h5>
                                    <p class="text-xs text-gray-500 mb-1">{{ $product->variant }}
                                        : {{ $variant->name }}</p>
                                    @if($product->discounted_price)
                                        <h5 class="text-xs text-gray-400 line-through">
                                            {{ english_to_persian_num(number_format($product->price)) }} تومان
                                        </h5>
                                        <h5 class="text-sm font-bold mt-1">
                                            {{ english_to_persian_num(number_format($product->discounted_price)) }}
                                            تومان
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
                @else
                    <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                        <a href="{{ route('product-page', [
                        'title' => $product->dashed_url,
                        'npi' => $product->id
                    ]) }}"
                           wire:navigate
                           class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img class="w-full aspect-square hover:scale-125 transition-transform"
                                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                     alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div
                                        class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
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
                @endif
            @endforeach
        
        </div>
    </div>
    
{{--    <div class="bg-gray-300 rounded-2xl p-6 flex mb-8">--}}
{{--        <span class="font-bold text-red-400">نحوه بسته بندی و ارسال در نویسینو</span>--}}
{{--        <img src="{{ asset('images/post.png') }}" class="w-8 h-8 mx-4" alt="">--}}
{{--    </div>--}}
    
    <div class="relative bg-radial from-green-100 to-green-500 mb-8 shadow-lg  px-4 rounded-2xl"
         x-data="{
            scrollLeft(container) {
                container.scrollBy({ left: -280, behavior: 'smooth' });
            },
            scrollRight(container) {
                container.scrollBy({ left: 280, behavior: 'smooth' });
            }
        }">
        <button
            @click="scrollRight($refs.sliderContainer)"
            class="hidden lg:flex absolute right-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        
        <button
            @click="scrollLeft($refs.sliderContainer)"
            class="hidden lg:flex absolute left-2 top-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-all cursor-pointer border border-gray-300"
            style="top: 55%; transform: translateY(-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        
        <div x-ref="sliderContainer"
             class="flex overflow-x-auto overflow-y-hidden hide-scrollbar gap-x-5 w-full scroll-smooth"
             style="scroll-behavior: smooth;">
            <div class="min-w-[180px] md:min-w-[230px] h-auto flex flex-col py-8 text-center">
                <div class="flex-grow py-4">
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                         لوازم اداری
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        انواع چسب
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        قیچی
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        پونز
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        منگنه
                    </p>
                    <p class="text-md lg:text-xl font-bold mb-2 text-white text-shadow-black text-shadow-sm ">
                        کاغذ
                    </p>
                </div>
                
                <div class="mt-auto pt-2 pb-3">
                    <a href="{{ route('category-page' , 'لوازم-اداری') }}" class="inline-flex items-center gap-1 text-sm font-bold  text-white group">
                        <span>مشاهده همه</span>
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @foreach($edari as $product)
                @if($product->variants->where('stock', '>', 0)->count() > 0)
                    @foreach($product->variants->where('stock', '>', 0) as $variant)
                        <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                            <a href="{{ route('product-page', array_filter([
                            'title' => $product->dashed_url,
                            'npi' => $product->id,
                            'nvi' => $variant->id
                        ])) }}"
                               wire:navigate
                               class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                                <div class="w-full relative overflow-hidden rounded-t-xl">
                                    <img class="w-full aspect-square hover:scale-125 transition-transform"
                                         src="{{ asset('storage/products/' . $product->id . '/small/' . $variant->id . '.webp') }}"
                                         alt="{{ $product->title }} - {{ $variant->name }}">
                                    @if($product->discounted_price)
                                        <div
                                            class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                            تخفیف ویژه!
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 py-3 text-center">
                                    <h5 class="text-md font-semibold mb-1">
                                        {{ english_to_persian_num($product->title) }}
                                    </h5>
                                    <p class="text-xs text-gray-500 mb-1">{{ $product->variant }}
                                        : {{ $variant->name }}</p>
                                    @if($product->discounted_price)
                                        <h5 class="text-xs text-gray-400 line-through">
                                            {{ english_to_persian_num(number_format($product->price)) }} تومان
                                        </h5>
                                        <h5 class="text-sm font-bold mt-1">
                                            {{ english_to_persian_num(number_format($product->discounted_price)) }}
                                            تومان
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
                @else
                    <div class="min-w-[180px] md:min-w-[230px] max-w-[180px] md:max-w-[230px] py-8">
                        <a href="{{ route('product-page', [
                        'title' => $product->dashed_url,
                        'npi' => $product->id
                    ]) }}"
                           wire:navigate
                           class="flex flex-col items-center shadow-md  shadow-gray-500 bg-white rounded-xl cursor-pointer h-full overflow-hidden">
                            <div class="w-full relative overflow-hidden rounded-t-xl">
                                <img class="w-full aspect-square hover:scale-125 transition-transform"
                                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                     alt="{{ $product->title }}">
                                @if($product->discounted_price)
                                    <div
                                        class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
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
                @endif
            @endforeach
        
        </div>
    </div>

</div>
