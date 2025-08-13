<div>
    <a href="{{ route('product-page' , ['title' => $product->dashed_title]) }}" class="h-96 w-80 bg-white flex flex-col items-center pt-4 rounded-sm shadow cursor-pointer">
        <div class="w-72 h-72 overflow-hidden">
            <img class="w-full h-full object-cover object-center"
                 src="{{ asset('/images/banner/b4.jpg') }}" alt="">
        </div>
        <div class="mt-3">
            <h5 class="font-bold text-right">{{ english_to_persian_num($product->title) }}</h5>
        </div>
        <div class="mt-3">
            <h5 class="text-right">{{ english_to_persian_num(number_format($product->price)) }} تومان</h5>
        </div>
    </a>
</div>
