<div>
    <div class="relative w-full">
        <div class="relative w-full">
            <div class="flex w-full m-2">
                <div class="w-full relative rounded-2xl bg-white">
                    <div class="px-2 flex flex-wrap items-center gap-1">
                        @foreach ($selectedProducts as $key => $value)
                            <div class="bg-pars-400 text-pars-500 px-2 py-1 rounded flex items-center space-x-1">
                                <span>{{ $value }}</span>
                                <button wire:click="removeProduct({{ $key }})" class="text-red-500 pr-1 text-sm">x
                                </button>
                            </div>
                        @endforeach
                        <input
                            class="rounded-2xl"
                            type="text"
                            wire:model.live.debounce.300ms="query"
                            placeholder="جستجوی محصول...">
                    </div>
                    @if(!empty($products))
                        <ul class="absolute z-10 bg-white border border-gray-300 mt-1 rounded shadow w-full max-h-40 overflow-y-auto">
                            @foreach ($products as $key=> $value)
                                <li wire:click="selectProduct({{ $key }})"
                                    class="px-2 py-1 cursor-pointer hover:bg-pars-400 hover:text-pars-500">
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button class="bg-pars-500 hover:bg-pars-600 text-white px-4 rounded-2xl mr-2"
                        wire:click.prevent="save()">ثبت
                </button>
            </div>
        </div>
    </div>
    @if($successMessage)
        <div wire:ignore class="fixed bottom-4 left-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)">
            {{ $successMessage }}
            <button @click="show = false" class="mr-2">×</button>
        </div>
    @endif
</div>
