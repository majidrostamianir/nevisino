@props(['quantity', 'notice' => null])

<div class="flex relative">
    @if($notice)
        <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">{{ $notice }}</span>
    @endif

    <button type="button"
            wire:click.prevent="increase()"
            aria-label="افزایش تعداد"
            class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-r-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </button>

    <div class="relative">
        <x-spinner wire:loading wire:target="increase,decrease"
                   class="absolute inset-0 m-auto w-5 h-5 text-pars-500 pointer-events-none" />

        <input type="text"
               inputmode="numeric"
               x-data="quantityInput"
               x-bind:value="toFa($wire.quantity)"
               @input="onInput($event)"
               @blur="onBlur($event)"
               value="{{ english_to_persian_num($quantity) }}"
               class="w-14 h-10 text-center border-t border-b border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-pars-500 text-gray-800">
    </div>

    <button type="button"
            wire:click.prevent="decrease()"
            aria-label="کاهش تعداد"
            class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-l-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
        </svg>
    </button>
</div>