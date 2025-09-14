<div>
    <div class="overflow-y-scroll relative">
        @foreach($addresses as $value)
            <div class="w-full shadow  rounded p-4 border border-pars-400 flex cursor-pointer mb-4">
{{--                 wire:click.prevent="selectAddress('{{ $value->id }}')">--}}
                <img class="w-6 h-fit" src="{{ asset('images/location.png') }}" alt="">
                <div class="mr-4">
                    <p class="mb-2  mt-1 text-pars-500">{{ $value['postal_address'] }}</p>
                    <p class="mb-2">کد پستی: {{ english_to_persian_num($value['zipcode']) }}</p>
                    <p class="mb-2">تحویل گیرنده: {{ $value['recipient_name'] }}
                        | {{ english_to_persian_num($value['recipient_mobile']) }}</p>
                </div>
            </div>
        @endforeach

        @if($showPopup)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click="togglePopup">
                <div class="bg-white rounded-2xl shadow-lg w-10/12 sm:w-1/3 px-4" wire:click.stop>
                    <div class="flex justify-between border-b-2  border-b-pars-400 py-4">
                        <strong class="text-lg font-semibold">ویرایش آدرس</strong>
                        <strong
                            wire:click="togglePopup"
                            class=" text-gray-500 hover:text-black cursor-pointer">
                            ✕
                        </strong>
                    </div>
                    <div class="my-3 h-72 overflow-y-scroll px-3">

                    </div>
                    <div class="border-t-2  border-t-pars-400 py-4 text-left">
                        <button class="text-white bg-pars-500 hover:bg-pars-600 p-2 rounded cursor-pointer"
                              wire:click.prevent="save()">ذخیره تغییرات</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
