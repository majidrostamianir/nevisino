<div
    x-data="{ showPopup: @entangle('showPopup').live }"
    x-cloak
    x-on:close-popup.window="showPopup = false">
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
         x-show="showPopup"
         x-transition.opacity.duration.200ms
         @click="showPopup = false">
        <div class="bg-white rounded-2xl shadow-lg w-10/12 lg:w-1/3 px-4"
             @click.stop>
            <div class="flex justify-between border-b-2  border-b-pars-400 py-4">
                <strong class="text-lg font-semibold">آدرس های شما</strong>
                <strong
                    @click="showPopup = false"
                    class=" text-gray-500 hover:text-black cursor-pointer">
                    ✕
                </strong>
            </div>
            <div class="my-3 h-72 overflow-y-scroll px-3">
                @foreach($addresses as $value)
                    <div
                        class="w-full shadow-md border p-2 mb-3 flex cursor-pointer rounded-xl"
                        :class="{
                            'border-2 border-pars-500 shadow-pars-500': '{{ $value->id }}' == '{{ $selectedAddress->id ?? '' }}',
                            'border-pars-400': '{{ $value->id }}' != '{{ $selectedAddress->id ?? '' }}'
                        }"
                        wire:click.prevent="selectAddress('{{ $value->id }}')"
                        @click="showPopup = false"
                    >
                        <img class="w-6 h-fit" src="{{ asset('images/location.png') }}" alt="">
                        <div class="mr-4">
                            <p class="mb-2  mt-1 text-pars-500">{{ english_to_persian_num($value['postal_address']) }}</p>
                            <p class="mb-2">کد پستی: {{ english_to_persian_num($value['zipcode']) }}</p>
                            <p class="mb-2">تحویل گیرنده: {{ english_to_persian_num($value['recipient_name']) }}
                                | {{ english_to_persian_num($value['recipient_mobile']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t-2 border-t-pars-400 py-4">
                <span
                    class="font-bold cursor-pointer"
                    wire:click.prevent="changeAddress()"
                >+ افزودن آدرس جدید</span>
            </div>
        </div>
    </div>
    <div class="lg:flex ">
        @if($selectedAddress)
            <div class="w-full lg:w-1/3 p-4 bg-pars-100 shadow rounded-2xl">
                <div class="w-full rounded border border-pars-400 shadow">
                    <div class="flex w-full p-2 cursor-pointer" @click="showPopup = true">
                        <img class="w-12" src="{{ asset('images/post.png') }}" alt="">
                        <div class="w-full px-2">
                            <p class="mb-2 text-pars-500">ارسال به آدرس:</p>
                            <strong>{{ english_to_persian_num($selectedAddress->postal_address) }}</strong>
                        </div>
                        <div class="text-nowrap">
                            <span class="cursor-pointer text-sm text-pars-500">تغییر آدرس ></span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pr-4">
                    <p class="mb-2">نام و نام خانوادگی گیرنده:
                        <strong>{{ english_to_persian_num($selectedAddress->recipient_name) }}</strong>
                    </p>
                    <p class="mb-2">شماره موبایل گیرنده:
                        <strong>{{ english_to_persian_num( $selectedAddress->recipient_mobile)  }}</strong></p>
                    <p class="mb-2">استان: <strong>{{ $selectedAddress->province->name }}</strong></p>
                    <p class="mb-2">شهر: <strong>{{ $selectedAddress->city->name }}</strong></p>
                    <p class="mb-2">آدرس دقیق پستی:
                        <strong>{{ english_to_persian_num($selectedAddress->postal_address) }}</strong></p>
                    <p class="mb-2">کد پستی: <strong>{{ english_to_persian_num($selectedAddress->zipcode) }}</strong>
                    </p>
                    <div class="mt-4">
                        <label class="mr-4 text-sm">توضیحات اختیاری در مورد این سفارش:</label>
                        <textarea
                            x-data="faNumber('description' , false)"
                            x-model="value"
                            @input="onInput"
                            class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                        @error('description') <p
                            class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                    </div>
                </div>
            </div>
        @else
            <div class="w-full lg:w-1/3 p-4 bg-pars-100 shadow rounded-2xl">
                <strong>مشخصات و آدرس دریافت کننده :</strong>
                <div class="mt-4">
                    <label class="mr-4 text-sm">نام و نام خانوادگی: <strong class="text-red-500">*</strong></label>
                    <input wire:model="recipient_name" class="mt-1 w-full rounded-full px-4">
                    @error('recipient_name') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">شماره موبایل:<strong class="text-red-500">*</strong></label>
                    <input x-data="faNumber('recipient_mobile' , true)"
                           x-model="value"
                           @input="onInput"
                           inputmode="numeric"
                           class="mt-1 w-full rounded-full px-4">
                    @error('recipient_mobile') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num(english_to_persian_num($message)) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">استان:<strong class="text-red-500">*</strong></label>
                    <select wire:model.live="province_id"
                            class="mt-1 w-full rounded-full px-4">
                        <option>استان خود را انتخاب کنید</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    @error('province_id') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">شهر:<strong class="text-red-500">*</strong></label>
                    <select wire:model.live="city_id"
                            class="mt-1 w-full rounded-full px-4">
                        <option>شهر خود را انتخاب کنید</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">آدرس دقیق پستی:<strong class="text-red-500">*</strong></label>
                    <textarea x-data="faNumber('postal_address' , false)"
                              x-model="value"
                              @input="onInput"
                              class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                    @error('postal_address') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">کد پستی:<strong class="text-red-500">*</strong></label>
                    <input x-data="faNumber('zipcode' , true)"
                           x-model="value"
                           @input="onInput"
                           inputmode="numeric"
                           class="mt-1 w-full rounded-full px-4">
                    @error('zipcode') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">توضیحات اختیاری در مورد این سفارش:</label>
                    <textarea x-data="faNumber('description' , false)"
                              x-model="value"
                              @input="onInput"
                              class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                    @error('description') <p
                        class="text-red-500 text-xs">{{ english_to_persian_num($message) }}</p> @enderror
                </div>
            </div>
        @endif
        <div class="lg:sticky top-20 w-full h-fit lg:w-1/3 bg-pars-100 shadow rounded-2xl mt-2 lg:mt-0 lg:mr-2 p-4"
             x-data="{
        shipping_method: @entangle('shipping_method').live ?? 'post_cod'
    }"
             x-cloak>
        
        <div class="w-full mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <strong>انتخاب روش ارسال</strong>
                    <livewire:components.tooltip text="در روش پس‌کرایه، هزینه ارسال را درب منزل به پستچی پرداخت می‌کنید. اما در روش پیش‌کرایه، مبلغ هزینه ارسال را به همراه مبلغ کالاها به سایت پرداخت می‌کنید و دیگر در زمان تحویل گرفتن، هیچگونه مبلغی پرداخت نمی‌کنید. (روش پس‌کرایه از روش پیش‌کرایه ارزان‌تر است)"
                                                 position="bottom"/>
                </div>
                <div class="flex flex-col gap-3">
                    <label
                        class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                        :class="shipping_method === 'post_cod' ? 'border-pars-500 shadow-md' : 'border-gray-300'">
                        
                        <input type="radio" value="post_cod" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('post_cod')">
                        <div class="flex items-center">
                    <span
                        class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                        :class="shipping_method === 'post_cod' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                              x-show="shipping_method === 'post_cod'"></span>
                    </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">پست پیشتاز - پس‌کرایه</span>
                                    <span class="text-xs text-green-400 font-bold">(پیشنهاد ما)</span>
                                </div>
                                <div class="text-xs text-gray-500">زمان تحویل: ۲ تا ۵ روز</div>
                            </div>
                        </div>
                    </label>
                    <label
                        class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                        :class="shipping_method === 'post_cash' ? 'border-pars-500 shadow-md' : 'border-gray-300'">
                        
                        <input type="radio" value="post_cash" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('post_cash')">
                        
                        <div class="flex items-center">
                    <span
                        class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                        :class="shipping_method === 'post_cash' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                              x-show="shipping_method === 'post_cash'"></span>
                    </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">پست پیشتاز - پیش‌کرایه</span>
                                </div>
                                <div class="text-xs text-gray-500">زمان تحویل: ۲ تا ۵ روز</div>
                            </div>
                        </div>
                        <div
                            class="text-sm font-bold text-pars-700">{{ english_to_persian_num(number_format(config('shop.post_price'))) }}
                            تومان
                        </div>
                    </label>
                    <label
                        class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                        :class="shipping_method === 'tipax_cod' ? 'border-pars-500 shadow-md' : 'border-gray-300'">
                        
                        <input type="radio" value="tipax_cod" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('tipax_cod')">
                        
                        <div class="flex items-center">
                    <span
                        class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                        :class="shipping_method === 'tipax_cod' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                              x-show="shipping_method === 'tipax_cod'"></span>
                    </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">تیپاکس - پس‌کرایه</span>
                                </div>
                                <div class="text-xs text-gray-500">زمان تحویل: ۱ تا ۳ روز</div>
                            </div>
                        </div>
                    </label>
                    <label
                        class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                        :class="shipping_method === 'tipax_cash' ? 'border-pars-500 shadow-md' : 'border-gray-300'">
                        
                        <input type="radio" value="tipax_cash" x-model="shipping_method" class="hidden"
                               wire:click="updateShippingMethod('tipax_cash')">
                        
                        <div class="flex items-center">
                    <span
                        class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                        :class="shipping_method === 'tipax_cash' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                              x-show="shipping_method === 'tipax_cash'"></span>
                    </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">تیپاکس - پیش‌کرایه</span>
                                </div>
                                <div class="text-xs text-gray-500">زمان تحویل: ۱ تا ۳ روز</div>
                            </div>
                        </div>
                        <div
                            class="text-sm font-bold text-pars-700">{{ english_to_persian_num(number_format(config('shop.tipax_price') )) }}
                            تومان
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="lg:sticky top-20 w-full h-fit lg:w-1/3 bg-pars-100 shadow rounded-2xl mt-2 lg:mt-0 lg:mr-2 p-4">
            
            
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>مبلغ قابل پرداخت:</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class=" text-left"><span>{{ english_to_persian_num(number_format($amount)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4"
                 x-data="{
                    payment_method: @entangle('payment_method').live,
                    copied:false,
                    copyCard(){
                        navigator.clipboard.writeText('5022291533610273');
                        this.copied=true;
                        setTimeout(()=>this.copied=false,1500);
                    }
                 }">
                
                <div class="flex flex-col gap-3">
                    <label
                        class="flex items-center bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                        :class="payment_method === 'gateway'
                        ? 'border-2 border-pars-500 shadow-md'
                        : 'border-gray-300'">
                        
                        <input type="radio" value="gateway" x-model="payment_method" class="hidden">
                        
                        <span
                            class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                            :class="payment_method === 'gateway' ? 'border-pars-500' : 'border-gray-400'">
                        <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                              x-show="payment_method === 'gateway'"></span>
                    </span>
                        <span class="text-sm">پرداخت از طریق درگاه بانکی با رمز دوم</span>
                    </label>
                    @if($torobEligible)
                        <label
                            class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="payment_method === 'torobpay'
                            ? 'border-2 border-pars-500 shadow-md'
                            : 'border-gray-300'">
                            
                            <input type="radio" value="torobpay" x-model="payment_method" class="hidden">
                            
                            <div class="flex items-center">
                            <span
                                class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                                :class="payment_method === 'torobpay' ? 'border-pars-500' : 'border-gray-400'">
                                <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                                      x-show="payment_method === 'torobpay'"></span>
                            </span>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">{{ $torobpayTitle }}</span>
                                    <span class="text-xs text-gray-500">{{ $torobpayDescription }}</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-center mr-2">
                                <img class="w-16 rounded-full shadow-md" src="{{ asset('images/torobpay.png') }}"
                                     alt="ترب پی">
                                <span class="text-xs text-gray-600 mt-1">بدون چک و ضامن</span>
                            </div>
                        </label>
                    @else
                        <label
                            class="flex items-center justify-between bg-gray-300 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="payment_method === 'torobpay'
                            ? 'border-2 border-pars-500 shadow-md'
                            : 'border-gray-300'">
                            
                            <input type="radio" disabled value="torobpay" x-model="payment_method" class="hidden">
                            
                            <div class="flex items-center">
                            <span
                                class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                                :class="payment_method === 'torobpay' ? 'border-pars-500' : 'border-gray-400'">
                                <span class="w-2.5 h-2.5 rounded-full bg-pars-500 transition-all duration-300"
                                      x-show="payment_method === 'torobpay'"></span>
                            </span>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">پرداخت اقساطی با ترب پی</span>
                                    <span
                                        class="text-xs text-red-500">برای سفارش‌های با مبالغ بالاتر از ۲۰,۰۰۰ تومان<span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-center mr-2">
                                <img class="w-16 rounded-full shadow-md" src="{{ asset('images/torobpay.png') }}"
                                     alt="ترب پی">
                                <span class="text-xs text-gray-600 mt-1">بدون چک و ضامن</span>
                            </div>
                        </label>
                    @endif
                    <div
                        class="bg-white rounded-xl border-2 transition-all duration-300 overflow-hidden"
                        :class="payment_method === 'card'
                        ? 'border-2 border-pars-500 shadow-md'
                        : 'border-gray-300'">
                        
                        <label class="flex items-center px-4 py-3 cursor-pointer">
                            <input type="radio" value="card" x-model="payment_method" class="hidden">
                            <span
                                class="w-5 h-5 flex items-center justify-center rounded-full border-2 ml-3 transition-all duration-300"
                                :class="payment_method === 'card' ? 'border-pars-500' : 'border-gray-400'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-pars-500"
                                          x-show="payment_method === 'card'"></span>
                                    </span>
                            <span class="text-sm">پرداخت از طریق کارت به کارت</span>
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
            </div>
            <div class="w-full"
                 x-data="{ payment_method: @entangle('payment_method').live }">
                <button
                    wire:click.prevent="pay"
                    wire:target="pay"
                    class="w-full min-w-[120px] cursor-pointer text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-2 flex items-center justify-center relative transition-all duration-300">

                    <span wire:loading.remove wire:target="pay">
                    <span x-text="payment_method === 'card' ? 'ثبت سفارش' : 'پرداخت'"></span>
                    </span>
                    
                    <span wire:loading wire:target="pay" class="flex items-center justify-center">
                        <svg class="w-6 h-6 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </button>
                
                
                @if ($errors->any())
                    <div class="mt-3 bg-red-100 text-red-500 rounded-xl p-2 text-sm">
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
