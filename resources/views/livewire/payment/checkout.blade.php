<div
        x-data="{ showPopup: @entangle('showPopup').live }"
        x-cloak
        x-on:close-popup.window="showPopup = false">
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
         x-show="showPopup"
         x-transition.opacity.duration.200ms
         @click="showPopup = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center border-b border-gray-100 px-5 py-4">
                <strong class="text-pars-700 font-bold text-lg">آدرس‌های شما</strong>
                <button @click="showPopup = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 max-h-80 overflow-y-auto space-y-3">
                @foreach($addresses as $value)
                    <div wire:click.prevent="selectAddress('{{ $value->id }}')" @click="showPopup = false"
                         class="border rounded-xl p-3 cursor-pointer transition-all duration-200"
                         :class="{
                     'border-pars-500 bg-pars-50 shadow-sm': '{{ $value->id }}' == '{{ $selectedAddress->id ?? '' }}',
                     'border-gray-200 hover:border-pars-300 bg-white': '{{ $value->id }}' != '{{ $selectedAddress->id ?? '' }}'
                 }">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-pars-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-pars-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 text-sm leading-relaxed">{{ english_to_persian_num($value['postal_address']) }}</p>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 mt-2 text-xs">
                                    <span class="text-gray-500">کد پستی: {{ english_to_persian_num($value['zipcode']) }}</span>
                                    <span class="text-gray-500">گیرنده: {{ english_to_persian_num($value['recipient_name']) }}</span>
                                    <span class="text-gray-500">{{ english_to_persian_num($value['recipient_mobile']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 px-5 py-4">
                <button wire:click.prevent="changeAddress()" @click="showPopup = false"
                        class="text-pars-600 hover:text-pars-700 font-medium text-sm flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    افزودن آدرس جدید
                </button>
            </div>
        </div>
    </div>
    <div class="lg:flex ">
        @if($selectedAddress)
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-20">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center justify-between cursor-pointer" @click="showPopup = true">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-bold text-gray-700">آدرس گیرنده</span>
                            </div>
                            <span class="text-xs text-pars-500 flex items-center gap-1">
                                تغییر آدرس
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">نام گیرنده:</span>
                                <span class="font-medium text-gray-800">{{ english_to_persian_num($selectedAddress->recipient_name) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">شماره موبایل:</span>
                                <span class="font-medium text-gray-800">{{ english_to_persian_num($selectedAddress->recipient_mobile) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">استان و شهر:</span>
                                <span class="font-medium text-gray-800">{{ $selectedAddress->province->name }} - {{ $selectedAddress->city->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">کد پستی:</span>
                                <span class="font-medium text-gray-800">{{ english_to_persian_num($selectedAddress->zipcode) }}</span>
                            </div>
                        </div>

                        <p class="text-gray-800 text-sm leading-relaxed border-gray-100 pb-3">
                            {{ english_to_persian_num($selectedAddress->postal_address) }}
                        </p>


                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="text-sm text-gray-600 block mb-2">توضیحات اختیاری:</label>
                            <textarea x-data="faNumber('description', false)" x-model="value" @input="onInput"
                                      rows="2"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all resize-none"
                                      placeholder="توضیحات خود را وارد کنید..."></textarea>
                            @error('description') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-20">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <strong class="text-pars-700 font-bold">مشخصات و آدرس دریافت کننده</strong>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نام و نام خانوادگی <span
                                        class="text-red-500">*</span></label>
                            <input wire:model="recipient_name"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400">
                            @error('recipient_name') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">شماره موبایل <span
                                        class="text-red-500">*</span></label>
                            <input x-data="faNumber('recipient_mobile', true)" x-model="value" @input="onInput"
                                   inputmode="numeric"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400">
                            @error('recipient_mobile') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">استان <span
                                        class="text-red-500">*</span></label>
                            <select wire:model.live="province_id"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400">
                                <option value="">استان خود را انتخاب کنید</option>
                                @foreach(\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('province_id') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">شهر <span
                                        class="text-red-500">*</span></label>
                            <select wire:model.live="city_id"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400">
                                <option value="">شهر خود را انتخاب کنید</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">آدرس دقیق پستی <span
                                        class="text-red-500">*</span></label>
                            <textarea x-data="faNumber('postal_address', false)" x-model="value" @input="onInput"
                                      rows="2"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 resize-none"></textarea>
                            @error('postal_address') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">کد پستی <span
                                        class="text-red-500">*</span></label>
                            <input x-data="faNumber('zipcode', true)" x-model="value" @input="onInput"
                                   inputmode="numeric"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400">
                            @error('zipcode') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات اختیاری</label>
                            <textarea x-data="faNumber('description', false)" x-model="value" @input="onInput" rows="2"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 resize-none"></textarea>
                            @error('description') <p
                                    class="text-red-500 text-xs mt-1">{{ english_to_persian_num($message) }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="lg:sticky top-20 w-full h-fit lg:w-1/3 mt-2 lg:mt-0 lg:mr-2"
             x-data="{
        shipping_method: @entangle('shipping_method').live ?? 'post_cod'
    }"
             x-cloak>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             stroke-width="1.8">
                            <rect x="1" y="8" width="13" height="11" rx="1.5"/>
                            <path d="M14 10h4l3 4v5h-7V10z"/>
                            <circle cx="5.5" cy="19.5" r="1.5"/>
                            <circle cx="18.5" cy="19.5" r="1.5"/>
                        </svg>
                        <strong class="text-pars-700 font-bold">انتخاب روش ارسال</strong>
                        <livewire:components.tooltip
                                text="در روش پس‌کرایه، هزینه ارسال را درب منزل به پستچی پرداخت می‌کنید. اما در روش پیش‌کرایه، مبلغ هزینه ارسال را به همراه مبلغ کالاها به سایت پرداخت می‌کنید و دیگر در زمان تحویل گرفتن، هیچگونه مبلغی پرداخت نمی‌کنید. (روش پس‌کرایه از روش پیش‌کرایه ارزان‌تر است)"
                                position="bottom"/>
                    </div>
                </div>

                <div class="p-5 space-y-3">
                    <label class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="shipping_method === 'post_cod' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">

                        <input type="radio" value="post_cod" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('post_cod')">
                        <div class="flex items-center">
                    <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                          :class="shipping_method === 'post_cod' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"
                              x-show="shipping_method === 'post_cod'"></span>
                    </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-800">{{ \App\Enums\ShippingMethodEnum::POST_COD->label() }}</span>
                                    <span class="text-xs text-green-600 font-bold">(پیشنهاد ما)</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ \App\Enums\ShippingMethodEnum::POST_COD->description() }}</div>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="shipping_method === 'post_cash' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">

                        <input type="radio" value="post_cash" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('post_cash')">
                        <div class="flex items-center">
                    <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                          :class="shipping_method === 'post_cash' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"
                              x-show="shipping_method === 'post_cash'"></span>
                    </span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ \App\Enums\ShippingMethodEnum::POST_CASH->label() }}</span>
                                <div class="text-xs text-gray-500">{{ \App\Enums\ShippingMethodEnum::POST_CASH->description() }}</div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-pars-700">{{ english_to_persian_num(number_format(config('shop.post_price'))) }}
                            تومان
                        </div>
                    </label>

                    <label class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="shipping_method === 'tipax_cod' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">

                        <input type="radio" value="tipax_cod" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('tipax_cod')">
                        <div class="flex items-center">
                    <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                          :class="shipping_method === 'tipax_cod' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"
                              x-show="shipping_method === 'tipax_cod'"></span>
                    </span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ \App\Enums\ShippingMethodEnum::TIPAX_COD->label() }}</span>
                                <div class="text-xs text-gray-500">{{ \App\Enums\ShippingMethodEnum::TIPAX_COD->description() }}</div>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="shipping_method === 'tipax_cash' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">

                        <input type="radio" value="tipax_cash" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('tipax_cash')">
                        <div class="flex items-center">
                    <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                          :class="shipping_method === 'tipax_cash' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"
                              x-show="shipping_method === 'tipax_cash'"></span>
                    </span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ \App\Enums\ShippingMethodEnum::TIPAX_CASH->label() }}</span>
                                <div class="text-xs text-gray-500">{{ \App\Enums\ShippingMethodEnum::TIPAX_CASH->description() }}</div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-pars-700">{{ english_to_persian_num(number_format(config('shop.tipax_price'))) }}
                            تومان
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="lg:sticky top-20 w-full h-fit lg:w-1/3 mt-2 lg:mt-0 lg:mr-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden "
                 x-data="{
                    payment_method: @entangle('payment_method').live,
                    copied: false,
                    copyCard() {
                        navigator.clipboard.writeText('5022291533610273');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 1500);
                    }
                 }">
                <div class="border-b border-gray-100 bg-gray-50/50 px-5 py-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <strong class="text-pars-700 font-bold">انتخاب روش پرداخت</strong>
                    </div>
                </div>
                <div class="px-5 pt-5">
                    <div class="flex justify-between items-center pb-4 border-gray-100">
                        <span class="text-gray-600">مبلغ قابل پرداخت</span>
                        <span class="text-pars-700 font-bold text-2xl">{{ english_to_persian_num(number_format($amount)) }} <span
                                    class="text-xs text-gray-400">تومان</span></span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <label class="flex items-center bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                           :class="payment_method === 'gateway' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">
                        <input type="radio" value="gateway" x-model="payment_method" class="hidden">
                        <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                              :class="payment_method === 'gateway' ? 'border-pars-500' : 'border-gray-400'">
                            <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"
                                  x-show="payment_method === 'gateway'"></span>
                        </span>
                        <span class="text-sm text-gray-700">پرداخت از طریق درگاه بانکی با رمز دوم</span>
                    </label>

{{--                    @if($torobpayEligible)--}}
{{--                        <label class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-200"--}}
{{--                               :class="payment_method === 'torobpay' ? 'border-pars-500 bg-pars-50' : 'border-gray-200 hover:border-pars-300'">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <input type="radio" value="torobpay" x-model="payment_method" class="hidden">--}}
{{--                                <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"--}}
{{--                                      :class="payment_method === 'torobpay' ? 'border-pars-500' : 'border-gray-400'">--}}
{{--                                    <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-200"--}}
{{--                                          x-show="payment_method === 'torobpay'"></span>--}}
{{--                                </span>--}}
{{--                                <div>--}}
{{--                                    <span class="text-sm font-medium text-gray-800">{{ $torobpayTitle }}</span>--}}
{{--                                    <div class="text-xs text-gray-500">{{ $torobpayDescription }}</div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <img class="w-12 rounded-full shadow-sm" src="{{ asset('images/torobpay.png') }}"--}}
{{--                                 alt="ترب پی">--}}
{{--                        </label>--}}
{{--                    @else--}}
{{--                        <label class="flex items-center justify-between bg-gray-100 px-4 py-3 rounded-xl border-2 border-gray-200 cursor-not-allowed opacity-60">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 border-gray-400"></span>--}}
{{--                                <div>--}}
{{--                                    <span class="text-sm font-medium text-gray-500">پرداخت اقساطی با ترب پی</span>--}}
{{--                                    <div class="text-xs text-red-500">برای سفارش‌های با مبالغ بالاتر از ۲۰,۰۰۰ تومان--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <img class="w-12 rounded-full shadow-sm opacity-50" src="{{ asset('images/torobpay.png') }}"--}}
{{--                                 alt="ترب پی">--}}
{{--                        </label>--}}
{{--                    @endif--}}

                    <div class="bg-white rounded-xl border-2 transition-all duration-200 overflow-hidden"
                         :class="payment_method === 'card' ? 'border-pars-500' : 'border-gray-200'">
                        <label class="flex items-center px-4 py-3 cursor-pointer">
                            <input type="radio" value="card" x-model="payment_method" class="hidden">
                            <span class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-200"
                                  :class="payment_method === 'card' ? 'border-pars-500' : 'border-gray-400'">
                                <span class="w-2.5 h-2.5 rounded-full bg-pars-500"
                                      x-show="payment_method === 'card'"></span>
                            </span>
                            <span class="text-sm text-gray-700">پرداخت از طریق کارت به کارت</span>
                        </label>

                        <div
                                class="transition-all duration-500 ease-in-out overflow-hidden"
                                :style="payment_method === 'card'
                            ? 'max-height:500px; opacity:1; padding:12px;'
                            : 'max-height:0; opacity:0; padding:0 12px;'">

                            <div
                                    class="w-full max-w-xs mx-auto bg-black rounded-2xl relative text-white overflow-visible  border"
                                    style="aspect-ratio:1.7;">
                                <div x-show="copied"
                                     x-transition.opacity
                                     class="absolute top-5 left-5 -translate-x-5 font-bold text-[11px] bg-black text-shadow-white px-3 py-1 rounded-full shadow-md">
                                    کپی شد ✓
                                </div>
                                <img src="{{ asset('images/Pasargad.png') }}"
                                     class="absolute top-3 right-3 w-10 opacity-90">
                                <div
                                        class="absolute top-4 left-1/2 -translate-x-1/2 text-sm font-semibold tracking-wide">
                                    بانک پاسارگاد
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <button type="button"
                                            @click.stop="copyCard"
                                            class="font-mono text-lg tracking-[0.3em] cursor-pointer transition duration-200">
                                        5022&nbsp;2915&nbsp;3361&nbsp;0273
                                    </button>
                                </div>
                                <div class="absolute top-[60%] right-4 text-xs font-semibold">
                                    مجید رستمیان
                                </div>
                                <div class="absolute bottom-3 right-4 left-4 text-[10px] text-yellow-300 leading-tight">
                                    لطفاً مبلغ {{ english_to_persian_num(number_format($amount)) }} تومان را به شماره
                                    کارت بالا واریز نموده، سپس با استفاده از دکمه زیر سفارش خود را ثبت کنید.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full p-2"
                     x-data="{ payment_method: @entangle('payment_method').live }">
                    <button
                            wire:click.prevent="pay"
                            wire:target="pay"
                            class="w-full min-w-[120px] cursor-pointer text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-2 flex items-center justify-center relative transition-all duration-300">

                    <span wire:loading.remove wire:target="pay">
                    <span x-text="payment_method === 'card' ? 'ثبت سفارش' : 'پرداخت'"></span>
                </span>
                        <span wire:loading wire:target="pay" class="flex items-center justify-center">
                    <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
                    </button>

                    @if ($errors->any())
                        <div class="mt-4 bg-red-50 text-red-600 rounded-xl p-3 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ english_to_persian_num($error) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
