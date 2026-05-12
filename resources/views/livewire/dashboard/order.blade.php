<div
    x-data="{
        openOrderNumber: null,

        init() {
            const params = new URLSearchParams(window.location.search)
            const openNumber = params.get('open')

            if (openNumber) {
                this.openOrderNumber = openNumber

                this.$nextTick(() => {
                    const el = document.getElementById('order-' + openNumber)
                    if (!el) return

                    // فقط لپ‌تاپ به بالا
                    const isDesktop = window.innerWidth >= 1024
                    const offset = isDesktop ? 80 : 0

                    const top =
                        el.getBoundingClientRect().top +
                        window.pageYOffset -
                        offset

                    window.scrollTo({
                        top,
                        behavior: 'smooth'
                    })
                })
            }
        }
    }"
>
    @foreach($orders as $order)
        <div
            id="order-{{ $order->order_number }}"
            wire:key="{{ $order->order_number }}"
            class="w-full shadow p-4 border border-pars-400 bg-white rounded mb-4 order-card">

            <div @click="openOrderNumber = (openOrderNumber === '{{ $order->order_number }}' ? null
                    : '{{ $order->order_number }}')"
                 class="toggle-btn flex justify-between items-center cursor-pointer">
                <div>
                    @switch($order->status)
                        @case('pending')
                            @if($order->transactions()->where('payment_gateway', 'card')->where('status','pending')->exists())
                                <div class="flex mb-4">
                                    <p class="text-orange-300 font-bold pt-1">در انتظار تایید پشتیبانی ...</p>
                                    <button
                                        wire:click.prevent.stop="dispatch([Goftino.open() , Goftino.sendMessage({text: 'لطفا رسید انتقال وجه را ارسال فرمایید'})])"
                                        wire:target="payAgain({{ $order->id }})"
                                        class="cursor-pointer text-nowrap px-2 text-xs mr-12 text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-1 flex items-center justify-center relative">
                                        ارسال رسید تراکنش
                                        <span wire:loading wire:target="payAgain({{ $order->id }})"
                                              class="flex items-center justify-center">
                                        <svg class="w-6 h-6 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    </span>
                                    </button>
                                </div>
                            @else
                                <div class="flex mb-4">
                                    <p class="text-orange-300 font-bold pt-1">در انتظار پرداخت ...</p>
                                    <button
                                        wire:click.prevent.stop="payAgain({{ $order->id }})"
                                        wire:target="payAgain({{ $order->id }})"
                                        class="cursor-pointer text-nowrap px-4 text-xs mr-auto md:mr-12 text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-1 flex items-center justify-center relative">
                                    <span wire:loading.remove wire:target="payAgain({{ $order->id }})">
                                        پرداخت
                                    </span>
                                        <span wire:loading wire:target="payAgain({{ $order->id }})"
                                              class="flex items-center justify-center">
                                        <svg class="w-6 h-6 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    </span>
                                    </button>
                                </div>
                            @endif
                            @break
                        @case('paid')
                            <p class="text-green-500 font-bold mb-2">پرداخت شده</p>
                            @break
                        @case('canceled')
                            <p class="text-gray-500 font-bold mb-2">لغو شده</p>
                            @break
                    @endswitch
                    <div class="flex flex-wrap">
                        <span>{{ english_to_persian_num(verta($order->created_at)->format('%d %B %Y - H:i:s')) }}</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>کد پیگیری سفارش: {{ english_to_persian_num($order->order_number) }}</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>جمع کل سفارش: {{ english_to_persian_num(number_format($order->total_price)) }} تومان</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>حمل و نقل: {{ english_to_persian_num(number_format($order->shipping_price)) }} تومان</span>
                    </div>
                </div>

                <button
                    class="flex items-center justify-center
                     h-8 px-2 sm:px-2 lg:px-0 lg:w-8
                     rounded-full lg:rounded-full
                     bg-pars-300 text-black
                     transition-all duration-200
                     cursor-pointer">
                    <svg
                        x-bind:class="{'rotate-180': openOrderNumber === '{{ $order->order_number }}'}"
                        class="w-4 h-4 transition-transform duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

            </div>

            <div x-show="openOrderNumber === '{{ $order->order_number }}'"
                 x-collapse
                 class="order-details overflow-hidden transition-all duration-300 ease-in-out mt-2">
                <div class="mt-8">
                    <div class="w-full sm:flex">
                        <div class="w-full sm:w-1/2 mb-4 sm:mb-0">
                            <div>
                             <span class="text-gray-400">تحویل گیرنده <span
                                     class="text-pars-700">{{ english_to_persian_num($order->recipient_name) }}</span></span>
                                <span class="mx-4  text-pars-400">&#9679;</span>
                                <span class="text-gray-400">شماره موبایل <span
                                        class="text-pars-700">{{ english_to_persian_num($order->recipient_mobile) }}</span></span>
                            </div>
                            <div class="w-full sm:w-1/2">
                            <span class=" text-gray-400">آدرس <span
                                    class="text-pars-700">
                                    {{ $order->province }} -
                                    {{ $order->city }} -
                                    {{ english_to_persian_num($order->postal_address) }}
                                </span></span>
                                <span class="mx-4  text-pars-400">&#9679;</span>
                                <span class="text-gray-400">کد پستی <span
                                        class="text-pars-700">{{ english_to_persian_num($order->zipcode) }}</span></span>
                                <span class="mx-4  text-pars-400">&#9679;</span>
                                <span class="text-gray-400">توضیحات شما <span
                                        class="text-pars-700">{{ english_to_persian_num($order->description) }}</span></span>

                            </div>
                        </div>
                        <div class="w-full sm:w-1/2">
                            @switch($order->shipping_status)
                                @case('pending')
                                    <div class="flex justify-between">
                                        @if($order->transactions()->where('payment_gateway', 'card')->where('status','pending')->exists())
                                            <span
                                                class="text-sm text-orange-300 font-bold">در انتظار تایید پشتیبانی</span>
                                        @else
                                            <span class="text-sm text-orange-300 font-bold">در انتظار پرداخت</span>
                                        @endif
                                        <span class="text-xs">مبلغ: {{ english_to_persian_num(number_format($order->amount)) }} تومان</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-300 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    @break
                                @case('processing')
                                    <div class="flex justify-between">
                                        <span class="text-sm text-green-500 font-bold">در حال پردازش سفارش</span>
                                        <span class="text-xs">مرحله بعد: بسته بندی</span>
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
                                        <span class="text-sm text-green-500 font-bold">در حال ارسال به آدرس شما</span>
                                        <span class="text-xs">مرحله بعد: تحویل کالا به مشتری</span>
                                    </div>
                                    <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 70%"></div>
                                    </div>
                                    <div class="flex justify-between">
                                        <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}"
                                           target="_blank"
                                           rel="nofollow noopener noreferrer"
                                           class="text-sm mt-2 cursor-pointer underline">کد پیگیری
                                            مرسوله {{ english_to_persian_num($order->tracking_code) }}</a>
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

                    <div class="mt-8">
                        <div class="mb-2">
                            <div class="text-gray-400">تراکنش ها</div>
                            <div class=" p-2 border-pars-400 rounded mb-4 bg-pars-200">
                                @foreach($order->transactions->sortByDesc('created_at') as $key => $value)
                                    <div class="flex flex-wrap items-center">
                                        @switch($value->status)
                                            @case('pending')
                                                @if($value->payment_gateway == 'card')
                                                    <span class="text-orange-500"><span class="ml-4  ">&#9679;</span>در انتظار تایید پشتیبانی</span>
                                                @else
                                                    <span class="text-orange-500"><span class="ml-4  ">&#9679;</span>در انتظار پرداخت</span>
                                                @endif
                                                @break
                                            @case('success')
                                                <span class="text-green-500"><span
                                                        class="ml-4  ">&#9679;</span>پرداخت موفق</span>
                                                @break
                                            @case('failed')
                                                <span class="text-red-500"><span
                                                        class="ml-4  ">&#9679;</span>پرداخت ناموفق</span>
                                                @break
                                            @case('cancel')
                                                <span class="text-gray-500">
                                                    <span class="ml-4  ">&#9679;</span>لغو شده</span>
                                                @break
                                        @endswitch
                                        <div>
                                            @if($value->payment_gateway == 'card')
                                                <span class="mx-4  text-pars-400">&#9679;</span>
                                                <span>ارسال شده به کارت: {{ english_to_persian_num($value->authority) }}</span>
                                            @else
                                                <span class="mx-4  text-pars-400">&#9679;</span>
                                                <span>کد پیگیری تراکنش {{ english_to_persian_num($value->authority) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="mx-4  text-pars-400">&#9679;</span>
                                            <span>مبلغ تراکنش {{ english_to_persian_num(number_format($value->amount)) }} تومان</span>
                                        </div>
                                        <div>
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
                                        <a href="{{ route('product-page' , ['title' => $product->dashed_url, 'npi'=>$product->id]) }}"
                                           class="flex flex-row sm:flex-col items-center bg-white rounded shadow hover:shadow-lg cursor-pointer h-full">
                                            <div
                                                class="w-24 sm:w-full  overflow-hidden rounded-r-sm sm:rounded-t-sm sm:rounded-b-none">
                                                @if($item->variant_id)
                                                    <img class="w-full aspect-square hover:scale-105 transition-all"
                                                         src="{{ asset('storage/products/' . $product->id . '/small/'. $item->variant_id.'.webp') }}"
                                                         alt="">
                                                @else
                                                    <img class="w-full aspect-square hover:scale-105 transition-all"
                                                         src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                                         alt="">
                                                @endif
                                            </div>
                                            <div class="flex-1 px-2 sm:px-0 sm:py-4 text-right sm:text-center">
                                                <h5 class="text-xs font-bold">
                                                    {{ english_to_persian_num($product->title) }}
                                                </h5>
                                                <h5 class="text-xs sm:text-sm mt-2">
                                                    {{ english_to_persian_num(number_format($item->price_snapshot)) }}
                                                    تومان
                                                </h5>
                                                <h5 class="text-xs sm:text-sm mt-2">
                                                    تعداد {{ english_to_persian_num($item->quantity) }} عدد
                                                </h5>
                                                @if($item->variant_id)
                                                    <h5 class="text-xs sm:text-sm mt-2">
                                                        {{ $product->variant }}
                                                        : {{ \App\Models\ProductVariant::query()->find($item->variant_id)->name }}
                                                    </h5>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
