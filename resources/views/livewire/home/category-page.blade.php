@push('seo-meta-tags')
    <meta name="description" content="{{ $this->url->description }}">

    @php
        $robots = ($this->url->indexing ? 'index' : 'noindex') . ', ' .
                  ($this->url->following ? 'follow' : 'nofollow');
    @endphp

    <meta name="robots" content="{{ $robots }}">
@endpush
<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($products as $product)
           <livewire:components.product-card :product="$product" />
        @endforeach
    </div>
    <div class="my-20 px-4">
        {!! $url->article !!}
    </div>
</div>
