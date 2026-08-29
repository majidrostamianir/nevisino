@push('seo-meta-tags')
    <meta name="description" content="{{ $this->url->meta_description }}">

    @php
        $robots = ($this->url->indexing ? 'index' : 'noindex') . ', ' .
                  ($this->url->following ? 'follow' : 'nofollow');
    @endphp

    <meta name="robots" content="{{ $robots }}">
@endpush
<div class="flex gap-4">
    <div class="w-3/12 hidden lg:block bg-white rounded shadow-md sticky top-20 h-fit p-2 text-center">
        <span class="border-b-2 border-b-red-500">تخفیف ویژه</span>
        @foreach( $discounted_products as $value)
            <a href="{{ $value['link'] }}" class="flex mt-4 gap-2">
                <img class="w-16 rounded" src="{{ $value['image'] }}" alt="">
                <div class="text-right">
                    <small class="block">{{ english_to_persian_num($value['name']) }}</small>
                    @if($value['has_discount'])
                        <small class="block line-through">{{ english_to_persian_num(number_format($value['base_price'])) }} تومان</small>
                        <small class="block text-red-600 font-bold">{{ english_to_persian_num(number_format($value['final_price'])) }} تومان</small>
                    @else
                        <small class="block">{{ english_to_persian_num(number_format($value['final_price'])) }} تومان</small>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    <div class="w-full lg:w-9/12 ">
        @if($url->title_h1)
            <div class="bg-white rounded-xl shadow-md p-4 mb-8 article">
                <h1 class="text-gray-700">
                    {{ $url->title_h1 }}
                </h1>
                <div class="p-4 text-justify text-xl">
                    {!! $url->mini_article !!}
                </div>
            </div>
        @endif

        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                @foreach($products as $product)
                    <livewire:components.product-card :product="$product"/>
                @endforeach
            </div>
        @else
            <div class="w-full bg-pars-100 rounded-2xl shadow text-center py-8">
                <img class="w-32 mx-auto" src="{{ asset('images/cart2.png') }}" alt="">
                <p class="pt-4 mb-8 ">محصولی در این دسته بندی موجود نیست</p>
                <a href="{{ route('shop') }}" wire:navigate
                   class="bg-pars-500 text-white px-2 py-1 rounded hover:bg-pars-600 shadow">برو به فروشگاه</a>
            </div>
        @endif

        @if($url->article)
            <div class="bg-white rounded-xl text-xl shadow-md p-8 mb-4 article text-justify">
                {!! $url->article !!}
            </div>
        @endif
    </div>
</div>