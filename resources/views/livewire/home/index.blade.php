<div>
    <div class="grid grid-cols-4 grid-rows-8 gap-2">
        @foreach(\App\Models\Product::all() as $product)
            <livewire:home.product-card :product="$product" />
        @endforeach
    </div>
</div>
