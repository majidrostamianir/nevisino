<div>
    <div class="w-full">
        @if($orders->isNotEmpty())
            <div class="bg-pars-100 rounded-2xl mb-4 p-4">
                <strong class="block mb-3 text-sm sm:text-base">
                    شما یک سفارش در انتظار پرداخت دارید
                </strong>
                
                @foreach($orders as $order)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between
                    border border-gray-300 w-full p-4 my-2 rounded-lg">
                        
                        <!-- اطلاعات سفارش -->
                        <div class="text-sm">
                            شماره سفارش:
                            {{ english_to_persian_num($order->order_number) }}
                            <span class="mx-2 text-pars-400 hidden sm:inline">&#9679;</span>
                            مبلغ قابل پرداخت:
                            {{ english_to_persian_num(number_format($order->amount)) }}
                            تومان
                        </div>
                        
                        <!-- هشدار -->
                        <div class="flex items-center gap-2 text-xs text-gray-700">
                            <svg width="18" height="18" viewBox="0 0 64 64" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                                <circle cx="32" cy="32" r="30" fill="#FF9800"/>
                                <path
                                    d="M32 18C30.9 18 30 18.9 30 20V36C30 37.1 30.9 38 32 38C33.1 38 34 37.1 34 36V20C34 18.9 33.1 18 32 18Z"
                                    fill="white"/>
                                <circle cx="32" cy="45" r="2.5" fill="white"/>
                            </svg>
                            <span>در صورت عدم پرداخت، این سفارش بزودی لغو خواهد شد.</span>
                        </div>
                        
                        <!-- دکمه‌ها -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <a href="/dashboard/order?open={{ $order->order_number }}"
                               class="cursor-pointer text-xs sm:text-sm bg-pars-500 text-white rounded px-4 py-1 text-center"
                               wire:navigate>
                                جزئیات سفارش و پرداخت
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="flex flex-col lg:flex-row gap-2">
        <div class="w-full lg:w-2/3 overflow-x-auto">
            @if($cart)
                <table class="w-full text-right bg-pars-100 rounded-2xl">
                    <tbody>
                    @foreach ($cart as $key => $product)
                        <tr wire:key="{{$key}}" class="border-b border-b-gray-200 last:border-0">
                            <td class="px-4 py-2">
                                @if($product['variant'])
                                    <a wire:navigate
                                       href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                        <img class="w-24 rounded"
                                             src="{{ asset('storage/products/' . $product['id'] . '/small/' . $product['variant'] . '.webp') }}"
                                             alt="">
                                    </a>
                                @else
                                    <a wire:navigate
                                       href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                        <img class="w-24 rounded"
                                             src="{{ asset('storage/products/' . $product['id'] . '/small/1.webp') }}"
                                             alt="">
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    class="font-bold">{{ $product['title'] }} {{ english_to_persian_num($product['code']) }}</span>
                                @if($product['variant'])
                                    <br>
                                    <span class="text-sm">
                                    {{ \App\Models\Product::query()->find($product['id'])->variant }}:
                                    {{ $product['variantName'] }}
                                </span>
                                @endif
                                <br>
                                <div class="lg:hidden pt-2">
                                    @if($product['quantity'] > 1)
                                        {{ english_to_persian_num(number_format($product['price'])) }}
                                        × {{ english_to_persian_num($product['quantity']) }}
                                    @else
                                        {{ english_to_persian_num(number_format($product['price'])) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 hidden lg:table-cell">
                                @if($product['quantity'] > 1)
                                    {{ english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}
                                    = {{ english_to_persian_num($product['quantity']) }}
                                    × {{ english_to_persian_num(number_format($product['price'])) }}
                                @else
                                    {{ english_to_persian_num(number_format($product['price'])) }}
                                @endif
                            </td>
                            <td class="px-4 py-2">
                            <span wire:click.prevent="removeFromCart('{{ $key }}')"
                                  class="font-bold bg-pars-500 transition-all duration-300 hover:bg-red-500 text-white cursor-pointer rounded-full px-2">×</span>
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
        
        <div class="w-full h-fit lg:w-1/3 p-4 bg-pars-100 shadow rounded-2xl mt-2 lg:mt-0 lg:mr-2 sticky top-20">
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>جمع مبلغ سبد خرید</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left"><span>{{ english_to_persian_num(number_format($sum)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>روش‌های ارسال</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">پست پیشتاز یا تیپاکس</div>
                <span>
                    <livewire:components.tooltip
                        text="در بخش تکمیل سفارش، شما می‌توانید روش ارسال دلخواه خود را انتخاب کنید. گزینه‌های موجود شامل تیپاکس (ارسال سریع‌تر) و پست پیشتاز (ارسال اقتصادی) می‌باشد." position="right"/>
                </span>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>هزینه ارسال</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>پیش‌کرایه یا پس‌کرایه</span>
                    <span>
                         <livewire:components.tooltip
                             text="در روش پیش‌کرایه مبلغ ثابتی بابت هزینه ارسال به مبلغ قابل پرداخت شما اضافه خواهد شد ولی در روش پس‌کرایه هزینه ارسال سفارش بر اساس وزن و مسافت محاسبه شده و در هنگام تحویل کالا، به مامور پست پرداخت می‌شود. (روش پس‌کرایه از روش پیش‌کرایه ارزان‌تر است)" position="right"/>
                    </span>
                </div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>زمان ارسال</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>هرروز ساعت ۱۰ صبح(غیرتعطیل)</span>
                    <span>
                         <livewire:components.tooltip
                             text="سفارش‌های ثبت شده تا قبل از ساعت ۱۰ صبحِ روزهای کاری، در همان روز و سفارش‌های ثبت شده بعد از این ساعت، در روز کاری بعد ارسال می‌گردند." position="right"/>
                    </span>
                </div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>بسته‌بندی</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>کارتن‌های پستیِ سه‌لایه</span>
                    <span>
                         <livewire:components.tooltip
                             text="کلیه سفارش‌ها در کارتن‌های پستیِ سه‌لایه و استاندارد، با دقت بالا بسته‌بندی شده و فضاهای خالی داخل کارتن با ضربه‌گیر پر می‌شود تا محصول در سلامت کامل به دست شما برسد." position="right"/>
                    </span>
                </div>
            </div>
            <div class="w-full mb-16 flex justify-between">
                <div class=""><strong>زمان رسیدن به مقصد</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">{{ english_to_persian_num('بین 1 تا 3 روز') }}</div>
            </div>
            <div class="w-full lg:flex justify-between">
                <button wire:click.prevent="checkout"
                        class="w-full text-center bg-pars-700 hover:bg-pars-800 text-white rounded-2xl py-1 cursor-pointer">
                    تکمیل سفارش
                </button>
            </div>
        </div>
    
    </div>
</div>