<div class="space-y-6"
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
                    const isDesktop = window.innerWidth >= 1024
                    const offset = isDesktop ? 80 : 0
                    const top = el.getBoundingClientRect().top + window.pageYOffset - offset
                    window.scrollTo({ top, behavior: 'smooth' })
                })
            }
        }
    }">

    @foreach($orders as $order)
        <div id="order-{{ $order->order_number }}"
             wire:key="{{ $order->order_number }}"
             class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div @click="openOrderNumber = (openOrderNumber === '{{ $order->order_number }}' ? null : '{{ $order->order_number }}')"
                 class="cursor-pointer select-none transition-colors duration-200 hover:bg-gray-50">
                <div class="p-5 lg:p-6">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0 space-y-3">
                            {{--  وضعیت --}}
                            @switch($order->status)
                                @case('pending')
                                    @if($order->transactions()->where('payment_gateway','card')->where('status','pending')->exists())
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-orange-50 rounded-full">
                                            <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                            <span class="text-orange-600 font-bold text-sm">در انتظار تایید پشتیبانی</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 rounded-full">
                                            <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                                            <span class="text-amber-600 font-bold text-sm">در انتظار پرداخت</span>
                                        </div>
                                    @endif
                                    @break
                                @case('paid')
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-green-600 font-bold text-sm">پرداخت شده</span>
                                    </div>
                                    @break
                                @case('canceled')
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span class="text-gray-500 font-bold text-sm">لغو شده</span>
                                    </div>
                                    @break
                            @endswitch
                            {{-- اطلاعات متا --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600">

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ english_to_persian_num(verta($order->created_at)->format('%d %B %Y')) }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span>{{ english_to_persian_num(verta($order->created_at)->format('H:i:s')) }}</span>
                                </div>

                                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    <span>کد پیگیری: {{ english_to_persian_num($order->order_number) }}</span>
                                </div>

                                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>جمع کل: <strong
                                                class="text-pars-700 font-bold">{{ english_to_persian_num(number_format($order->total_price)) }}</strong> تومان</span>
                                </div>

                                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" stroke-width="1.8">
                                        <rect x="1" y="8" width="13" height="11" rx="1.5"/>
                                        <path d="M14 10h4l3 4v5h-7V10z"/>
                                        <circle cx="5.5" cy="19.5" r="1.5"/>
                                        <circle cx="18.5" cy="19.5" r="1.5"/>
                                    </svg>
                                    <span>ارسال: {{ $order->shipping_method->label() }}</span>
                                    @if($order->shipping_method->isCashOnDelivery())
                                        <span class="text-gray-400">({{ english_to_persian_num(number_format($order->shipping_price)) }} تومان)</span>
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- دکمه اکاردئون --}}
                        <div class="flex justify-center lg:justify-end w-full lg:w-auto flex-shrink-0 mt-4 lg:mt-0">
                            <button class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-all duration-200">
                                <svg
                                        x-bind:class="{'rotate-180': openOrderNumber === '{{ $order->order_number }}'}"
                                        class="w-5 h-5 text-gray-600 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ───── جزئیات (accordion) ───── --}}
            <div x-show="openOrderNumber === '{{ $order->order_number }}'"
                 x-collapse
                 class="border-t border-gray-100 bg-gray-50/30">
                <div class="p-5 lg:p-6 space-y-8">
                    {{-- اطلاعات گیرنده + آدرس --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- گیرنده --}}
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <h3 class="text-pars-700 font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                اطلاعات تحویل گیرنده
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">نام و نام خانوادگی:</span>
                                    <span class="font-medium text-gray-800">{{ english_to_persian_num($order->recipient_name) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">شماره موبایل:</span>
                                    <span class="font-medium text-gray-800">{{ english_to_persian_num($order->recipient_mobile) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- آدرس --}}
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <h3 class="text-pars-700 font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                آدرس ارسال
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500 flex-shrink-0">استان و شهر:</span>
                                    <span class="font-medium text-gray-800">{{ $order->province }} - {{ $order->city }}</span>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500 flex-shrink-0">آدرس پستی:</span>
                                    <span class="font-medium text-gray-800 text-left leading-relaxed">{{ english_to_persian_num($order->postal_address) }}</span>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500 flex-shrink-0">کد پستی:</span>
                                    <span class="font-medium text-gray-800">{{ english_to_persian_num($order->zipcode) }}</span>
                                </div>
                                @if($order->description)
                                    <div class="flex justify-between gap-4">
                                        <span class="text-gray-500 flex-shrink-0">توضیحات:</span>
                                        <span class="font-medium text-gray-800 text-left">{{ english_to_persian_num($order->description) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ───── وضعیت ارسال با stepper ───── --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-3">
                            <h3 class="text-pars-700 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     stroke-width="1.8">
                                    <rect x="1" y="8" width="13" height="11" rx="1.5"/>
                                    <path d="M14 10h4l3 4v5h-7V10z"/>
                                    <circle cx="5.5" cy="19.5" r="1.5"/>
                                    <circle cx="18.5" cy="19.5" r="1.5"/>
                                </svg>
                                وضعیت سفارش
                            </h3>
                        </div>
                        @php
                            $orderSteps = [
                                ['key' => 'register',    'label' => 'ثبت سفارش'],
                                ['key' => 'pending',     'label' => 'در انتظار پرداخت'],
                                ['key' => 'processing',  'label' => 'پردازش سفارش'],
                                ['key' => 'preparing',   'label' => 'بسته‌بندی'],
                                ['key' => 'shipped',     'label' => 'در مسیر مقصد'],
                                ['key' => 'delivered',   'label' => 'تحویل داده شده'],
                            ];
                            $stepKeys      = array_column($orderSteps, 'key');
                            $currentStatus = $order->shipping_status ?? 'register';
                            $currentIdx    = array_search($currentStatus, $stepKeys);
                            if ($currentIdx === false) $currentIdx = 0;
                        @endphp

                        <div class="relative flex items-start justify-between my-5">
                            {{-- خط پس‌زمینه --}}
                            <div class="absolute top-4 right-[10%] left-[10%] h-0.5 bg-gray-100"></div>

                            {{-- خط پیشرفت --}}
                            @if($currentIdx > 0)
                                <div class="absolute top-4 right-[10%] h-0.5 bg-green-400 transition-all duration-700"
                                     style="width: calc({{ ($currentIdx / (count($orderSteps) - 1)) * 80 }}%);"></div>
                            @endif

                            @foreach($orderSteps as $i => $step)
                                <div class="relative flex flex-col items-center gap-1.5 z-10"
                                     style="width: {{ 100 / count($orderSteps) }}%">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300
                @if($i < $currentIdx) bg-green-500 border-green-500 text-white
                @elseif($i === $currentIdx)
                    @if($currentStatus === 'delivered') bg-green-500 border-green-500 text-white
                    @else bg-white border-green-500 text-green-600 shadow-sm
                    @endif
                @else bg-white border-gray-200 text-gray-300 @endif">
                                        @if($i <= $currentIdx)
                                            {{-- تیک برای وضعیت‌های قبلی و همچنین وضعیت فعلی اگر تحویل شده باشد --}}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            {{ english_to_persian_num($i + 1) }}
                                        @endif
                                    </div>
                                    <span class="text-center text-xs leading-tight px-1
                @if($i <= $currentIdx) text-gray-700 font-semibold @else text-gray-300 @endif">
                {{ $step['label'] }}
            </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- لینک پیگیری (در صورت وجود) --}}
                        @if(($order->shipping_status === 'shipped' || $currentStatus === 'shipped') && !empty($order->tracking_code))
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="https://tracking.post.ir/?id={{ $order->tracking_code }}"
                                   target="_blank" rel="nofollow noopener noreferrer"
                                   class="inline-flex items-center gap-2 text-sm text-pars-600 hover:text-pars-700 underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    کد پیگیری مرسوله: {{ english_to_persian_num($order->tracking_code) }}
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($order->status == 'pending' )
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"
                             x-data="{
                                    payment_method: @entangle('payment_method').live,
                                    copied: false,
                                    copyCard() {
                                        navigator.clipboard.writeText('5022291533610273');
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1500);
                                    }}">
                            <h3 class="text-pars-700 font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                روش پرداخت
                            </h3>

                            <div class="flex flex-col gap-3">
                                <div class="flex flex-col lg:flex-row gap-3">
                                    <label class="flex flex-1 items-center bg-white px-4 py-2 rounded-xl border-2 cursor-pointer transition-all duration-300"
                                           :class="payment_method === 'gateway' ? 'border-pars-500 shadow-md' : 'border-gray-200'">
                                        <input type="radio" value="gateway" x-model="payment_method" class="hidden">
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3"
                                              :class="payment_method === 'gateway' ? 'border-pars-500' : 'border-gray-300'">
                                            <span class="w-2.5 h-2.5 rounded-full bg-pars-500"
                                                  x-show="payment_method === 'gateway'"></span>
                                        </span>
                                        <span class="text-sm">پرداخت از طریق درگاه بانکی با رمز دوم</span>
                                    </label>
                                    <label class="flex items-center bg-white px-4 py-2 rounded-xl border-2 cursor-pointer transition-all duration-300 lg:flex-1"
                                           :class="payment_method === 'card' ? 'border-pars-500 shadow-md' : 'border-gray-200'">
                                        <input type="radio" value="card" x-model="payment_method" class="hidden">
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3"
                                              :class="payment_method === 'card' ? 'border-pars-500' : 'border-gray-300'">
                                            <span class="w-2.5 h-2.5 rounded-full bg-pars-500"
                                                  x-show="payment_method === 'card'"></span>
                                        </span>
                                        <span class="text-sm">پرداخت از طریق کارت به کارت</span>
                                    </label>

                                </div>

                                <div x-show="payment_method === 'card'" x-collapse>
                                    <div class="w-full max-w-xs mx-auto bg-gradient-to-br from-gray-900 to-black rounded-2xl relative text-white overflow-visible border border-gray-700 mt-2"
                                         style="aspect-ratio:1.7;">
                                        <div x-show="copied" x-transition.opacity
                                             class="absolute top-5 left-5 font-bold text-[11px] bg-green-500 text-white px-3 py-1 rounded-full shadow-md z-10">
                                            کپی شد ✓
                                        </div>
                                        <img src="{{ asset('images/Pasargad.png') }}"
                                             class="absolute top-3 right-3 w-10 opacity-90" alt="">
                                        <div class="absolute top-4 left-1/2 -translate-x-1/2 text-sm font-semibold tracking-wide text-white/80">
                                            بانک پاسارگاد
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <button type="button" @click.stop="copyCard"
                                                    class="font-mono text-base md:text-lg tracking-[0.2em] md:tracking-[0.3em] cursor-pointer transition duration-200 hover:scale-105">
                                                5022 2915 3361 0273
                                            </button>
                                        </div>
                                        <div class="absolute top-[60%] right-4 text-xs font-semibold text-white/70">مجید
                                            رستمیان
                                        </div>
                                        <div class="absolute bottom-3 right-4 left-4 text-[10px] text-yellow-400 leading-tight">
                                            لطفاً مبلغ {{ english_to_persian_num(number_format($order->amount)) }} تومان
                                            را واریز کرده، سپس دکمه زیر را بزنید.
                                        </div>
                                    </div>
                                </div>

                                {{-- دکمه پرداخت جدا از گزینه‌ها --}}
                                <button wire:click.prevent="pay('{{ $order->id }}')" wire:target="pay"
                                        class="w-full lg:w-64 lg:mx-auto h-10 cursor-pointer bg-pars-500 hover:bg-pars-600 active:scale-95 text-white rounded-xl flex items-center justify-center gap-2 transition-all duration-200 font-bold text-sm shadow-sm">
                                    <span wire:loading.remove wire:target="pay">
                                        <span x-text="payment_method === 'card' ? 'ثبت واریزی' : 'پرداخت مجدد'"></span>
                                    </span>
                                    <span wire:loading wire:target="pay" class="flex items-center justify-center">
                                        <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                                        </svg>
                                    </span>
                                </button>

                            </div>
                            @error('payment_method')
                            <div class="mt-2 text-red-500 text-xs">{{ english_to_persian_num($message) }}</div>
                            @enderror
                        </div>
                    @endif

                    {{-- ───── تاریخچه تراکنش‌ها ───── --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-3">
                            <h3 class="text-pars-700 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                تاریخچه تراکنش‌ها
                            </h3>
                        </div>

                        <div class="p-5">
                            @if(!$order->transactions()->exists())
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>هیچ تراکنشی انجام نشده است.</span>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($order->transactions->sortByDesc('created_at') as $value)
                                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-all duration-200">
                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <div class="flex items-center gap-3">
                                                    @switch($value->status)
                                                        @case('pending')
                                                            @if($value->payment_gateway == 'card')
                                                                <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                                                                <span class="text-orange-600 text-sm font-medium">در انتظار تایید</span>
                                                            @else
                                                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                                                <span class="text-amber-600 text-sm font-medium">در انتظار پرداخت</span>
                                                            @endif
                                                            @break
                                                        @case('success')
                                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                            <span class="text-green-600 text-sm font-medium">پرداخت موفق</span>
                                                            @break
                                                        @case('failed')
                                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                            <span class="text-red-600 text-sm font-medium">پرداخت ناموفق</span>
                                                            @break
                                                        @case('cancel')
                                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                                            <span class="text-gray-500 text-sm font-medium">لغو شده</span>
                                                            @break
                                                    @endswitch
                                                </div>

                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="text-gray-500">
                                                        @switch($value->payment_gateway)
                                                            @case('card') کارت به کارت @break
                                                            @case('torobpay') ترب‌پی @break
                                                            @default درگاه بانکی
                                                        @endswitch
                                                    </span>

                                                    @if($value->payment_gateway == 'card' && $value->authority)
                                                        <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">
                                                            واریز به: {{ english_to_persian_num($value->authority) }}
                                                        </span>
                                                    @elseif($value->payment_gateway == 'gateway' && $value->authority)
                                                        <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">
                                                            کد پیگیری: {{ english_to_persian_num($value->authority) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2 mr-auto">
                                                    <span class="text-pars-700 font-bold">{{ english_to_persian_num(number_format($value->amount)) }}</span>
                                                    <span class="text-xs text-gray-400">تومان</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5 mt-2 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ english_to_persian_num(verta($value->created_at)->format('%d %B %Y ساعت H:i:s')) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- ───── محصولات سفارش ───── --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden pb-30">
                        <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-3">
                            <h3 class="text-pars-700 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                محصولات سفارش
                            </h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 p-5">
                            @foreach($order->items as $item)
                                @php
                                    $product = \App\Models\Product::query()->find($item->product_id)
                                @endphp
                                <a href="{{ route('product-page', ['title' => $product->dashed_url, 'npi' => $product->id]) }}"
                                   class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                                    <div class="relative overflow-hidden bg-gray-100">
                                        @if($item->variant_id)
                                            <img class="w-full aspect-square object-cover group-hover:scale-105 transition-transform duration-300"
                                                 src="{{ asset('storage/products/' . $product->id . '/small/'. $item->variant_id.'.webp') }}"
                                                 alt="{{ $product->title }}">
                                        @else
                                            <img class="w-full aspect-square object-cover group-hover:scale-105 transition-transform duration-300"
                                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                                 alt="{{ $product->title }}">
                                        @endif
                                    </div>
                                    <div class="p-3 text-center">
                                        <h5 class="text-xs font-bold text-gray-800 line-clamp-2 min-h-[2.5rem]">
                                            {{ english_to_persian_num($product->title) }}
                                        </h5>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-pars-700 font-bold text-sm">
                                                {{ english_to_persian_num(number_format($item->price_snapshot)) }}
                                                <span class="text-xs font-normal text-gray-400">تومان</span>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                تعداد: {{ english_to_persian_num($item->quantity) }} عدد
                                            </p>
                                            @if($item->variant_id)
                                                <p class="text-xs text-gray-400 line-clamp-1">
                                                    {{ $product->variant }}
                                                    : {{ \App\Models\ProductVariant::query()->find($item->variant_id)->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>