<div>
    <div class="relative">
        @foreach($addresses as $value)
            <div class="w-full shadow  rounded p-4 border border-pars-400 bg-white flex cursor-pointer mb-4"
                 wire:click.prevent="selectAddress('{{ $value->id }}')">
                <img class="w-6 h-fit" src="{{ asset('images/location.png') }}" alt="">
                <div class="mr-4">
                    <p class="mb-2  mt-1 text-pars-500">{{ $value['postal_address'] }}</p>
                    <p class="mb-2">کد پستی: {{ english_to_persian_num($value['zipcode']) }}</p>
                    <p class="mb-2">تحویل گیرنده: {{ $value['recipient_name'] }}
                        | {{ english_to_persian_num($value['recipient_mobile']) }}</p>
                </div>
            </div>
        @endforeach
        <button class="bg-pars-700 hover:bg-pars-800 rounded-2xl cursor-pointer py-1 mt-2 px-2 text-white shadow" wire:click="newAddress">افزودن آدرس جدید</button>
        @if($showPopup)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click="togglePopup">
                <div class="bg-white rounded-2xl shadow-lg w-10/12 sm:w-1/3 px-4" wire:click.stop>
                    <div class="flex justify-between border-b-2  border-b-pars-400 py-4">
                       @if($selectedAddress)
                            <strong class="text-lg font-semibold">ویرایش آدرس</strong>
                        @else
                            <strong class="text-lg font-semibold">افزودن آدرس</strong>
                       @endif
                        <strong
                            wire:click="togglePopup"
                            class=" text-gray-500 hover:text-black cursor-pointer">
                            ✕
                        </strong>
                    </div>
                    <div class="my-3 h-72 overflow-y-scroll px-3 ">
                        <div class="w-full ">
                            <div class="mt-4">
                                <label class="mr-4 text-sm">نام و نام خانوادگی: <strong class="text-red-500">*</strong></label>
                                <input wire:model="recipient_name" class="mt-1 w-full rounded-full px-4  border border-pars-600">
                                @error('recipient_name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label class="mr-4 text-sm">شماره موبایل:<strong class="text-red-500">*</strong></label>
                                <input wire:model="recipient_mobile" class="mt-1 w-full rounded-full px-4  border border-pars-600">
                                @error('recipient_mobile') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label class="mr-4 text-sm">استان:<strong class="text-red-500">*</strong></label>
                                <select wire:model.live="province_id"
                                        class="mt-1 w-full rounded-full px-4  border border-pars-600">
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
                                        class="mt-1 w-full rounded-full px-4  border border-pars-600">
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
                                          class="mt-1 bg-white min-h-24 w-full rounded-2xl outline-none px-4 border border-pars-600"></textarea>
                                @error('postal_address') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label class="mr-4 text-sm">کد پستی:<strong class="text-red-500">*</strong></label>
                                <input wire:model="zipcode" class="mt-1 w-full rounded-full px-4  border border-pars-600">
                                @error('zipcode') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="border-t-2  border-t-pars-400 py-4 text-left">
                        <span class="text-red-500 underline cursor-pointer" wire:click="delete()">حذف</span>
                        <button class="text-white bg-pars-500 hover:bg-pars-600 p-2 mr-4 rounded cursor-pointer"
                              wire:click.prevent="save()">ذخیره تغییرات</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
