@push('torob-meta-tags')
    <meta name="product_id" content="{{ $product->id }}">
    @php
        $variantPrice = $product->variant === null ? null : $product->variants->min('price');
        $finalPrice = ($product->variant === null || $variantPrice == 0 || $variantPrice === null)
            ? $product->price
            : $variantPrice;
    @endphp
    <meta name="product_price" content="{{ $finalPrice }}">
    @php
        $totalStock = $product->variant === null
            ? $product->stock
            : $product->variants->sum('stock');
    @endphp
    <meta name="availability" content="{{ $totalStock > 0 ? 'instock' : 'outofstock' }}">
    <meta name="product_name" content="{{ $product->title }}">
    <meta property="og:image" content="{{ $src }}">
    <meta name="guarantee" content="{{ $product->guarantee ?? 'گارانتی اصالت و سلامت فیزیکی کالا' }}">
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
    <div class="sm:flex flex-row-reverse w-full p-4 pb-8  sm:p-8 rounded-2xl mx-auto mt-10 bg-pars-100 shadow">
        <div class="w-full sm:w-7/12 sm:mr-2">
            <div class="sm:hidden">
                <h1 class=" font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1">
                    @php
                        $category = \App\Models\Category::query()->find($product->category_id);
                        $parent = \App\Models\Category::query()->find($category->parent_id);
                    @endphp

                    <a href="{{ route('home') }}">نویسینو</a>

                    @if($parent)
                        / <a href="{{ '/category/' . $parent->dashed_title }}">{{ $parent->title }}</a>
                    @endif

                    @if($category)
                        / <a href="{{ '/category/' . $category->dashed_title }}">{{ $category->title }}</a>
                    @endif

                </h4>
                @if($product->code)
                    <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span
                            class="font-bold">کد محصول:</span> {{ english_to_persian_num($product->code) }}</h4>
                @endif
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span class="font-bold">دسته بندی: </span><a
                        href="{{ '/category/' . \App\Models\Category::query()->find($product->category_id)->dashed_title }}">{{ \App\Models\Category::query()->find($product->category_id)->title }}</a>
                </h4>
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1">
                    <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
                </h4>
                <h4 class="bg-pars-200 shadow rounded mb-8 p-1">
                    <span class="font-bold">قیمت:</span>
                    {{ english_to_persian_num(number_format($product->price)) }}
                    تومان
                </h4>
            </div>
            <div class="overflow-hidden h-[60vh] relative flex justify-center items-center">

                <img src="{{ $src }}"
                     x-bind:src="$wire.src"
                     x-on:load="imageLoaded = true; isLoading = false;"
                     x-on:error="imageLoaded = true; isLoading = false;"
                     wire:loading.remove
                     wire:target="setImage"
                     class="h-[60vh] object-contain transition-transform duration-200 mx-auto select-none rounded-2xl">
                <div class="absolute inset-0 w-full h-full" x-show="isLoading">
                    <div
                        class="w-full h-full rounded-2xl flex items-center justify-center backdrop-blur-sm z-50 transition-opacity duration-300">
                        <div
                            class="w-10 h-10 border-4 border-pars-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
                <div class="absolute inset-0 w-full h-full" wire:loading wire:target="setImage">
                    <div
                        class="w-full h-full rounded-2xl flex items-center justify-center backdrop-blur-sm z-50 transition-opacity duration-300">
                        <div
                            class="w-10 h-10 border-4 border-pars-500 border-t-transparent rounded-full animate-spin"></div>
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
                                class="text-center mt-1 text-xs transition-all">{{ \App\Models\ProductVariant::query()->find($image)->name }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="w-full sm:w-5/12 sm:ml-2 mt-6 sm:mt-0">
            <div class="hidden sm:block">
                <h1 class=" font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1">
                    @php
                        $category = \App\Models\Category::query()->find($product->category_id);
                        $parent = \App\Models\Category::query()->find($category->parent_id);
                    @endphp

                    <a href="{{ route('home') }}">نویسینو</a>

                    @if($parent)
                        / <a href="{{ '/category/' . $parent->dashed_title }}">{{ $parent->title }}</a>
                    @endif

                    @if($category)
                        / <a href="{{ '/category/' . $category->dashed_title }}">{{ $category->title }}</a>
                    @endif

                </h4>
                @if($product->code)
                    <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span
                            class="font-bold">کد محصول:</span> {{ english_to_persian_num($product->code) }}</h4>
                @endif
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span class="font-bold">دسته بندی: </span><a
                        href="{{ '/category/' . \App\Models\Category::query()->find($product->category_id)->dashed_title }}">{{ \App\Models\Category::query()->find($product->category_id)->title }}</a>
                </h4>
                <h4 class="bg-pars-200 shadow rounded mb-3 p-1">
                    <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
                </h4>
                <h4 class="bg-pars-200 shadow rounded mb-8 p-1">
                    <span class="font-bold">قیمت:</span>
                    {{ english_to_persian_num(number_format($product->price)) }}
                    تومان
                </h4>
            </div>

            @if((is_null($product->variant) && $product->stock > 0) ||
                    (!is_null($product->variant) && $product->variants->sum('stock') > 0))
                @if($product->variant)
                    <h4 class="mb-3 p-1">
                        <div class="flex flex-wrap gap-2 ">
                        <span class="font-bold">
                              انتخاب {{$product->variant}}:
                        </span>
                            <div
                                @click="if (selected != 1) { selected = 1 ; $wire.setImage('1'); }"
                                class="relative px-4 rounded border border-gray-400  select-none transition-all duration-150"
                                :class="{
                                    'border-pars-500  bg-pars-500 text-white font-bold shadow': selected < 1000,
                                    'border-gray-400 cursor-pointer hover:border-pars-500': selected > 1000
                                }">
                                <span class="">به انتخاب نویسینو</span>
                                {{--                            <template x-if="selected < 1000">--}}
                                {{--                                <svg class="absolute top-1 right-1 w-4 h-4 text-pars-500" fill="none"--}}
                                {{--                                     viewBox="0 0 24 24" stroke="currentColor">--}}
                                {{--                                    <path stroke-linecap="round" stroke-linejssoin="round" stroke-width="3"--}}
                                {{--                                          d="M5 13l4 4L19 7"/>--}}
                                {{--                                </svg>--}}
                                {{--                            </template>--}}
                            </div>
                            @foreach($product->variants as $value)
                                <div
                                    @click="if (selected != '{{ $value->id }}') { selected = '{{ $value->id }}'; $wire.setImage('{{ $value->id }}'); }"
                                    class="relative px-4 rounded border border-gray-400  select-none transition-all duration-150"
                                    :class="{
                                    'border-pars-500  bg-pars-500 text-white font-bold shadow': selected == '{{ $value->id }}',
                                    'border-gray-400 cursor-pointer hover:border-pars-500': selected != '{{ $value->id }}' && {{ $value->stock }} > 0,
                                    'opacity-50 cursor-not-allowed border-gray-400': {{ $value->stock }} == 0
                                }">
                                    <span class="">{{ $value->name }}</span>
                                    @if($value->stock == 0)
                                        <span class="text-sm text-red-500">ناموجود</span>
                                    @endif
                                    {{--                                <template x-if="selected == '{{ $value->id }}'">--}}
                                    {{--                                    <svg class="absolute top-1 right-1 w-4 h-4 text-white" fill="none"--}}
                                    {{--                                         viewBox="0 0 24 24" stroke="currentColor">--}}
                                    {{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"--}}
                                    {{--                                              d="M5 13l4 4L19 7"/>--}}
                                    {{--                                    </svg>--}}
                                    {{--                                </template>--}}
                                </div>
                            @endforeach
                        </div>
                        @error('selectedVariant')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </h4>
                @endif
                <div class="flex items-center gap-2">
                    <div class="flex relative">
                        <svg wire:loading wire:target="increase , decrease"
                             class="absolute top-1.5 right-12 w-5 h-5 mr-2 animate-spin text-pars-500"
                             fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z">
                            </path>
                        </svg>
                        @if($stock == 0)
                            <span class="absolute -bottom-6 right-8 text-xs text-red-500">ناموجود</span>
                        @elseif(persian_to_english_num( $this->quantity)  >=  $stock)
                            <span class="absolute -bottom-6 right-8 text-xs text-red-500">حداکثر {{ english_to_persian_num($stock) }} عدد</span>
                        @endif

                        <div wire:click.prevent="increase()"
                             class="w-10 h-8 flex items-center justify-center bg-pars-300 hover:bg-pars-400 active:bg-pars-500 active:text-white rounded-r-2xl cursor-pointer select-none">
                            +
                        </div>
                        <input type="text"
                               wire:model.live="quantity"
                               oninput="this.value = this.value.replace(/[^0-9۰-۹]/g, ''); this.value = this.value.replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]); "
                               class="w-14 h-8 text-center border-t border-b border-pars-300 border-l-0 border-r-0 focus:outline-none"
                               value="{{ strtr($quantity, ['0' =>'۰' ,'1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}">
                        <div wire:click.prevent="decrease()"
                             class="w-10 h-8 flex items-center justify-center bg-pars-300 hover:bg-pars-400 active:bg-pars-500 active:text-white rounded-l-2xl cursor-pointer select-none">
                            -
                        </div>
                    </div>
                    <button wire:click="addToCart()" wire:navigate wire:loading.attr="disabled"
                            class="relative flex items-center cursor-pointer justify-center bg-pars-500 text-xs sm:text-sm text-white px-6 h-8 rounded shadow hover:bg-pars-600 transition-all">
                        افزودن به سبد خرید
                        <svg wire:loading wire:target="addToCart"
                             class="w-5 h-5 absolute left-0.5 animate-spin text-white"
                             fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z">
                            </path>
                        </svg>
                    </button>
                </div>
            @else
                <h4 class="text-red-500 text-xl text-center bg-pars-200 shadow rounded mb-3 p-1">اتمام موجودی</h4>
            @endif
        </div>

    </div>
</div>
