<div>
    <a href="{{ route('product-page' , ['title' => $product->dashed_title]) }}"
       class="flex flex-row sm:flex-col items-center bg-white rounded shadow hover:shadow-lg cursor-pointer h-full">

        <!-- عکس -->
        <div class="w-24 sm:w-full  overflow-hidden rounded-r-sm sm:rounded-t-sm sm:rounded-b-none">
            <img class="w-full aspect-square hover:scale-105 transition-all"
                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                 alt="">
        </div>

        <!-- متن -->
        <div class="flex-1 px-2 sm:px-0 sm:py-4 text-right sm:text-center">
            <h5 class="text-xs sm:text-lg font-bold">
                {{ english_to_persian_num($product->title) }}
            </h5>
            <h5 class="text-xs sm:text-sm mt-2">
                {{ english_to_persian_num(number_format($product->price)) }} تومان
            </h5>
        </div>
    </a>
</div>
