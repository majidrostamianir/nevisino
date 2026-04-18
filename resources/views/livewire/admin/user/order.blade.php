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
        <div id="order-{{ $order->order_number }}"
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
                                    <p class="text-blue-600 font-bold pt-1">در انتظار تایید ...</p>
                                </div>
                            @else
                                <div class="flex mb-4">
                                    <p class="text-orange-300 font-bold pt-1">در انتظار پرداخت ...</p>
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
                            </div>
                            <div class="w-full sm:w-1/2">
                                <span class=" text-gray-400">توضیحات <span
                                        class="text-pars-700">
                                        {{ $order->description }}
                                 </div>
                        </div>
                        <div class="w-full sm:w-1/2">
                            @switch($order->shipping_status)
                                @case('pending')
                                    <div class="flex w-full">
                                        <div class="w-full">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-orange-300 font-bold">در انتظار پرداخت</span>
                                                <span class="text-xs">مبلغ: {{ english_to_persian_num(number_format($order->amount)) }} تومان</span>
                                            </div>
                                            <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                                <div class="bg-orange-300 h-2 rounded-full" style="width: 15%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @break
                                @case('processing')
                                    <div class="flex w-full">
                                        <div class="w-full">
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-sm text-green-500 font-bold">در حال پردازش سفارش</span>
                                                <span class="text-xs">مرحله بعد: بسته بندی</span>
                                            </div>
                                            <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: 25%"></div>
                                            </div>
                                        </div>
                                        <button wire:click.prevent="nextStep('{{ $order->id }}')"
                                                class="bg-pars-700 hover:bg-pars-800 w-fit text-nowrap mr-2 px-2 py-1 rounded-2xl  h-fit text-white cursor-pointer">
                                            مرحله بعد
                                        </button>
                                    </div>
                                    @break
                                @case('preparing')
                                    <div class="flex w-full">
                                        <div class="w-full">
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-sm text-green-500 font-bold">در حال بسته بندی و ارسال</span>
                                                <span class="text-xs">مرحله بعد: تحویل به اداره پست</span>
                                            </div>
                                            <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: 40%"></div>
                                            </div>
                                        </div>
                                        <button wire:click.prevent="nextStep('{{ $order->id }}')"
                                                class="bg-pars-700 hover:bg-pars-800 w-fit text-nowrap mr-2 px-2 py-1 rounded-2xl  h-fit text-white cursor-pointer">
                                            مرحله بعد
                                        </button>
                                    </div>
                                    @break
                                @case('shipped')
                                    <div class="flex w-full">
                                        <div class="w-full">
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-sm text-green-500 font-bold">در حال ارسال به آدرس شما</span>
                                                <span class="text-xs">مرحله بعد: تحویل کالا به مشتری</span>
                                            </div>
                                            <div class="w-full mt-2 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: 70%"></div>
                                            </div>
                                            <div class="flex w-full ">
                                                <span class="text-sm text-nowrap mt-2">کد پیگیری مرسوله</span>
                                                <input type="text"
                                                       wire:model.defer="trackingCodes.{{ $order->id }}"
                                                       class="bg-white w-full rounded-2xl border py-0 px-2 mt-0.5 mr-2">
                                                @error("trackingCodes.$order->id")
                                                <span class="text-red-500">{{ $message }}</span>
                                                @enderror

                                                <button wire:click.prevent="saveTrackingCode('{{ $order->id }}')"
                                                        class="bg-pars-700 hover:bg-pars-800 px-3 py-1 rounded-2xl text-white cursor-pointer ml-2">
                                                    دخیره و ارسال پیامک
                                                </button>
                                            </div>
                                        </div>
                                        <button wire:click.prevent="nextStep('{{ $order->id }}')"
                                                class="bg-pars-700 hover:bg-pars-800 w-fit text-nowrap mr-2 px-2 py-1 rounded-2xl  h-fit text-white cursor-pointer">
                                            مرحله بعد
                                        </button>
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
                            <div class="p-2 border-pars-400 mb-8 rounded bg-pars-200">
                                @foreach($order->transactions->sortByDesc('created_at') as $key => $value)
                                    <div class="pb-2 last:pb-0 flex flex-wrap items-center">
                                        @switch($value->status)
                                            @case('pending')
                                                <span><span
                                                        class="ml-4  text-orange-500">&#9679;</span>در انتظار پرداخت</span>
                                                @break
                                            @case('success')
                                                <span><span
                                                        class="ml-4  text-green-500">&#9679;</span>پرداخت موفق</span>
                                                @break
                                            @case('failed')
                                                <span><span
                                                        class="ml-4  text-red-500">&#9679;</span>پرداخت ناموفق</span>
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
                                        <div >
                                            <span class="mx-4  text-pars-400">&#9679;</span>
                                            <span> {{ english_to_persian_num(verta($value->created_at)->format('%d %B %Y ساعت H:i:s')) }} </span>
                                        </div>
                                        @if($value->status !== "success")
                                                <div >
                                                    <span class="mx-4  text-pars-400">&#9679;</span>
                                                    <button wire:click.prevent="verifyTransaction('{{$value->id}}')"
                                                            class="cursor-pointer rounded-2xl px-2 py-1 bg-green-400 text-white text-sm">چک کردن موجودی و تایید تراکنش و کاهش موجودی</button>

                                                    <button wire:click.prevent="failedTransaction('{{$value->id}}')"
                                                            class="cursor-pointer rounded-2xl px-2 py-1 bg-red-400 text-white text-sm">عدم تایید تراکنش</button>
                                                </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex flex-wrap">
                                @foreach($order->items as $item)
                                        <?php
                                        $product = \App\Models\Product::query()->find($item->product_id)
                                        ?>
                                    <div class="w-48 ml-2 mb-2">
                                        <a href="{{ route('product-page' , ['title' => $product->dashed_url , 'npi'=>$product->id]) }}"
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
                                                     {{ english_to_persian_num($item->quantity) }} عدد
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
