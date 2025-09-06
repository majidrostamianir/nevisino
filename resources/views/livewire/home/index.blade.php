<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach(\App\Models\Product::all() as $product)
            <livewire:home.product-card :product="$product"/>
        @endforeach
    </div>
</div>
