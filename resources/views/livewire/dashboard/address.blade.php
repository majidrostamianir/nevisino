<div>
    <div class="space-y-6 relative">
        {{-- لیست آدرس‌ها --}}
        @foreach($addresses as $value)
            <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300">
                <div class="p-5 lg:p-6">
                    <div class="flex flex-col lg:flex-row items-start gap-4">
                        {{-- آیکون لوکیشن --}}
                        <div class="flex-shrink-0 hidden sm:block">
                            <div class="w-10 h-10 bg-pars-50 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- اطلاعات آدرس --}}
                        <div class="flex-1 min-w-0 space-y-2">
                            {{-- آدرس پستی --}}
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-gray-700 text-sm leading-relaxed">
                                    {{ english_to_persian_num($value['postal_address']) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                                {{-- کد پستی --}}
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 10h18M6 10v4m4-4v4m4-4v4m4-4v4M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-500">کد پستی:</span>
                                    <span class="font-medium text-gray-700">{{ english_to_persian_num($value['zipcode']) }}</span>
                                </div>

                                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>

                                {{-- نام گیرنده --}}
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-gray-500">تحویل گیرنده:</span>
                                    <span class="font-medium text-gray-700">{{ english_to_persian_num($value['recipient_name']) }}</span>
                                </div>

                                <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>

                                {{-- موبایل --}}
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-500">موبایل:</span>
                                    <span class="font-medium text-gray-700">{{ english_to_persian_num($value['recipient_mobile']) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- دکمه ویرایش --}}
                        <button wire:click.prevent="selectAddress('{{ $value->id }}')"
                                class="flex-shrink-0 cursor-pointer bg-gray-50 hover:bg-pars-500 hover:text-white border border-gray-200 hover:border-pars-500 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            ویرایش
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- دکمه افزودن آدرس جدید --}}
        <button wire:click="newAddress"
                class="w-full lg:w-auto cursor-pointer bg-pars-500 hover:bg-pars-600 active:scale-95 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm transition-all duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            افزودن آدرس جدید
        </button>

        {{-- مودال پاپ‌آپ --}}
        @if($showPopup)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click="togglePopup">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" wire:click.stop>
                    {{-- هدر مودال --}}
                    <div class="flex justify-between items-center border-b border-gray-100 px-6 py-4">
                        <h3 class="text-pars-700 font-bold text-lg">
                            @if($selectedAddress)
                                ویرایش آدرس
                            @else
                                افزودن آدرس جدید
                            @endif
                        </h3>
                        <button wire:click="togglePopup"
                                class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- فرم --}}
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        {{-- نام و نام خانوادگی --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                نام و نام خانوادگی <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="recipient_name"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all">
                            @error('recipient_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- شماره موبایل --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                شماره موبایل <span class="text-red-500">*</span>
                            </label>
                            <input x-data="faNumber('recipient_mobile' , true)"
                                   x-model="value"
                                   @input="onInput"
                                   inputmode="numeric"
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all">
                            @error('recipient_mobile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- استان --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                استان <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="province_id"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all">
                                <option value="">استان خود را انتخاب کنید</option>
                                @foreach(\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error('province_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- شهر --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                شهر <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="city_id"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all">
                                <option value="">شهر خود را انتخاب کنید</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- آدرس دقیق پستی --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                آدرس دقیق پستی <span class="text-red-500">*</span>
                            </label>
                            <textarea x-data="faNumber('postal_address' , false)"
                                      x-model="value"
                                      @input="onInput"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all resize-none"></textarea>
                            @error('postal_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- کد پستی --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                کد پستی <span class="text-red-500">*</span>
                            </label>
                            <input  x-data="faNumber('zipcode' , true)"
                                    x-model="value"
                                    @input="onInput"
                                    inputmode="numeric"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-pars-400 focus:ring-1 focus:ring-pars-400 transition-all">
                            @error('zipcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- فوتر مودال با دکمه‌ها --}}
                    <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                        @if($selectedAddress)
                            <button wire:click="delete()"
                                    class="cursor-pointer text-red-500 hover:text-red-600 transition-colors text-sm font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                حذف
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button wire:click.prevent="save()"
                                class="cursor-pointer bg-pars-500 hover:bg-pars-600 active:scale-95 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm">
                            ذخیره تغییرات
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>