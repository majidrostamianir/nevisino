<div>
    <div class="sm:flex">
        @if($cart)
            <table class="w-full sm:w-2/3 text-right bg-pars-100 rounded-2xl overflow-x-scroll">
                <thead>
                <tr class="shadow-xs">
                    <th class="px-4 py-2">تصویر</th>
                    <th class="px-4 py-2">محصول</th>
                    <th class="px-4 py-2">تعداد</th>
                    <th class="px-4 py-2">قیمت کل</th>
                    <th class="px-4 py-2">حذف</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($cart as $key => $product)
                    <tr wire:key="{{$key}}" class=" border-b border-b-gray-200 last:border-0 ">
                        <td class="px-4 py-2">
                            <img class="w-24 rounded"
                                 src="{{ asset('storage/products/'.$product['id'].'/small/1.webp') }}">
                        </td>
                        <td class="px-4 py-2">
                            {{ $product['title'] }}  {{ english_to_persian_num($product['code']) }}
                            @if($product['variant'])
                                - {{ $product['variantName'] }}
                            @endif
                        </td>

                        <td class="px-4 py-2">
                            <div class="flex flex-col sm:flex-row items-center">
                                <div wire:click.prevent="increase('{{ $key }}')"
                                     class="w-10 h-10 flex items-center justify-center bg-pars-300 hover:bg-pars-400  sm:rounded-none rounded-t-2xl sm:rounded-r-2xl  cursor-pointer select-none">
                                    +
                                </div>
                                <input type="text"
                                       disabled
                                       class="w-10 sm:w-14 h-10 text-center border-t border-b border-pars-300 border-l-0 border-r-0 focus:outline-none"
                                       value="{{ strtr($product['quantity'], ['0' =>'۰' ,'1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']) }}">
                                <div wire:click.prevent="decrease('{{ $key }}')"
                                     class="w-10 h-10 flex items-center justify-center bg-pars-300 hover:bg-pars-400 sm:rounded-none rounded-b-2xl  sm:rounded-l-2xl cursor-pointer select-none">
                                    -
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
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
            <div class="w-full sm:w-2/3 bg-pars-100 rounded-2xl shadow">
                <img class="w-32 mx-auto pt-8" src="{{ asset('images/cart2.png') }}" alt="">
                <p class="text-center  pt-8 w-full">سبد خرید شما خالی است</p>
            </div>
        @endif
        <div class="sticky top-20 h-fit w-full  sm:w-1/3 p-4 bg-pars-100 shadow rounded-2xl mt-2 sm:mt-0 sm:mr-2">
            <div class="w-full mb-4 sm:flex justify-between">
                <div class="w-full">
                    <strong>جمع مبلغ سفارش:</strong>
                </div>
                <div class="w-full text-left">
                    <span>{{ english_to_persian_num(number_format($sum)) }} تومان</span>
                </div>
            </div>
            <div class="w-full mb-4 sm:flex justify-between">
                <div class="w-full">
                    <strong>حمل و نقل:</strong>
                </div>
                <div class="w-full text-left">
                    <span>{{ english_to_persian_num(number_format($shipping)) }} تومان</span>
                </div>
            </div>
            <div class="w-full sm:flex justify-between">
                <div class="w-full">
                    <strong>زمان تحویل به پست:</strong>
                </div>
                <div class="w-full text-left">
                    <span>همه روزه راس ساعت ۱۰ صبح(بجز روزهای تعطیل)</span>
                </div>
            </div>

            <hr class="text-gray-300 my-5">
            <div class="w-full mb-4 sm:flex justify-between">
                <div class="w-full">
                    <strong>مبلغ قابل پرداخت:</strong>
                </div>
                <div class="w-full text-left">
                    <span>{{ english_to_persian_num(number_format($total)) }} تومان</span>
                </div>
            </div>

            <div class="w-full sm:flex justify-between">
                <button wire:click.prevent="checkout" class="w-full text-center bg-pars-700 hover:bg-pars-800 text-white rounded-2xl py-1 cursor-pointer">
                    تکمیل سفارش
                </button>
            </div>
        </div>
    </div>
</div>
