<div class="flex flex-col sm:flex-row gap-2">
    <!-- جدول -->
    <div class="w-full sm:w-2/3 overflow-x-auto">
        @if($cart)
            <table class=" w-full text-right bg-pars-100 rounded-2xl">
                <tbody>
                @foreach ($cart as $key => $product)

                    <tr wire:key="{{$key}}" class="border-b border-b-gray-200 last:border-0">
                        <td class="px-4 py-2">
                            @if($product['variant'])
                                <a wire:navigate href="{{ route('product-page' , ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url]) }}">
                                    <img class="w-24 rounded"
                                         src="{{ asset('storage/products/' . $product['id'] . '/small/'. $product['variant'] .'.webp') }}"
                                         alt="">
                                </a>
                            @else
                                <a wire:navigate href="{{ route('product-page' , ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url]) }}">
                                    <img class="w-24 rounded"
                                         src="{{ asset('storage/products/' . $product['id'] . '/small/1.webp') }}"
                                         alt="">
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="font-bold">{{ $product['title'] }}  {{ english_to_persian_num($product['code']) }}</span>
                            @if($product['variant'])
                                <br>
                                <span class="text-sm">
                                    {{ \App\Models\Product::query()->find($product['id'])->variant }}
                                : {{ $product['variantName'] }}
                                </span>
                                <br>
                                <div class="sm:hidden pt-2">
                                    @if($product['quantity'] > 1)
{{--                                        {{  english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}--}}
{{--                                        = --}}
                                        {{ english_to_persian_num($product['quantity']) }}
                                        × {{ english_to_persian_num(number_format($product['price'])) }}
                                    @else
                                        {{  english_to_persian_num(number_format( $product['price'])) }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 hidden sm:table-cell">
                            @if($product['quantity'] > 1)
                                {{  english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}
                                =  {{ english_to_persian_num($product['quantity']) }}
                                × {{ english_to_persian_num(number_format($product['price'])) }}
                            @else
                                {{  english_to_persian_num(number_format( $product['price'])) }}
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span wire:click.prevent="removeFromCart('{{ $key }}')"
                                  class="font-bold bg-pars-500 transition-all duration-300 hover:bg-red-500 text-white cursor-pointer rounded-full px-2 ">×</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="w-full bg-pars-100 rounded-2xl shadow text-center py-8">
                <img class="w-32 mx-auto" src="{{ asset('images/cart2.png') }}" alt="">
                <p class="pt-4 mb-8">سبد خرید شما خالی است</p>
                <a href="{{ route('shop') }}" wire:navigate
                   class="bg-pars-500 text-white px-2 py-1 rounded hover:bg-pars-600 shadow">برو به فروشگاه</a>
            </div>
        @endif
    </div>

    <!-- منوی سمت راست -->
    <div class="w-full h-fit sm:w-1/3 p-4 bg-pars-100 shadow rounded-2xl mt-2 sm:mt-0 sm:mr-2 sticky top-20">
        <div class="w-full mb-4 flex justify-between">
            <div class=""><strong>جمع مبلغ سفارش</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class=" text-left"><span>{{ english_to_persian_num(number_format($sum)) }} تومان</span></div>
        </div>
        <div class="w-full mb-4 flex justify-between">
            <div class=""><strong>روش ارسال</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class=" text-left">پست پیشتاز</div>
        </div>
        <div class="w-full mb-4 flex justify-between">
            <div class=""><strong>زمان ارسال</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class=text-left">
                <span>هر روز ساعت ۱۰ صبح<span class="text-xs">(روزهای کاری)</span></span>
            </div>
        </div>
        <div class="w-full mb-16 flex justify-between">
            <div class=""><strong>زمان رسیدن به مقصد</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class="text-left">{{ english_to_persian_num('بین 3 تا 5 روز') }}</div>
        </div>

        <div class="w-full sm:flex justify-between">
            <button wire:click.prevent="checkout"
                    class="w-full text-center bg-pars-700 hover:bg-pars-800 text-white rounded-2xl py-1 cursor-pointer">
                تکمیل سفارش
            </button>
        </div>
    </div>
</div>
