@push('variant')
    <link rel="canonical" href="https://nevisino.ir/product/{{ $npi }}/{{ $title }}" />
@endpush
<div class="w-full"
     x-data="{
        selected: @entangle('selectedVariant').live,
        isLoading: false,
        select(id) {
            if (this.selected == id) return;
            this.selected = id;
        }
     }"
     x-init="$watch('selected', () => { isLoading = true; })">
    <div class="grid grid-cols-1 sm:grid-cols-[5fr_7fr] sm:grid-rows-[auto_1fr] sm:gap-x-4 w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm">
        <div class="sm:col-start-1 sm:row-start-1 min-w-0">
            <h1 class="font-bold text-xl sm:text-2xl mb-6 text-gray-800">{{ $this->titleFa }}</h1>

            <div class="flex flex-wrap items-center gap-2 mb-3">
                <div class="inline-flex items-center gap-1 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <a href="{{ route('home') }}" class="hover:text-pars-600">نویسینو</a>
                    @if($this->parentCategory)
                        <span>/</span>
                        <a href="/category/{{ $this->parentCategory->dashed_url }}" class="hover:text-pars-600">{{ $this->parentCategory->title }}</a>
                    @endif
                    @if($this->category)
                        <span>/</span>
                        <a href="/category/{{ $this->category->dashed_url }}" class="hover:text-pars-600">{{ $this->category->title }}</a>
                    @endif
                </div>
            </div>

            @if($product->code)
                <x-product.info-row path="M7 20l4-16m2 16l4-16M6 9h14M4 15h14">
                    <span class="font-bold">کد محصول:</span>
                    <span>{{ english_to_persian_num($product->code) }}</span>
                </x-product.info-row>
            @endif

            @if($this->category)
                <x-product.info-row path="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    <span class="font-bold">دسته بندی:</span>
                    <a href="/category/{{ $this->category->dashed_url }}" class="hover:text-pars-600">{{ $this->category->title }}</a>
                </x-product.info-row>
            @endif

            <x-product.info-row path="M5 13l4 4L19 7" bg="bg-green-50" icon-class="text-green-500">
                <span>محصول ارسالی دقیقا مشابه تصاویر می باشد.</span>
            </x-product.info-row>

            @if($this->inStock)
                <div class="flex items-center gap-2 text-lg bg-gray-50 px-3 py-2 rounded-lg mb-8">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold">قیمت:</span>
                    @if($this->price['hasDiscount'])
                        <span class="line-through text-gray-400 text-sm">{{ $this->price['base'] }}</span>
                    @endif
                    <span class="font-bold text-pars-700 text-lg">{{ $this->price['final'] }}</span>
                    <span>تومان</span>
                </div>
            @endif
        </div>

        {{-- ۲) گالری تصاویر --}}
        <div class="sm:col-start-2 sm:row-start-1 sm:row-span-2 min-w-0">
            <x-product.image-viewer :src="$src" :alt="$this->titleFa" />

            <div class="flex flex-wrap justify-center mt-4">
                @foreach($this->gallery as $image)
                    <div wire:key="thumb-{{ $image['id'] }}">
                        <img @click="select('{{ $image['id'] }}')"
                             class="w-16 h-16 cursor-pointer rounded transition-all border-2 border-transparent mx-2"
                             :class="{ 'border-pars-600 ring-2 ring-offset-2 ring-pars-600': selected == '{{ $image['id'] }}' }"
                             src="{{ asset('storage/products/' . $product->id . '/small/' . $image['id'] . '.webp') }}"
                             alt="{{ $image['alt'] }}">
                        @if($image['label'])
                            <div class="text-center mt-1 text-xs transition-all"
                                 :class="{ 'font-bold text-pars-600': selected == '{{ $image['id'] }}' }">{{ $image['label'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ۳) انتخاب واریانت و خرید --}}
        <div class="sm:col-start-1 sm:row-start-2 min-w-0 mt-6 sm:mt-0">
            @if($this->inStock)
                @if($product->variant)
                    <x-product.variant-picker :product="$product" />
                @endif

                <div class="flex items-center gap-3">
                    <x-product.quantity-stepper :quantity="$quantity" :notice="$this->stockNotice" />

                    <button wire:click="addToCart()" wire:loading.attr="disabled"
                            class="relative flex items-center cursor-pointer justify-center bg-pars-500 text-sm text-white px-6 h-10 rounded-xl shadow-sm hover:bg-pars-600 hover:shadow-md transition-all duration-200 font-medium">
                        افزودن به سبد خرید
                        <x-spinner wire:loading wire:target="addToCart" class="w-5 h-5 absolute left-2 text-white" />
                    </button>
                </div>
            @else
                <div class="text-center bg-red-50 border border-red-200 rounded-xl p-4">
                    <span class="text-red-500 font-bold">اتمام موجودی</span>
                </div>
            @endif
        </div>
    </div>

    {{-- توضیحات محصول --}}
    @if($product->description)
        <x-product.section title="معرفی محصول" path="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            <div class="text-justify text-gray-600 leading-relaxed">
                {!! english_to_persian_num($product->description) !!}
            </div>
        </x-product.section>
    @endif

    {{-- ویژگی‌ها --}}
    @if(!empty($this->specifications))
        <x-product.section title="ویژگی‌ها" path="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
            <div class="rounded-xl overflow-hidden border border-gray-100">
                @foreach($this->specifications as $spec)
                    <div class="flex text-sm">
                        <div class="text-gray-700 w-1/2 md:w-1/3 p-2.5 pr-4 border-b border-gray-100 bg-gray-50 font-medium">{{ $spec['name'] }}</div>
                        <div class="text-gray-800 w-1/2 md:w-2/3 p-2.5 pr-4 border-b border-gray-100 bg-white">{{ $spec['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </x-product.section>
    @endif

    {{-- محصولات مرتبط --}}
    @if($this->relatedProducts->isNotEmpty())
        <x-product.section title="کالاهای مرتبط" path="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4" wire:ignore>
                @foreach($this->relatedProducts as $related)
                    <livewire:components.product-card :product="$related" :key="'related-' . $related->id"/>
                @endforeach
            </div>
        </x-product.section>
    @endif
</div>