<div class="w-full">
    <div class="sm:flex flex-row-reverse w-full p-4  sm:p-8 rounded-2xl mx-auto mt-10 bg-pars-100 shadow">
        <div class="w-full sm:w-7/12 sm:mr-2">
            <h1 class="sm:hidden font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
            <div class="overflow-hidden h-[60vh] relative flex justify-center items-center"
                 id="zoomContainer-{{ $product->id }}">
                <img id="zoomImg-{{ $product->id }}" src="{{ $src }}"
                     class="h-[60vh] object-contain transition-transform duration-200 mx-auto">
            </div>

            <div class="flex flex-wrap justify-center mt-2">
                @foreach($images as $image)
                    <div>
                        <img wire:click.prevent="setImage('{{ $image }}')" wo
                             class="w-16 h-16 mx-1 cursor-pointer rounded hover:scale-105"
                             src="{{ asset('storage/products/' . $product->id. '/small/' . $image . '.webp') }}">
                        @if(\App\Models\ProductVariant::query()->find($image))
                            <div
                                class="text-center text-xs">{{ \App\Models\ProductVariant::query()->find($image)->name }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="w-full sm:w-5/12 sm:ml-2 mt-6 sm:mt-0">

            <h1 class="hidden sm:block font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
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
                <span class="font-bold">قیمت:</span>
                {{ english_to_persian_num(number_format($product->price)) }}
                تومان
            </h4>

            <h4 class="bg-pars-200 shadow rounded mb-3 p-1">
                <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
            </h4>
            @if($product->variant)
                <h4 class="bg-pars-200 shadow rounded mb-6 p-1">
                <span class="font-bold">
                   {{$product->variant}}
                </span>
                    <select
                        class="w-fit rounded-2xl border border-pars-500 py-0 px-4 focus:outline-none focus:ring-2 focus:ring-pars-500 focus:border-pars-500 cursor-pointer"
                        wire:model.live="selectedVariant">
                        <option value="">انتخاب کنید</option>
                        @foreach($product->variants as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}
                                @if($value->stock == 0)
                                    (اتمام موجودی)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('selectedVariant')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </h4>
            @endif
            @if((is_null($product->variant) && $product->stock > 0) ||
                    (!is_null($product->variant) && $product->variants->sum('stock') > 0))
                <div class="flex items-center gap-2">
                    <div class="flex">
                        <div wire:click.prevent="increase()"
                             class="w-10 h-10 flex items-center justify-center bg-pars-300 hover:bg-pars-400 rounded-r-2xl cursor-pointer select-none">
                            +
                        </div>
                        <input type="text"
                               wire:model.live="quantity"
                               oninput="this.value = this.value.replace(/[^0-9۰-۹]/g, ''); this.value = this.value.replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]); "
                               class="w-14 h-10 text-center border-t border-b border-pars-300 border-l-0 border-r-0 focus:outline-none"
                               value="{{ strtr($quantity, ['0' =>'۰' ,'1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}">

                        <div wire:click.prevent="decrease()"
                             class="w-10 h-10 flex items-center justify-center bg-pars-300 hover:bg-pars-400 rounded-l-2xl cursor-pointer select-none">
                            -
                        </div>
                    </div>
                    <button wire:click="addToCart()" wire:navigate wire:loading.attr="disabled"
                            class="flex items-center cursor-pointer justify-center bg-pars-500 text-white px-4 h-10 rounded shadow hover:bg-pars-600 transition-all">
                        افزودن به سبد خرید
                        <svg wire:loading wire:target="makePayment" class="w-5 h-5 ml-2 animate-spin text-white"
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


    {{-- <div class="w-full sm:w-11/12  p-4 rounded-2xl mx-auto mt-10 bg-pars-100 shadow min-h-24 mb-6">
         <small>طرح های مشابه</small>
         <div class="container mt-2">
             @foreach($similar as $value)
                 <a class="similar-item relative h-40"
                    href="{{ route('product-page' , ['title' => $value->dashed_title]) }}">
                     <img src="{{ $value->url }}" alt="" class="h-40 rounded">
                 </a>
             @endforeach
         </div>
     </div>--}}

    <script>
        document.addEventListener('livewire:navigated', () => {
            initZoom('{{ $product->id }}');
        });

        document.addEventListener('livewire:init', () => {
            initZoom('{{ $product->id }}');
        });

        function initZoom(productId) {
            const container = document.getElementById('zoomContainer-' + productId);
            const img = document.getElementById('zoomImg-' + productId);

            if (!container || !img) return;

            // حذف event listenerهای قبلی (اگر وجود دارن)
            container.removeEventListener('mousemove', container._zoomHandler);
            container.removeEventListener('mouseleave', container._resetHandler);

            function zoom(e) {
                const {left, top, width, height} = container.getBoundingClientRect();
                const x = ((e.clientX - left) / width) * 100;
                const y = ((e.clientY - top) / height) * 100;

                img.style.transformOrigin = `${x}% ${y}%`;
                img.style.transform = "scale(2)";
            }

            function resetZoom() {
                img.style.transformOrigin = "center center";
                img.style.transform = "scale(1)";
            }

            // ذخیره reference به توابع برای حذف بعدی
            container._zoomHandler = zoom;
            container._resetHandler = resetZoom;

            container.addEventListener('mousemove', zoom);
            container.addEventListener('mouseleave', resetZoom);
        }

        // مقداردهی اولیه
        initZoom('{{ $product->id }}');
    </script>
</div>
