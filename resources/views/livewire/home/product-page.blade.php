@push('variant')
    <link rel="canonical" href="https://nevisino.ir/product/{{ $npi }}/{{ $title }}" />
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

    <div class="sm:flex flex-row-reverse w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm">
        <div class="w-full sm:w-7/12 sm:mr-2">
            <div class="sm:hidden">
                <h1 class="font-bold text-xl mb-6 text-gray-800">{{ english_to_persian_num($product->title) }}</h1>

                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @php
                        $category = \App\Models\Category::query()->find($product->category_id);
                        $parent = \App\Models\Category::query()->find($category->parent_id);
                    @endphp
                    <div class="inline-flex items-center gap-1 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <a href="{{ route('home') }}" class="hover:text-pars-600">نویسینو</a>
                        @if($parent)
                            <span>/</span>
                            <a href="{{ '/category/' . $parent->dashed_url }}" class="hover:text-pars-600">{{ $parent->title }}</a>
                        @endif
                        @if($category)
                            <span>/</span>
                            <a href="{{ '/category/' . $category->dashed_url }}" class="hover:text-pars-600">{{ $category->title }}</a>
                        @endif
                    </div>
                </div>

                @if($product->code)
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg mb-3">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        <span class="font-bold">کد محصول:</span>
                        <span>{{ english_to_persian_num($product->code) }}</span>
                    </div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg mb-3">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="font-bold">دسته بندی:</span>
                    <a href="{{ '/category/' . \App\Models\Category::query()->find($product->category_id)->dashed_url }}" class="hover:text-pars-600">
                        {{ \App\Models\Category::query()->find($product->category_id)->title }}
                    </a>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-green-50 px-3 py-1.5 rounded-lg mb-3">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
                </div>
                {{-- فقط در صورتی قیمت و اطلاعات رو نشون بده که موجودی داشته باشه --}}
                @if((is_null($product->variant) && $product->stock > 0) || (!is_null($product->variant) && $product->variants->sum('stock') > 0))
                    <div class="flex items-center gap-2 text-lg bg-gray-50 px-3 py-2 rounded-lg mb-8">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold">قیمت:</span>
                        @if($product->discounted_price)
                            <span class="line-through text-gray-400 text-sm">{{ english_to_persian_num(number_format($product->price)) }}</span>
                            <span class="font-bold text-pars-700 text-lg">{{ english_to_persian_num(number_format($product->discounted_price)) }}</span>
                        @else
                            <span class="font-bold text-pars-700 text-lg">{{ english_to_persian_num(number_format($product->price)) }}</span>
                        @endif
                        <span>تومان</span>
                    </div>
                @else
                    {{-- محصول ناموجود - فقط پیام اتمام موجودی --}}
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-8 text-center">
                        <span class="text-red-500 font-bold text-sm">اتمام موجودی</span>
                    </div>
                @endif
            </div>

            {{-- تصویر اصلی با زوم --}}
            <div class="overflow-hidden h-[60vh] relative flex justify-center items-center bg-gray-50 rounded-2xl"
                 x-data="{
                        isZoomed: false,
                        zoomOnHover: false,
                        originX: '50%',
                        originY: '50%',
                        lastTap: 0,

                        init() {
                            this.zoomOnHover = window.matchMedia('(hover: hover)').matches;
                        },

                        setZoomOrigin(event) {
                            const rect = this.$el.getBoundingClientRect();
                            let clientX, clientY;

                            if (event.clientX && event.clientY) {
                                clientX = event.clientX;
                                clientY = event.clientY;
                            } else if (event.touches && event.touches[0]) {
                                clientX = event.touches[0].clientX;
                                clientY = event.touches[0].clientY;
                            } else if (event.changedTouches && event.changedTouches[0]) {
                                clientX = event.changedTouches[0].clientX;
                                clientY = event.changedTouches[0].clientY;
                            } else {
                                return;
                            }

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
                            if (tapLength < 500 && this.isZoomed) {
                                this.isZoomed = false;
                                this.lastTap = 0;
                            } else {
                                this.isZoomed = !this.isZoomed;
                                this.lastTap = currentTime;
                            }
                        },

                        resetZoomState() {
                            this.isZoomed = false;
                            this.originX = '50%';
                            this.originY = '50%';
                            this.lastTap = 0;
                        },

                        handleMouseEnter() {
                            if (this.zoomOnHover) this.isZoomed = true;
                        },

                        handleMouseMove(event) {
                            if (this.zoomOnHover && this.isZoomed) {
                                this.setZoomOrigin(event);
                            }
                        },

                        handleMouseLeave() {
                            if (this.zoomOnHover) this.isZoomed = false;
                        }
                    }"
                 @mouseenter="handleMouseEnter()"
                 @mousemove="handleMouseMove($event)"
                 @mouseleave="handleMouseLeave()"
                 @click="handleMobileAction($event)"
                 :class="{
                    'cursor-zoom-in': zoomOnHover && !isZoomed,
                    'cursor-pointer': !zoomOnHover
                    }"
                 style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">

                <img src="{{ $src }}"
                     x-bind:src="$wire.src"
                     x-on:load="imageLoaded = true; isLoading = false;"
                     x-on:error="imageLoaded = true; isLoading = false;"
                     wire:loading.remove
                     wire:target="setImage"
                     :class="{
                        'scale-200': isZoomed,
                        'scale-100': !isZoomed
                    }"
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
            <div class="flex flex-wrap justify-center mt-4">
                @foreach($images as $image)
                    <div>
                        <img
                            @click="if (selected != '{{ $image }}') { selected = '{{ $image }}'; $wire.setImage('{{ $image }}'); }"
                            class="w-16 h-16 cursor-pointer rounded transition-all border-2 border-transparent mx-2"
                            :class="{
                                'border-pars-600 ring-2 ring-offset-2 ring-pars-600': selected == '{{ $image }}'
                            }"
                             src="{{ asset('storage/products/' . $product->id. '/small/' . $image . '.webp') }}">
                        @if(\App\Models\ProductVariant::query()->find($image))
                            <div
                                :class="{
                                 'font-bold text-pars-600': selected == '{{ $image }}'
                             }"
                                 class="text-center mt-1 text-xs transition-all">{{ english_to_persian_num(\App\Models\ProductVariant::query()->find($image)->name) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- سمت چپ (اطلاعات و خرید) --}}
        <div class="w-full sm:w-5/12 sm:ml-2 mt-6 sm:mt-0">
            <div class="hidden sm:block">
                <h1 class="font-bold text-2xl mb-6 text-gray-800">{{ english_to_persian_num($product->title) }}</h1>

                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @php
                        $category = \App\Models\Category::query()->find($product->category_id);
                        $parent = \App\Models\Category::query()->find($category->parent_id);
                    @endphp
                    <div class="inline-flex items-center gap-1 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <a href="{{ route('home') }}" class="hover:text-pars-600">نویسینو</a>
                        @if($parent)
                            <span>/</span>
                            <a href="{{ '/category/' . $parent->dashed_url }}" class="hover:text-pars-600">{{ $parent->title }}</a>
                        @endif
                        @if($category)
                            <span>/</span>
                            <a href="{{ '/category/' . $category->dashed_url }}" class="hover:text-pars-600">{{ $category->title }}</a>
                        @endif
                    </div>
                </div>

                @if($product->code)
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg mb-3">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        <span class="font-bold">کد محصول:</span>
                        <span>{{ english_to_persian_num($product->code) }}</span>
                    </div>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg mb-3">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="font-bold">دسته بندی:</span>
                    <a href="{{ '/category/' . \App\Models\Category::query()->find($product->category_id)->dashed_url }}" class="hover:text-pars-600">
                        {{ \App\Models\Category::query()->find($product->category_id)->title }}
                    </a>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-green-50 px-3 py-1.5 rounded-lg mb-3">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
                </div>

                {{-- فقط در صورتی قیمت و اطلاعات رو نشون بده که موجودی داشته باشه --}}
                @if((is_null($product->variant) && $product->stock > 0) || (!is_null($product->variant) && $product->variants->sum('stock') > 0))

                    <div class="flex items-center gap-2 text-lg bg-gray-50 px-3 py-2 rounded-lg mb-8">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold">قیمت:</span>
                        @if($product->discounted_price)
                            <span class="line-through text-gray-400 text-sm">{{ english_to_persian_num(number_format($product->price)) }}</span>
                            <span class="font-bold text-pars-700 text-2xl">{{ english_to_persian_num(number_format($product->discounted_price)) }}</span>
                        @else
                            <span class="font-bold text-pars-700 text-2xl">{{ english_to_persian_num(number_format($product->price)) }}</span>
                        @endif
                        <span>تومان</span>
                    </div>
                @endif
            </div>

            {{-- انتخاب واریانت --}}
            @if((is_null($product->variant) && $product->stock > 0) || (!is_null($product->variant) && $product->variants->sum('stock') > 0))
                @if($product->variant)
                    <div class="mb-4 p-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-bold text-gray-700 text-sm">{{ $product->variant }}:</span>
                            <div class="flex flex-wrap gap-2">
                                <div @click="if (selected != 1) { selected = 1 ; $wire.setImage('1'); }"
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
                        </div>
                        @error('selectedVariant')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                {{-- تعداد و دکمه افزودن به سبد --}}
                <div class="flex items-center gap-3">
                    <div class="flex relative">
                        <svg wire:loading wire:target="increase , decrease"
                             class="absolute top-1.5 right-12 w-5 h-5 mr-2 animate-spin text-pars-500"
                             fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z"></path>
                        </svg>

                        @if($stock == 0)
                            <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">ناموجود</span>
                        @elseif(persian_to_english_num($this->quantity) >= $stock)
                            @if($product->variant === null || $selectedVariant < 1000)
                                <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">موجودی انبار {{ english_to_persian_num($stock) }} عدد است</span>
                            @else
                                <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">تنها {{ english_to_persian_num($stock) }} عدد از {{ $product->variant }} {{ english_to_persian_num(\App\Models\ProductVariant::find($selectedVariant)->name) }} باقی مانده است</span>
                            @endif
                        @endif

                        <div wire:click.prevent="increase()"
                             class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-r-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
                            +
                        </div>
                        <input type="text"
                               wire:model.live="quantity"
                               oninput="this.value = this.value.replace(/[^0-9۰-۹]/g, ''); this.value = this.value.replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]); "
                               class="w-14 h-10 text-center border-t border-b border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-pars-500 text-gray-800"
                               value="{{ strtr($quantity, ['0' =>'۰' ,'1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}">
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
            @else
                <div class="text-center bg-red-50 border border-red-200 rounded-xl p-4">
                    <span class="text-red-500 font-bold">اتمام موجودی</span>
                </div>
            @endif
        </div>
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
                {{ english_to_persian_num($product->description) }}
            </div>
        </div>
    @endif

    {{-- ویژگی‌ها --}}
    @if($product->attrs()->exists())
        <div class="w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm mt-4">
            <div class="font-bold mb-4 text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                ویژگی‌ها
            </div>
            <div class="rounded-xl overflow-hidden border border-gray-100">
                @foreach($product->attrs()->get()->sortBy('title') as $attr)
                    <div class="flex text-sm">
                        <div class="text-gray-700 w-1/2 md:w-1/3 p-2.5 pr-4 border-b border-gray-100 bg-gray-50 font-medium">{{ english_to_persian_num($attr->title) }}</div>
                        <div class="text-gray-800 w-1/2 md:w-2/3 p-2.5 pr-4 border-b border-gray-100 bg-white">{{ english_to_persian_num($attr->value) }}</div>
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