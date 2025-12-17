<div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        @foreach($products as $product)
            <livewire:components.product-card :product="$product"/>
        @endforeach
    </div>
</div>
