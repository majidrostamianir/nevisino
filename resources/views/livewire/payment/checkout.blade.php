<div
    x-data="{ showPopup: @entangle('showPopup').live }"
    x-cloak
    x-on:close-popup.window="showPopup = false">
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
         x-show="showPopup"
         x-transition.opacity.duration.200ms
         @click="showPopup = false">
        <div class="bg-white rounded-2xl shadow-lg w-10/12 sm:w-1/3 px-4"
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
                            <p class="mb-2  mt-1 text-pars-500">{{ $value['postal_address'] }}</p>
                            <p class="mb-2">کد پستی: {{ english_to_persian_num($value['zipcode']) }}</p>
                            <p class="mb-2">تحویل گیرنده: {{ $value['recipient_name'] }}
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
    <div class="sm:flex ">
        @if($selectedAddress)
            <div class="w-full sm:w-2/3 p-4 bg-pars-100 shadow rounded-2xl">
                <div class="w-full rounded border border-pars-400 shadow">
                    <div class="flex w-full p-2 cursor-pointer" @click="showPopup = true">
                        <img class="w-12" src="{{ asset('images/post.png') }}" alt="">
                        <div class="w-full px-2">
                            <p class="mb-2 text-pars-500">ارسال به آدرس:</p>
                            <strong>{{ $selectedAddress->postal_address }}</strong>
                        </div>
                        <div class="text-nowrap">
                            <span class="cursor-pointer text-sm text-pars-500">تغییر آدرس ></span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pr-4">
                    <p class="mb-2">نام و نام خانوادگی گیرنده: <strong>{{ $selectedAddress->recipient_name }}</strong>
                    </p>
                    <p class="mb-2">شماره موبایل گیرنده:
                        <strong>{{ english_to_persian_num( $selectedAddress->recipient_mobile)  }}</strong></p>
                    <p class="mb-2">استان: <strong>{{ $selectedAddress->province->name }}</strong></p>
                    <p class="mb-2">شهر: <strong>{{ $selectedAddress->city->name }}</strong></p>
                    <p class="mb-2">آدرس کامل پستی: <strong>{{ $selectedAddress->postal_address }}</strong></p>
                    <p class="mb-2">کد پستی: <strong>{{ english_to_persian_num($selectedAddress->zipcode) }}</strong>
                    </p>
                    <div class="mt-4">
                        <label class="mr-4 text-sm">توضیحات اختیاری در مورد این سفارش:</label>
                        <textarea wire:model="description"
                                  class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                        @error('description') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @else
            <div class="w-full sm:w-2/3 p-4 bg-pars-100 shadow rounded-2xl">
                <strong>مشخصات و آدرس دریافت کننده :</strong>
                <div class="mt-4">
                    <label class="mr-4 text-sm">نام و نام خانوادگی: <strong class="text-red-500">*</strong></label>
                    <input wire:model="recipient_name" class="mt-1 w-full rounded-full px-4">
                    @error('recipient_name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">شماره موبایل:<strong class="text-red-500">*</strong></label>
                    <input wire:model="recipient_mobile" class="mt-1 w-full rounded-full px-4">
                    @error('recipient_mobile') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
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
                    @error('province_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
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
                    @error('city_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">آدرس کامل و دقیق:<strong class="text-red-500">*</strong></label>
                    <textarea wire:model="postal_address"
                              class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                    @error('postal_address') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">کد پستی:<strong class="text-red-500">*</strong></label>
                    <input wire:model="zipcode" class="mt-1 w-full rounded-full px-4">
                    @error('zipcode') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="mt-4">
                    <label class="mr-4 text-sm">توضیحات اختیاری در مورد این سفارش:</label>
                    <textarea wire:model="description"
                              class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4"></textarea>
                    @error('description') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
            </div>

        @endif

        <div class="sticky top-20 w-full h-fit sm:w-1/3 bg-pars-100 shadow rounded-2xl mt-2 sm:mt-0 sm:mr-2 p-4">
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>جمع مبلغ سفارش</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class=" text-left"><span>{{ english_to_persian_num(number_format($sum)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>حمل و نقل:</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left"><span>{{ english_to_persian_num(number_format( $shipping)) }} تومان</span></div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>روش ارسال</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class=" text-left">پست پیشتاز</div>
            </div>
            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>زمان ارسال</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class=text-left">
                    <span>هر روز ساعت ۱۰ صبح<span class="text-xs">(روزهای کاری)</span></span>
                </div>
            </div>
            <div class="w-full mb-16 flex justify-between">
                <div class=""><strong>زمان رسیدن به مقصد</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class="text-left">{{ english_to_persian_num('بین 3 تا 5 روز') }}</div>
            </div>

            <div class="w-full mb-4 flex justify-between">
                <div class=""><strong>مبلغ قابل پرداخت:</strong></div>
                <span class="flex-1 border-b h-4 border-dotted border-gray-400 mx-2"></span>
                <div class=" text-left"><span>{{ english_to_persian_num(number_format($amount)) }} تومان</span></div>
            </div>

            <div class="w-full">
                <button wire:click.prevent="pay"
                        wire:loading.attr="disabled"
                        wire:target="pay"
                        class="w-full text-center bg-pars-500 hover:bg-pars-600 text-white rounded-2xl py-1 cursor-pointer">
                    <span wire:loading.remove wire:target="pay">
                        پرداخت
                    </span>
                    <span wire:loading wire:target="pay">
                        در حال انتقال به درگاه...
                    </span>
                </button>
                @if ($errors->any())
                    <div class="mt-3 bg-red-100 text-red-500 rounded-xl p-2 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
