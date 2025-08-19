<div class="w-full">
    <div class="sm:flex flex-row-reverse w-full p-4  sm:p-8 rounded-2xl mx-auto mt-10 bg-pars-100 shadow">
        <div class="w-full sm:w-7/12 sm:mr-2">
            <h1 class="sm:hidden font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
            <a href="{{ asset('storage/products/' . $product->id . '/large/1.webp') }}" data-fancybox="gallery"
               data-caption="{{ english_to_persian_num($product->title) }}">
                <img class="rounded sm:h-80 mx-auto"
                     src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"/>
            </a>

        </div>
        <div class="w-full sm:w-5/12 sm:ml-2 mt-6 sm:mt-0">
            <h1 class="hidden sm:block font-bold text-xl mb-6">{{ english_to_persian_num($product->title) }}</h1>
            <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span
                    class="font-bold">شناسه محصول:</span> {{ english_to_persian_num($product->id) }}</h4>
            <h4 class="bg-pars-200 shadow rounded mb-3 p-1"><span class="font-bold">دسته بندی: </span><a
                    href="{{ '/category/' . \App\Models\Category::query()->find($product->category_id)->dashed_title }}">{{ \App\Models\Category::query()->find($product->category_id)->title }}</a>
            </h4>
            <h4 class="bg-pars-200 shadow rounded mb-6 p-1">
                <span class="font-bold">قیمت:</span>
                {{ english_to_persian_num(number_format($product->price)) }}
                تومان
            </h4>
            <button wire:click="addToCart()" wire:navigate wire:loading.attr="disabled"
                    class="bg-pars-500 cursor-pointer shadow rounded p-1 w-full transition-all text-white hover:bg-pars-600">
                افزودن به سبد خرید
               {{-- <svg wire:loading wire:target="makePayment" class="w-5 h-5 ml-2 animate-spin text-white"
                     fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M12 2a10 10 0 00-9 5.5 1 1 0 101.7 1A8 8 0 0112 4a8 8 0 017.3 4.5 1 1 0 101.7-1A10 10 0 0012 2z">
                    </path>
                </svg>--}}
            </button>
        </div>
    </div>
    <div class="w-full sm:w-11/12  p-4 rounded-2xl mx-auto mt-10 bg-pars-100 shadow min-h-24 mb-6">
        <small>طرح های مشابه</small>
        <div class="container mt-2">
            @foreach($similar as $value)
                <a class="similar-item relative h-40"
                   href="{{ route('product-page' , ['title' => $value->dashed_title]) }}">
                    <img src="{{ $value->url }}" alt="" class="h-40 rounded">
                </a>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener("livewire:navigated", function () {
            Fancybox.bind("[data-fancybox='gallery']", {
                Toolbar: true,
                Thumbs: true
            });
        });
    </script>
</div>
