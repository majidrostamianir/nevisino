<div class="w-full self-center relative">
    <input type="text" wire:model.live.debounce.750ms="query"
           wire:focus="focus"
           @if($isFocused && ($urls->isNotEmpty() || $products->isNotEmpty()))
               wire:click.away="blur"
           @endif
           class="text-sm bg-white w-full mr-3 rounded-2xl pr-4 shadow"
           placeholder="جستجو ...">
    <span class="absolute top-2.5 left-0  cursor-pointer" wire:click="clearSearch">
        @if($query != '' )
            <svg width="16" height="16" viewBox="0 0 16 16">
            <circle cx="8" cy="8" r="7" fill="#318dc1"/>
            <line x1="5" y1="5" x2="11" y2="11" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="11" y1="5" x2="5" y2="11" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        @endif
    </span>
    @if($isFocused && ($urls->isNotEmpty() || $products->isNotEmpty()))
        <div class="absolute top-10 rounded right-3 w-full bg-white p-4 z-10 shadow" wire:ignore.self>
            <span class="text-xs bg-pars-100 py-0.5 px-1 rounded cursor-default">محصولات</span>
            @foreach($products as $product)
                <a wire:navigate href="{{ route('product-page', ['title' => $product->dashed_url , 'npi'=>$product->id] ) }}"
                   class="block rounded-2xl p-2 hover:bg-pars-400 hover:text-pars-500 hover:cursor-pointer">{{ english_to_persian_num($product->title) }}</a>
            @endforeach
            <hr class="my-2">
            <span class="text-xs bg-pars-100 py-0.5 px-1 rounded cursor-default">دسته بندی</span>
            @foreach($urls as $url)
                <a wire:navigate href="{{ route('category-page', ['dashed' => $url->dashed_url] ) }}"
                   class="block rounded-2xl p-2 hover:bg-pars-400 hover:text-pars-500 hover:cursor-pointer">{{ english_to_persian_num($url->title_h1 ?? $url->title_tag) }}</a>
            @endforeach
        </div>
    @elseif($isFocused && strlen($query)>2 && $urls->isEmpty() && $products->isEmpty())
        <span class="absolute top-10 rounded text-center right-3 w-full bg-white p-4 z-10 shadow" wire:ignore.self>
            چیزی پیدا نشد!
        </span>
        @endif
</div>
