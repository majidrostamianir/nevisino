<div>
    <a href="{{ route('product-page' , ['title' => $product->dashed_url , 'npi' => $product->id ]) }}" wire:navigate
       class="group flex flex-col items-center bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 overflow-hidden h-full">

        <!-- عکس -->
        <div class="w-full relative overflow-hidden bg-gray-50">
            @if((is_null($product->variant) && $product->stock == 0) || (!is_null($product->variant) && $product->variants->sum('stock') == 0))
                <img class="w-full aspect-square group-hover:scale-105 transition-transform duration-300 grayscale"
                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                     alt="{{ $product->title }}">
                <div class="absolute top-3 -left-12 rotate-[-45deg] bg-red-500 text-white text-xs font-bold px-12 py-1 shadow-md">
                    ناموجود
                </div>
            @else
                <img class="w-full aspect-square group-hover:scale-105 transition-transform duration-300"
                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                     alt="{{ $product->title }}">
                @if($product->discounted_price)
                    <div class="absolute top-2 right-2 bg-gradient-to-r from-red-500 to-red-600 text-white px-2 py-1 rounded-full text-[10px] sm:text-xs font-bold shadow-md">
                        🔥 تخفیف
                    </div>
                @endif
            @endif
        </div>

        <!-- متن -->
        <div class="flex-1 w-full p-3 text-center">
            <h5 class="text-sm sm:text-base font-bold text-gray-800 line-clamp-2 min-h-[2.5rem]">
                {{ english_to_persian_num($product->title) }}
            </h5>

            @if((is_null($product->variant) && $product->stock > 0) || (!is_null($product->variant) && $product->variants->sum('stock') > 0))
                <div class="mt-2">
                    @if($product->discounted_price)
                        <div class="flex items-center justify-center gap-2 flex-wrap">
                            <span class="text-xs text-gray-400 line-through">
                                {{ english_to_persian_num(number_format($product->price)) }}
                            </span>
                            <span class="text-pars-700 font-bold text-sm sm:text-base">
                                {{ english_to_persian_num(number_format($product->discounted_price)) }}
                            </span>
                            <span class="text-xs text-gray-400">تومان</span>
                        </div>
                        <div class="inline-block mt-1 bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ english_to_persian_num(round((($product->price - $product->discounted_price) / $product->price) * 100)) }}% تخفیف
                        </div>
                    @else
                        <div class="flex items-center justify-center gap-1">
                            <span class="text-gray-700 font-bold text-sm sm:text-base">
                                {{ english_to_persian_num(number_format($product->price)) }}
                            </span>
                            <span class="text-xs text-gray-400">تومان</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-2">
                    <span class="text-gray-400 text-xs">ناموجود</span>
                </div>
            @endif
        </div>
    </a>
</div>