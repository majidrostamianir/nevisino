<div>

    @foreach($orders as $order)
        <div class="w-full shadow p-4 border border-pars-400 cursor-pointer rounded mb-4"
             wire:click="toggleOrder({{ $order->id }})">
            @switch($order->status)
                @case('pending')
                    <div class="flex mb-4">
                        <p class="text-orange-300 font-bold pt-1">در انتظار پرداخت ...</p>
                        <button wire:click.prevent="payAgain('{{$order->id}}')"
                                wire:loading.attr="disabled"
                                wire:target="payAgain"
                                class="w-fix px-4 mr-12 text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-1 cursor-pointer">
                    <span wire:loading.remove wire:target="payAgain">
                        پرداخت
                    </span>
                            <span wire:loading wire:target="payAgain">
                        در حال انتقال به درگاه...
                    </span>
                        </button>
                    </div>
                    @break
                @case('paid')
                    <p class="text-green-500 font-bold mb-2">پرداخت شده</p>
                    @break
                @case('canceled')
                    <p class="text-gray-500 font-bold mb-2">لغو شده</p>
                    @break
            @endswitch
            <div class="flex flex-wrap">
                <span>{{ english_to_persian_num(verta($order->create_at)->format('%d %B %Y')) }}</span>
                <span class="mx-4  text-pars-400">&#9679;</span>
                <span>کد پیگیری سفارش: {{ english_to_persian_num($order->order_number) }}</span>
                <span class="mx-4  text-pars-400">&#9679;</span>
                <span>جمع کل سفارش: {{ english_to_persian_num(number_format($order->total_price)) }} تومان</span>
                <span class="mx-4  text-pars-400">&#9679;</span>
                <span>حمل و نقل: {{ english_to_persian_num(number_format($order->shipping_price)) }} تومان</span>
            </div>
            @if($openOrderId === $order->id)
                <div class="mt-8">
                    <div class="flex mb-2">
                        <span class="text-gray-400">تحویل گیرنده <span
                                class="text-pars-700">{{ english_to_persian_num($order->recipient_name) }}</span></span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span class="text-gray-400">شماره موبایل <span
                                class="text-pars-700">{{ english_to_persian_num($order->recipient_mobile) }}</span></span>
                    </div>
                    <div class="sm:flex">
                        <div class="w-full sm:w-1/2">
                            <span class=" text-gray-400">آدرس <span
                                    class="text-pars-700">{{ english_to_persian_num($order->postal_address) }}</span></span>
                        </div>
                        <div class="w-full sm:w-1/4 mt-0 sm:mt-4">
                            @switch($order->shipping_status)
                                @case('pending')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-orange-300 font-bold">در انتظار پرداخت</span>
                                        <span class="text-xs">مبلغ: {{ english_to_persian_num(number_format($order->amount)) }} تومان</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-300 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    @break
                                @case('processing')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-green-500 font-bold">در حال پردازش سفارش</span>
                                        <span class="text-xs">مرحله بعد: تحویل از انبار</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 25%"></div>
                                    </div>
                                    @break
                                @case('preparing')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-green-500 font-bold">در حال بسته بندی</span>
                                        <span class="text-xs">مرحله بعد: تحویل به اداره پست</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 40%"></div>
                                    </div>


                                    @break
                                @case('shipped')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-green-500 font-bold">در حال ارسال با پست</span>
                                        <span class="text-xs">مرحله بعد: تحویل کالا به مشتری</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 70%"></div>
                                    </div>
                                    <div class="flex justify-between">
                                <span
                                    class="text-sm mt-2">کد پیگیری مرسوله {{ english_to_persian_num($order->tracking_code) }}</span>
                                    </div>
                                    @break
                                @case('delivered')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-green-500 font-bold">تحویل داده شده</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                    </div>
                            @endswitch
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <div class="mb-2">
                        <div class="text-gray-400 mb-3">تراکنش ها</div>
                        <div class=" px-2 py-2 border-pars-400 rounded mb-4">
                            @foreach($order->transactions->sortByDesc('created_at') as $key => $value)
                                @if($key>0)
                                    <hr class="text-pars-400">
                                @endif
                                <div class="mt-4 mb-2 flex flex-wrap">
                                    @switch($value->status)
                                        @case('pending')
                                            <span><span class="ml-4  text-orange-500">&#9679;</span>در انتظار پرداخت</span>
                                            @break
                                        @case('success')
                                            <span><span class="ml-4  text-green-500">&#9679;</span>پرداخت موفق</span>
                                            @break
                                        @case('failed')
                                            <span><span class="ml-4  text-red-500">&#9679;</span>پرداخت ناموفق</span>
                                            @break
                                        @case('cancel')
                                            <span><span class="ml-4  text-gray-500">&#9679;</span>لغو شده</span>
                                            @break
                                    @endswitch
                                    <div>
                                        <span class="mx-4  text-pars-400">&#9679;</span>
                                        <span>کد پیگیری تراکنش {{ english_to_persian_num($value->authority) }}</span>
                                    </div>
                                    <div>
                                        <span class="mx-4  text-pars-400">&#9679;</span>
                                        <span>مبلغ تراکنش {{ english_to_persian_num(number_format($value->amount)) }} تومان</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="mx-4  text-pars-400">&#9679;</span>
                                        <span> {{ english_to_persian_num(verta($value->created_at)->format('%d %B %Y ساعت H:i:s')) }} </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap">
                            @foreach($order->items as $item)
                                    <?php
                                    $product = \App\Models\Product::query()->find($item->product_id)
                                    ?>
                                <div class="w-48 ml-2 mb-2">
                                    <a href="{{ route('product-page' , ['title' => $product->dashed_title]) }}"
                                       class="flex flex-row sm:flex-col items-center bg-white rounded shadow hover:shadow-lg cursor-pointer h-full">
                                        <div
                                            class="w-24 sm:w-full  overflow-hidden rounded-r-sm sm:rounded-t-sm sm:rounded-b-none">
                                            <img class="w-full aspect-square hover:scale-105 transition-all"
                                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                                 alt="">
                                        </div>
                                        <div class="flex-1 px-2 sm:px-0 sm:py-4 text-right sm:text-center">
                                            <h5 class="text-xs font-bold">
                                                {{ english_to_persian_num($product->title) }}
                                            </h5>
                                            <h5 class="text-xs sm:text-sm mt-2">
                                                {{ english_to_persian_num(number_format($item->price_snapshot)) }} تومان
                                            </h5>
                                            <h5 class="text-xs sm:text-sm mt-2">
                                                تعداد {{ english_to_persian_num($item->quantity) }} عدد
                                            </h5>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
