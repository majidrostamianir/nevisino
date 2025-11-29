<div>
    <a href="{{ route('product-page' , ['title' => $product->dashed_title]) }}" wire:navigate
       class="flex flex-col p-1.5 items-center bg-white rounded shadow hover:shadow-lg cursor-pointer h-full">

        <!-- عکس -->
        <div class="w-full relative overflow-hidden rounded-xs">

            @if((is_null($product->variant) && $product->stock == 0) ||
                                (!is_null($product->variant) && $product->variants->sum('stock') == 0))
                <img class="w-full aspect-square hover:scale-105 transition-all grayscale"
                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                     alt="">
                <div class="absolute top-3 -left-15 rotate-[-45deg] bg-red-600 text-white text-xs sm:text-sm sm:font-bold px-16 py-1 shadow-md">
                    ناموجود
                </div>
            @else
                <img class="w-full aspect-square hover:scale-105 transition-all "
                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                     alt="">
                @if($product->discounted_price)
                    <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                        تخفیف ویژه!
                    </div>
                @endif
            @endif


        </div>

        <!-- متن -->
        <div class="flex-1 px-0 py-4  text-center">
            <h5 class="text-xs sm:text-md lg:text-lg">
                {{ english_to_persian_num($product->title) }}
            </h5>
            @if($product->discounted_price)
                <h5 class="text-xs sm:text-sm mt-2 line-through text-gray-400">
                    {{ english_to_persian_num(number_format($product->price)) }} تومان
                </h5>
                <h5 class="text-xs sm:text-sm mt-1 ">
                    {{ english_to_persian_num(number_format($product->discounted_price)) }} تومان
                </h5>
            @else
                <h5 class="text-xs sm:text-sm mt-2">
                    {{ english_to_persian_num(number_format($product->price)) }} تومان
                </h5>
            @endif
        </div>
    </a>
</div>
