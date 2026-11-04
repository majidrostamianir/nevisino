@push('variant')
    <link rel="canonical" href="{{ request()->getSchemeAndHttpHost() }}/product/{{ $npi }}/{{ $title }}" />
    <style>
        [x-cloak] { display: none !important; }

        @keyframes ping-gap {
            0%        { transform: scale(1); opacity: .6; }
            50%, 100% { transform: scale(2); opacity: 0; }
        }

        @keyframes shine-sweep {
            0%   { transform: translateX(100%);  }
            50%  { transform: translateX(-100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
@endpush

<div class="w-full"
     x-data="{
        selected: @entangle('selectedVariant'),
        isLoading: false,
        imageLoaded: true
     }"
     x-init="
        $watch('selected', (value) => {
            isLoading = true;
            imageLoaded = false;
        });">

    <div class="grid grid-cols-1 sm:grid-cols-12 sm:grid-rows-[max-content_max-content_1fr] gap-4 w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm">

        {{-- ========== ۱) اطلاعات محصول (بدون قیمت) ========== --}}
        <div class="order-1 sm:col-span-5 sm:col-start-1 sm:row-start-1">
            <h1 class="font-bold text-xl sm:text-2xl mb-6 text-gray-800">{{ english_to_persian_num($product->title) }}
                @if($product->code)
                    <span class="text-sm text-gray-500 font-light">{{ english_to_persian_num($product->code) }}</span>
                @endif
            </h1>

            {{-- breadcrumb --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <div class="inline-flex items-center gap-1 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <a href="{{ route('home') }}" class="hover:text-pars-600">نویسینو</a>
                    @if($parentCategory)
                        <span>/</span>
                        <a href="{{ '/category/' . $parentCategory->dashed_url }}" class="hover:text-pars-600">{{ $parentCategory->title }}</a>
                    @endif
                    @if($category)
                        <span>/</span>
                        <a href="{{ '/category/' . $category->dashed_url }}" class="hover:text-pars-600">{{ $category->title }}</a>
                    @endif
                </div>
            </div>

            {{-- code + category --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="font-bold">دسته بندی:</span>
                    @if($category)
                        <a href="{{ '/category/' . $category->dashed_url }}" class="hover:text-pars-600">
                            {{ $category->title }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- تضمین تطابق --}}
            <div class="order-1 sm:col-span-5 sm:col-start-1 sm:row-start-1">
                <div class="group relative overflow-hidden flex items-center gap-3 rounded-xl p-3.5 bg-gradient-to-l from-emerald-500/10 via-emerald-400/5 to-white border border-emerald-300/60 shadow-sm shadow-emerald-100/50">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-transparent via-white/60 to-transparent"
                         style="animation: shine-sweep 4s ease-out infinite;"></div>
                    <div class="relative flex-shrink-0">
                        <span class="absolute inset-0 rounded-full bg-emerald-400/40"
                              style="animation: ping-gap 2s cubic-bezier(0,0,.2,1) infinite;"></span>
                        <div class="absolute inset-0 rounded-full bg-emerald-400/40 blur-lg"></div>

                        <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-emerald-300 via-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-500/40">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-white/0 via-white/40 to-white/0 opacity-60"></div>
                            <svg class="relative w-7 h-7 text-white drop-shadow-md" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2.5l7 3v5.5c0 4.5-3 8.5-7 10-4-1.5-7-5.5-7-10V5.5l7-3z" fill="currentColor" fill-opacity="0.2"/>
                                <path d="M12 2.5l7 3v5.5c0 4.5-3 8.5-7 10-4-1.5-7-5.5-7-10V5.5l7-3z"/>
                                <path d="M9 12l2 2 4-4" stroke-width="2.5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 pr-3">
                        <p class="text-sm font-extrabold text-emerald-900 flex items-center gap-1.5 tracking-[0.18em]">
                            تضمین تطابق کالا
                        </p>
                        <p class="text-xs text-emerald-700/90 mt-0.5 leading-relaxed font-medium">محصول ارسالی دقیقاً مشابه
                            تصاویر می‌باشد</p>
                    </div>
                    <div class="hidden sm:flex items-center justify-center flex-shrink-0 px-5 py-1.5 rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-400/40">
                        <span class="text-sm font-extrabold leading-none">۱۰۰٪</span>
                        <span class="text-[10px] leading-none pr-2 opacity-90">مطابق تصاویر</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== ۲) تصویر + تامبنیل ========== --}}
        <div class="order-2 sm:col-span-7 sm:col-start-6 sm:row-start-1 sm:row-span-3">
            <div class="overflow-hidden h-[60vh] relative flex justify-center items-center bg-gray-50 rounded-2xl"
                 x-data="{
                    isZoomed: false,
                    zoomOnHover: false,
                    originX: '50%',
                    originY: '50%',
                    lastTap: 0,
                    init() { this.zoomOnHover = window.matchMedia('(hover: hover)').matches; },
                    setZoomOrigin(event) {
                        const rect = this.$el.getBoundingClientRect();
                        let clientX, clientY;
                        if (event.clientX && event.clientY) { clientX = event.clientX; clientY = event.clientY; }
                        else if (event.touches && event.touches[0]) { clientX = event.touches[0].clientX; clientY = event.touches[0].clientY; }
                        else if (event.changedTouches && event.changedTouches[0]) { clientX = event.changedTouches[0].clientX; clientY = event.changedTouches[0].clientY; }
                        else { return; }
                        const x = ((clientX - rect.left) / rect.width) * 100;
                        const y = ((clientY - rect.top) / rect.height) * 100;
                        this.originX = `${Math.max(0, Math.min(100, x))}%`;
                        this.originY = `${Math.max(0, Math.min(100, y))}%`;
                    },
                    handleMobileAction(event) {
                        if (this.zoomOnHover) return;
                        const currentTime = new Date().getTime();
                        const tapLength = currentTime - this.lastTap;
                        this.setZoomOrigin(event);
                        if (tapLength < 500 && this.isZoomed) { this.isZoomed = false; this.lastTap = 0; }
                        else { this.isZoomed = !this.isZoomed; this.lastTap = currentTime; }
                    },
                    resetZoomState() { this.isZoomed = false; this.originX = '50%'; this.originY = '50%'; this.lastTap = 0; },
                    handleMouseEnter() { if (this.zoomOnHover) this.isZoomed = true; },
                    handleMouseMove(event) { if (this.zoomOnHover && this.isZoomed) this.setZoomOrigin(event); },
                    handleMouseLeave() { if (this.zoomOnHover) this.isZoomed = false; }
                }"
                 @mouseenter="handleMouseEnter()"
                 @mousemove="handleMouseMove($event)"
                 @mouseleave="handleMouseLeave()"
                 @click="handleMobileAction($event)"
                 :class="{ 'cursor-zoom-in': zoomOnHover && !isZoomed, 'cursor-pointer': !zoomOnHover }"
                 style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">

                <img src="{{ $src }}"
                     x-bind:src="$wire.src"
                     x-on:load="imageLoaded = true; isLoading = false;"
                     x-on:error="imageLoaded = true; isLoading = false;"
                     wire:loading.remove
                     wire:target="setImage"
                     alt="{{ english_to_persian_num($product->title) }}"
                     :class="{ 'scale-200': isZoomed, 'scale-100': !isZoomed }"
                     :style="`transform-origin: ${originX} ${originY};`"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     class="h-[60vh] object-contain transition-transform duration-200 mx-auto select-none rounded-2xl">

                <div class="absolute inset-0 w-full h-full" x-show="isLoading">
                    <div class="w-full h-full rounded-2xl flex items-center justify-center backdrop-blur-sm z-50 transition-opacity duration-300">
                        <div class="w-10 h-10 border-4 border-pars-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
                <div class="absolute inset-0 w-full h-full" wire:loading wire:target="setImage">
                    <div class="w-full h-full rounded-2xl flex items-center justify-center backdrop-blur-sm z-50 transition-opacity duration-300">
                        <div class="w-10 h-10 border-4 border-pars-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>

            {{-- thumbnail --}}
            <div class="flex flex-wrap justify-center mt-4">
                @foreach($images as $image)
                    @php
                        $variantName = $variantNameMap[$image] ?? null;
                        $altText     = english_to_persian_num($product->title) . ($variantName ? ' - ' . english_to_persian_num($variantName) : '');
                    @endphp
                    <div>
                        <img
                                @click="if (selected != '{{ $image }}') { selected = '{{ $image }}'; $wire.setImage('{{ $image }}'); }"
                                class="w-16 h-16 cursor-pointer rounded transition-all border-2 border-transparent mx-2"
                                :class="{ 'border-pars-600 ring-2 ring-offset-2 ring-pars-600': selected == '{{ $image }}' }"
                                src="{{ asset('storage/products/' . $product->id . '/small/' . $image . '.webp') }}"
                                alt="{{ $altText }}">
                        @if($variantName)
                            <div :class="{ 'font-bold text-pars-600': selected == '{{ $image }}' }"
                                 class="text-center mt-1 text-xs transition-all">{{ english_to_persian_num($variantName) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========== ۳) قیمت ========== --}}
        <div class="order-4 sm:col-span-5 sm:col-start-1 sm:row-start-2 sm:self-start">
            @if($inStock)
                <div class="flex items-center gap-2 text-lg bg-gray-50 px-3 py-2 rounded-lg">
                    <span class="font-bold">قیمت:</span>
                    @if($hasDiscount)
                        <span class="line-through text-gray-400 text-sm">{{ english_to_persian_num(number_format($basePrice)) }}</span>
                        <span class="font-bold text-pars-700 text-lg">{{ english_to_persian_num(number_format($finalPrice)) }}</span>
                    @else
                        <span class="font-bold text-pars-700 text-lg">{{ english_to_persian_num(number_format($finalPrice)) }}</span>
                    @endif
                    <span>تومان</span>
                </div>
            @else
                <div class="text-center bg-red-50 border border-red-200 rounded-xl p-3">
                    <span class="text-red-500 font-bold text-sm">اتمام موجودی</span>
                </div>
            @endif
        </div>

        {{-- ========== ۴) واریانت + تعداد + دکمه + خرید متقابل ========== --}}
        @if($inStock)
            <div class="contents sm:block sm:col-span-5 sm:col-start-1 sm:row-start-3">

                {{-- واریانت --}}
                @if($product->variant)
                    <div class="order-3 p-1 sm:mb-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-gray-700 text-sm whitespace-nowrap">
                                {{ $product->variant }}:
                            </span>

                            <div @click="if (selected != 1) { selected = 1; $wire.setImage('1'); }"
                                 class="relative px-4 py-1.5 rounded-xl border select-none transition-all duration-150 text-sm cursor-pointer"
                                 :class="{
                                    'border-pars-500 bg-pars-500 text-white font-bold shadow-md': selected < 1000,
                                    'border-gray-300 hover:border-pars-400 bg-white text-gray-700': selected > 1000
                                 }">
                                <span>به انتخاب نویسینو</span>
                            </div>

                            @foreach($product->variants as $value)
                                <div @click="if (selected != '{{ $value->id }}') { selected = '{{ $value->id }}'; $wire.setImage('{{ $value->id }}'); }"
                                     class="relative px-4 py-1.5 rounded-xl border select-none transition-all duration-150 text-sm"
                                     :class="{
                                        'border-pars-500 bg-pars-500 text-white font-bold shadow-md': selected == '{{ $value->id }}',
                                        'border-gray-300 hover:border-pars-400 bg-white text-gray-700 cursor-pointer': selected != '{{ $value->id }}' && {{ $value->stock }} > 0,
                                        'opacity-50 cursor-not-allowed border-gray-200 bg-gray-100': {{ $value->stock }} == 0
                                     }">
                                    <span>{{ english_to_persian_num($value->name) }}</span>
                                    @if($value->stock == 0)
                                        <span class="mr-1 text-xs text-red-500">(ناموجود)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @error('selectedVariant')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                {{-- تعداد و دکمه افزودن به سبد --}}
                <div class="order-5 flex items-center gap-3">
                    <div class="flex relative">
                        <svg wire:loading wire:target="increase , decrease"
                             class="absolute top-1.5 right-12 w-5 h-5 mr-2 animate-spin text-pars-500"
                             fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z"></path>
                        </svg>

                        @if($stock == 0)
                            <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">ناموجود</span>
                        @elseif(persian_to_english_num($quantity) >= $stock)
                            @if($selectedVariantName)
                                <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">تنها {{ english_to_persian_num($stock) }} عدد از {{ $product->variant }} {{ english_to_persian_num($selectedVariantName) }} باقی مانده است</span>
                            @else
                                <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">موجودی انبار {{ english_to_persian_num($stock) }} عدد است</span>
                            @endif
                        @endif

                        <div wire:click.prevent="increase()"
                             class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-r-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
                            +
                        </div>
                        <input type="text"
                               wire:model.live="quantity"
                               oninput="this.value = this.value.replace(/[^0-9۰-۹]/g, ''); this.value = this.value.replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);"
                               class="w-14 h-10 text-center border-t border-b border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-pars-500 text-gray-800"
                               value="{{ $quantity }}">
                        <div wire:click.prevent="decrease()"
                             class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-l-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
                            -
                        </div>
                    </div>

                    <button wire:click="addToCart()" wire:navigate wire:loading.attr="disabled"
                            class="relative flex items-center cursor-pointer justify-center bg-pars-500 text-sm text-white px-6 h-10 rounded-xl shadow-sm hover:bg-pars-600 hover:shadow-md transition-all duration-200 font-medium">
                        افزودن به سبد خرید
                        <svg wire:loading wire:target="addToCart"
                             class="w-5 h-5 absolute left-2 animate-spin text-white"
                             fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z"></path>
                        </svg>
                    </button>
                </div>

                {{-- خرید متقابل (نقدی/اقساطی) --}}
                <div class="order-6 mt-3 sm:mt-5" x-data="{ open: false }">
                    <div class="relative overflow-hidden rounded-2xl border {{ $crossTheme['border'] }} {{ $crossTheme['bg'] }} p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-11 h-11 rounded-xl {{ $crossTheme['iconBg'] }} flex items-center justify-center shadow-md">
                                @if($crossTheme['icon'] === 'cash')
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-extrabold {{ $crossTheme['textDark'] }}">{{ $crossTitle }}</p>
                                <p class="text-xs {{ $crossTheme['textSoft'] }} mt-1 leading-relaxed">{{ $crossDesc }}</p>
                            </div>
                        </div>

                        <button type="button"
                                @click="open = true"
                                class="cursor-pointer mt-3 w-full h-10 {{ $crossTheme['btn'] }} text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                            <span>{{ $crossLabel }}</span>
                        </button>
                    </div>

                    {{-- دیالوگ تایید انتقال --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click.self="open = false"
                         @keydown.escape.window="open = false"
                         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="p-6 text-center">
                                <div class="mx-auto w-14 h-14 rounded-full flex items-center justify-center mb-4">
                                    <div class="w-12 h-12 rounded-full {{ $crossTheme['iconBg'] }} flex items-center justify-center shadow-lg">
                                        @if($crossTheme['icon'] === 'cash')
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                {{-- اینجا از رنگ تم برای تیتر استفاده شده --}}
                                <h3 class="text-lg font-bold {{ $crossTheme['textDark'] }} mb-2">{{ $crossTitle }}</h3>
                                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $crossDesc }}</p>
                                <p class="text-[10px] text-gray-400 mb-5 px-2 py-2 bg-gray-50 rounded-lg font-mono text-justify">{{ $msg }}</p>

                                <div class="flex gap-2">
                                    <button type="button" @click="open = false"
                                            class="flex-1 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                                        انصراف
                                    </button>
                                    <a href="{{ $crossUrl }}"
                                       class="flex-1 h-10 rounded-xl {{ $crossTheme['btn'] }} text-white text-sm font-bold shadow-sm transition-colors flex items-center justify-center">
                                        تایید و انتقال
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- توضیحات محصول --}}
    @if($product->description)
        <div class="w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm mt-4">
            <div class="font-bold mb-4 text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                معرفی محصول
            </div>
            <div class="text-justify text-gray-600 leading-relaxed">
                {!! english_to_persian_num($product->description) !!}
            </div>
        </div>
    @endif

    @if(!empty($displayAttributes))
        <div class="w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm mt-4">
            <div class="font-bold mb-4 text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ویژگی‌ها
            </div>
            <div class="rounded-xl overflow-hidden border border-gray-100">
                @foreach($displayAttributes as $attr)
                    <div class="flex text-sm">
                        <div class="text-gray-700 w-1/2 md:w-1/3 p-2.5 pr-4 border-b border-gray-100 bg-gray-50 font-medium">{{ english_to_persian_num($attr['name']) }}</div>
                        <div class="text-gray-800 w-1/2 md:w-2/3 p-2.5 pr-4 border-b border-gray-100 bg-white">{{ english_to_persian_num($attr['value']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- محصولات مرتبط --}}
    @if($relatedProducts->isNotEmpty())
        <div class="w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm mt-4">
            <div class="font-bold mb-4 text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                کالاهای مرتبط
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4" wire:ignore>
                @foreach($relatedProducts as $relate)
                    <livewire:components.product-card :product="$relate"/>
                @endforeach
            </div>
        </div>
    @endif
</div>