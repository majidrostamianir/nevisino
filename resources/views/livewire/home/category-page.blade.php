<div>
    <div class="flex mt-12 sm:mt-0">
        @foreach($products as $product)
           <livewire:home.product-card :product="$product" />
        @endforeach
    </div>

</div>
