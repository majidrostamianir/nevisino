<div>
    @foreach($orders as $order)
        <div wire:key="{{ $order->order_number }}"
             class="w-full shadow p-4 border border-pars-400 bg-white rounded mb-4 order-card">
            <div class="toggle-btn flex justify-between items-center cursor-pointer">
                <div>
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
                        <span>{{ english_to_persian_num(verta($order->created_at)->format('%d %B %Y - H:i:s')) }}</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>کد پیگیری سفارش: {{ english_to_persian_num($order->order_number) }}</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>جمع کل سفارش: {{ english_to_persian_num(number_format($order->total_price)) }} تومان</span>
                        <span class="mx-4  text-pars-400">&#9679;</span>
                        <span>حمل و نقل: {{ english_to_persian_num(number_format($order->shipping_price)) }} تومان</span>
                    </div>
                </div>

                <button class=" flex items-center cursor-pointer">
                    <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div class="order-details overflow-hidden transition-all duration-300 ease-in-out mt-2">
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
                        </div>
                        <div class="w-full sm:w-1/2">
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
                                                <span><span class="ml-4  text-orange-500">&#9679;</span>در انتظار پرداخت</span>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setupToggleButtons();
        });
        document.addEventListener("livewire:navigated", function () {
            setupToggleButtons();
        });


        function setupToggleButtons() {
            const toggleButtons = document.querySelectorAll('.toggle-btn');

            toggleButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const card = btn.closest('.order-card');
                    const details = card.querySelector('.order-details');
                    const arrow = btn.querySelector('svg');

                    // بستن سایر کارت‌ها
                    document.querySelectorAll('.order-card .order-details').forEach(d => {
                        if (d !== details) {
                            d.style.height = '0px';
                            d.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
                        }
                    });

                    // باز/بسته کردن کارت جاری با ارتفاع اتوماتیک
                    if (details.style.height && details.style.height !== '0px') {
                        details.style.height = '0px';
                        arrow.classList.remove('rotate-180');
                    } else {
                        details.style.height = details.scrollHeight + 'px';
                        arrow.classList.add('rotate-180');
                    }
                });
            });

            // تنظیم اولیه همه کارت‌ها
            document.querySelectorAll('.order-card .order-details').forEach(d => d.style.height = '0px');
        }
    </script>
</div>
