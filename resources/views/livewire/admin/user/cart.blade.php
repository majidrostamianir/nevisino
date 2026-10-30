<div class="flex flex-col lg:flex-row gap-2">
    <!-- جدول -->
    <div class="w-full lg:w-2/3 overflow-x-auto">
        @if($cart)
            <table class="w-full text-right bg-pars-100 rounded-2xl">
                <tbody>
                @foreach ($cart as $key => $product)
                    <tr wire:key="{{ $key }}" class="border-b border-b-gray-200 last:border-0">
                        {{-- تصویر --}}
                        <td class="px-4 py-2">
                            @if($product['variant'])
                                <a wire:navigate
                                   href="{{ route('product-page', ['title' => $product['dashed_url'], 'npi' => $product['id']]) }}">
                                    <img class="w-24 rounded"
                                         src="{{ asset('storage/products/' . $product['id'] . '/small/' . $product['variant'] . '.webp') }}"
                                         alt="">
                                </a>
                            @else
                                <a wire:navigate
                                   href="{{ route('product-page', ['title' => $product['dashed_url'], 'npi' => $product['id']]) }}">
                                    <img class="w-24 rounded"
                                         src="{{ asset('storage/products/' . $product['id'] . '/small/1.webp') }}"
                                         alt="">
                                </a>
                            @endif
                        </td>

                        {{-- عنوان و واریانت --}}
                        <td class="px-4 py-2">
                            <span class="font-bold">
                                {{ $product['title'] }}
                            </span>

                            @if($product['variant'])
                                <br>
                                <span class="text-sm">
                                    {{ $product['variantField'] }}: {{ $product['variantName'] }}
                                </span>
                            @endif

                            {{-- قیمت‌ها در موبایل --}}
                            <div class="lg:hidden pt-2 space-y-1">
                                <div class="text-sm">
                                    <span class="text-gray-500">نقدی:</span>
                                    @if($product['quantity'] > 1)
                                        {{ english_to_persian_num(number_format($product['cash_price'])) }}
                                        ×
                                        {{ english_to_persian_num($product['quantity']) }}
                                        =
                                        {{ english_to_persian_num(number_format($product['quantity'] * $product['cash_price'])) }}
                                    @else
                                        {{ english_to_persian_num(number_format($product['cash_price'])) }}
                                    @endif
                                    <span class="text-xs text-gray-400">تومان</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">قسطی:</span>
                                    @if($product['quantity'] > 1)
                                        {{ english_to_persian_num(number_format($product['installment_price'])) }}
                                        ×
                                        {{ english_to_persian_num($product['quantity']) }}
                                        =
                                        {{ english_to_persian_num(number_format($product['quantity'] * $product['installment_price'])) }}
                                    @else
                                        {{ english_to_persian_num(number_format($product['installment_price'])) }}
                                    @endif
                                    <span class="text-xs text-gray-400">تومان</span>
                                </div>
                            </div>
                        </td>

                        {{-- قیمت‌ها در دسکتاپ --}}
                        <td class="px-4 py-2 hidden lg:table-cell">
                            <div class="text-xs text-gray-500 mb-1">نقدی</div>
                            <div>
                                @if($product['quantity'] > 1)
                                    {{ english_to_persian_num(number_format($product['quantity'] * $product['cash_price'])) }}
                                    <span class="text-xs text-gray-400">
                                        ({{ english_to_persian_num($product['quantity']) }}
                                        × {{ english_to_persian_num(number_format($product['cash_price'])) }})
                                    </span>
                                @else
                                    {{ english_to_persian_num(number_format($product['cash_price'])) }}
                                @endif
                                <span class="text-xs text-gray-400">تومان</span>
                            </div>

                            <div class="text-xs text-gray-500 mt-3 mb-1">قسطی</div>
                            <div>
                                @if($product['quantity'] > 1)
                                    {{ english_to_persian_num(number_format($product['quantity'] * $product['installment_price'])) }}
                                    <span class="text-xs text-gray-400">
                                        ({{ english_to_persian_num($product['quantity']) }}
                                        × {{ english_to_persian_num(number_format($product['installment_price'])) }})
                                    </span>
                                @else
                                    {{ english_to_persian_num(number_format($product['installment_price'])) }}
                                @endif
                                <span class="text-xs text-gray-400">تومان</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="w-full bg-pars-100 rounded-2xl shadow text-center py-8">
                <img class="w-32 mx-auto" src="{{ asset('images/cart2.png') }}" alt="">
                <p class="pt-4 mb-8">سبد خرید خالی است</p>
            </div>
        @endif
    </div>

    <!-- منوی سمت راست -->
    <div class="w-full h-fit lg:w-1/3 p-4 bg-pars-100 shadow rounded-2xl mt-2 lg:mt-0 lg:mr-2 sticky top-20">
        <div class="w-full mb-4 flex justify-between">
            <div><strong>جمع مبلغ (نقدی)</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class="text-left">
                <span>{{ english_to_persian_num(number_format($sumCash)) }} تومان</span>
            </div>
        </div>

        <div class="w-full mb-4 flex justify-between">
            <div><strong>جمع مبلغ (قسطی)</strong></div>
            <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
            <div class="text-left">
                <span>{{ english_to_persian_num(number_format($sumInstallment)) }} تومان</span>
            </div>
        </div>
    </div>
</div>