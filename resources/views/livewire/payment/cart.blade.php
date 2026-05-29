<div class="space-y-6">
    {{-- سفارشات در انتظار پرداخت --}}
    @if($orders->isNotEmpty())
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-200 bg-amber-100/50">
                <strong class="text-amber-800 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    شما یک سفارش در انتظار پرداخت دارید
                </strong>
            </div>
            <div class="p-4 space-y-3">
                @foreach($orders as $order)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="text-sm">
                            <span class="text-gray-500">شماره سفارش:</span>
                            <span class="font-bold text-gray-800">{{ english_to_persian_num($order->order_number) }}</span>
                            <span class="hidden sm:inline mx-2 text-gray-300">|</span>
                            <span class="text-gray-500">مبلغ قابل پرداخت:</span>
                            <span class="font-bold text-pars-700">{{ english_to_persian_num(number_format($order->amount)) }}</span>
                            <span class="text-xs text-gray-400">تومان</span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full">
                            <svg width="16" height="16" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="32" r="30" fill="#FF9800"/>
                                <path d="M32 18C30.9 18 30 18.9 30 20V36C30 37.1 30.9 38 32 38C33.1 38 34 37.1 34 36V20C34 18.9 33.1 18 32 18Z" fill="white"/>
                                <circle cx="32" cy="45" r="2.5" fill="white"/>
                            </svg>
                            <span>در صورت عدم پرداخت، این سفارش بزودی لغو خواهد شد.</span>
                        </div>

                        <a href="/dashboard/order?open={{ $order->order_number }}"
                           class="cursor-pointer text-sm bg-pars-500 hover:bg-pars-600 active:scale-95 text-white rounded-xl px-5 py-2 text-center transition-all duration-200 shadow-sm flex items-center justify-center gap-2"
                           wire:navigate>

                            جزئیات سفارش و پرداخت
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l-7 7 7 7"/>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- محتوای اصلی سبد خرید --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- جدول محصولات --}}
        <div class="w-full lg:w-2/3 overflow-x-auto">
            @if($cart)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-4 text-sm font-bold text-gray-700">تصویر</th>
                            <th class="px-5 py-4 text-sm font-bold text-gray-700">محصول</th>
                            <th class="px-5 py-4 text-sm font-bold text-gray-700 hidden lg:table-cell">قیمت</th>
                            <th class="px-5 py-4 text-sm font-bold text-gray-700 hidden lg:table-cell"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($cart as $key => $product)
                            <tr wire:key="{{$key}}" class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    @if($product['variant'])
                                        <a wire:navigate href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                            <img class="w-20 h-20 object-cover rounded-xl shadow-sm" src="{{ asset('storage/products/' . $product['id'] . '/small/' . $product['variant'] . '.webp') }}" alt="">
                                        </a>
                                    @else
                                        <a wire:navigate href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                            <img class="w-20 h-20 object-cover rounded-xl shadow-sm" src="{{ asset('storage/products/' . $product['id'] . '/small/1.webp') }}" alt="">
                                        </a>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <span class="font-bold text-gray-800 text-sm">{{ $product['title'] }} {{ english_to_persian_num($product['code']) }}</span>
                                            @if($product['variant'])
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ \App\Models\Product::query()->find($product['id'])->variant }}: {{ $product['variantName'] }}
                                                </div>
                                            @endif

                                        </div>

                                    </div>
                                    <div class="lg:hidden mt-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-500 text-sm">تعداد:</span>
                                            <span class="text-sm font-medium">{{ english_to_persian_num($product['quantity']) }}</span>
                                            <span class="text-pars-700 font-bold mr-3">{{ english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}</span>
                                            <span class="text-xs text-gray-400">تومان</span>
                                        </div>
                                    </div>

                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <span class="text-pars-700 font-bold text-lg">{{ english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}</span>
                                            <span class="text-xs text-gray-400 mr-1">تومان</span>
                                            @if($product['quantity'] > 1)
                                                <div class="text-xs text-gray-400">({{ english_to_persian_num(number_format($product['price'])) }} × {{ english_to_persian_num($product['quantity']) }})</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <button wire:click.prevent="removeFromCart('{{ $key }}')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-12">
                    <img class="w-32 mx-auto" src="{{ asset('images/cart2.png') }}" alt="">
                    <p class="pt-4 mb-8">سبد خرید شما خالی است</p>
                    <a href="{{ route('shop') }}" wire:navigate class="inline-block mt-4 bg-pars-500 hover:bg-pars-600 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all">
                        برو به فروشگاه
                    </a>
                </div>
            @endif
        </div>
        <div class="w-full h-fit lg:w-1/3  bg-white  border border-gray-100 p-5 shadow rounded-2xl mt-2 lg:mt-0 lg:mr-2 sticky top-20">
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>جمع مبلغ سبد خرید</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left"><span>{{ english_to_persian_num(number_format($sum)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>روش‌های ارسال</strong></div>
                <span>
                    <livewire:components.tooltip
                            text="در بخش تکمیل سفارش، شما می‌توانید روش ارسال دلخواه خود را انتخاب کنید. گزینه‌های موجود شامل تیپاکس (ارسال سریع‌تر) و پست پیشتاز (ارسال اقتصادی) می‌باشد." position="left"/>
                </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">پست پیشتاز یا تیپاکس</div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>هزینه ارسال</strong></div>
                <span>
                         <livewire:components.tooltip
                                 text="در روش پیش‌کرایه مبلغ ثابتی بابت هزینه ارسال به مبلغ قابل پرداخت شما اضافه خواهد شد ولی در روش پس‌کرایه هزینه ارسال سفارش بر اساس وزن و مسافت محاسبه شده و در هنگام تحویل کالا، به مامور پست پرداخت می‌شود. (روش پس‌کرایه از روش پیش‌کرایه ارزان‌تر است)" position="left"/>
                    </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>پیش‌کرایه یا پس‌کرایه</span>

                </div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>زمان ارسال</strong></div>
                <span>
                         <livewire:components.tooltip
                                 text="سفارش‌های ثبت شده تا قبل از ساعت ۱۰ صبحِ روزهای کاری، در همان روز و سفارش‌های ثبت شده بعد از این ساعت، در روز کاری بعد ارسال می‌گردند." position="left"/>
                    </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>هرروز ساعت ۱۰ صبح(غیرتعطیل)</span>
                </div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>بسته‌بندی</strong></div>
                <span>
                         <livewire:components.tooltip
                                 text="کلیه سفارش‌ها در کارتن‌های پستیِ سه‌لایه و استاندارد، با دقت بالا بسته‌بندی شده و فضاهای خالی داخل کارتن با ضربه‌گیر پر می‌شود تا محصول در سلامت کامل به دست شما برسد." position="left"/>
                    </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">
                    <span>کارتن‌های پستیِ سه‌لایه</span>
                </div>
            </div>
            <div class="w-full mb-16 flex justify-between">
                <div class=""><strong>زمان رسیدن به مقصد</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">{{ english_to_persian_num('بین 1 تا 3 روز') }}</div>
            </div>
            <div class="w-full lg:flex justify-between">
                <button wire:click.prevent="checkout"
                        class="w-full mt-6 bg-pars-500 hover:bg-pars-600 active:scale-95 text-white font-bold py-3 rounded-xl transition-all duration-200 shadow-sm flex items-center justify-center gap-2 cursor-pointer">

                    تکمیل سفارش
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l-7 7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>