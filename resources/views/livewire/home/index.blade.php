<div>
    {{--<div class="grid grid-cols-6 grid-rows-4 gap-4">
        <div class="col-span-2 row-span-2">
            <img src="images/banner/b3.jpg" class="rounded-md shadow w-full h-[35vh]" alt="">
        </div>
        <div class="col-span-2 row-span-2 col-start-1 row-start-3">
            <img src="images/banner/b3.jpg" class="rounded-md shadow w-full h-[35vh]" alt="">
        </div>
        <div class="col-span-2 row-span-2 col-start-3 row-start-1">
            <img src="images/banner/b3.jpg" class="rounded-md shadow w-full h-[35vh]" alt="">
        </div>
        <div class="col-span-2 row-span-2 col-start-3 row-start-3">
            <img src="images/banner/b3.jpg" class="rounded-md shadow w-full h-[35vh]" alt="">
        </div>
        <div class="col-span-2 row-span-4 col-start-5 row-start-1">
            <img src="images/banner/b3.jpg" class="rounded-md shadow w-fit h-full" alt="">
        </div>
    </div>--}}

    @foreach(\App\Models\Product::all() as $product)
        <livewire:home.product-card :product="$product" />
    @endforeach
</div>
