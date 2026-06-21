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

        {{-- لیست محصولات --}}
        <div class="w-full lg:w-2/3">
            @if($cart)
                <div class="space-y-3">
                    @foreach ($cart as $key => $product)
                        <div wire:key="{{$key}}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                            <div class="flex gap-4">
                                {{-- تصویر --}}
                                <div class="flex-shrink-0">
                                    @if($product['variant'])
                                        <a wire:navigate href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                            <img class="w-24 h-24 object-cover rounded-xl shadow-sm" src="{{ asset('storage/products/' . $product['id'] . '/small/' . $product['variant'] . '.webp') }}" alt="">
                                        </a>
                                    @else
                                        <a wire:navigate href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}">
                                            <img class="w-24 h-24 object-cover rounded-xl shadow-sm" src="{{ asset('storage/products/' . $product['id'] . '/small/1.webp') }}" alt="">
                                        </a>
                                    @endif
                                </div>

                                {{-- اطلاعات محصول --}}
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <a wire:navigate href="{{ route('product-page', ['title' => \App\Models\Product::query()->find($product['id'])->dashed_url, 'npi' => $product['id']]) }}"
                                               class="font-bold text-gray-800 text-sm hover:text-pars-600 transition-colors">
                                                {{ english_to_persian_num($product['title']) }} {{ english_to_persian_num($product['code']) }}
                                            </a>
                                            @if($product['variant'])
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ \App\Models\Product::query()->find($product['id'])->variant }}: {{ $product['variantName'] }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- دکمه حذف --}}
                                        <button wire:click.prevent="removeFromCart('{{ $key }}')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer flex-shrink-0 mr-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- قیمت و تعداد --}}
                                    <div class="flex justify-between items-end mt-4">
                                        <div class="text-xs text-gray-500">
                                            تعداد: <span class="font-medium">{{ english_to_persian_num($product['quantity']) }}</span>
                                        </div>
                                        <div class="text-left">
                                            <span class="text-pars-700 font-bold text-lg">{{ english_to_persian_num(number_format($product['quantity'] * $product['price'])) }}</span>
                                            <span class="text-xs text-gray-400 mr-1">تومان</span>
                                            @if($product['quantity'] > 1)
                                                <div class="text-xs text-gray-400 text-left">
                                                    ({{ english_to_persian_num(number_format($product['price'])) }} تومان × {{ english_to_persian_num($product['quantity']) }})
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

        {{-- سایدبار سمت راست --}}
        <div class="w-full lg:w-1/3 bg-white border border-gray-100 p-5 shadow rounded-2xl sticky top-20">
            <div class="w-full mb-4 flex justify-between">
                <div><strong>جمع مبلغ سبد خرید</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left"><span>{{ english_to_persian_num(number_format($sum)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div><strong>روش‌های ارسال</strong></div>
                <span>
                    <livewire:components.tooltip
                            text="در بخش تکمیل سفارش، شما می‌توانید روش ارسال دلخواه خود را انتخاب کنید. گزینه‌های موجود شامل تیپاکس (ارسال سریع‌تر) و پست پیشتاز (ارسال اقتصادی) می‌باشد." position="left"/>
                </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">پست پیشتاز یا تیپاکس</div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div><strong>هزینه ارسال</strong></div>
                <span>
                    <livewire:components.tooltip
                            text="در روش پیش‌کرایه مبلغ ثابتی بابت هزینه ارسال به مبلغ قابل پرداخت شما اضافه خواهد شد ولی در روش پس‌کرایه هزینه ارسال سفارش بر اساس وزن و مسافت محاسبه شده و در هنگام تحویل کالا، به مامور پست پرداخت می‌شود. (روش پس‌کرایه از روش پیش‌کرایه ارزان‌تر است)" position="left"/>
                </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">پیش‌کرایه یا پس‌کرایه</div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div><strong>زمان ارسال</strong></div>
                <span>
                    <livewire:components.tooltip
                            text="سفارش‌های ثبت شده تا قبل از ساعت ۱۰ صبحِ روزهای کاری، در همان روز و سفارش‌های ثبت شده بعد از این ساعت، در روز کاری بعد ارسال می‌گردند." position="left"/>
                </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">هرروز ساعت ۱۰ صبح(غیرتعطیل)</div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div><strong>بسته‌بندی</strong></div>
                <span>
                    <livewire:components.tooltip
                            text="کلیه سفارش‌ها در کارتن‌های پستیِ سه‌لایه و استاندارد، با دقت بالا بسته‌بندی شده و فضاهای خالی داخل کارتن با ضربه‌گیر پر می‌شود تا محصول در سلامت کامل به دست شما برسد." position="left"/>
                </span>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">کارتن‌های پستیِ سه‌لایه</div>
            </div>
            <div class="w-full mb-6 flex justify-between">
                <div><strong>زمان رسیدن به مقصد</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">{{ english_to_persian_num('بین 1 تا 3 روز') }}</div>
            </div>

            <button wire:click.prevent="checkout"
                    class="w-full bg-pars-500 hover:bg-pars-600 active:scale-95 text-white font-bold py-3 rounded-xl transition-all duration-200 shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                تکمیل سفارش
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l-7 7 7 7"/>
                </svg>
            </button>
        </div>
    </div>
</div>