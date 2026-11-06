@props(['quantity', 'notice' => null])

<div class="flex relative">
    <x-spinner wire:loading wire:target="increase,decrease"
               class="absolute top-1.5 right-12 w-5 h-5 mr-2 text-pars-500" />

    @if($notice)
        <span class="absolute -bottom-6 right-2 text-xs text-red-500 text-nowrap">{{ $notice }}</span>
    @endif

    <div wire:click.prevent="increase()"
         class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-r-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
        +
    </div>

    <input type="text"
           inputmode="numeric"
           x-data="quantityInput"
           x-bind:value="toFa($wire.quantity)"
           @input="onInput($event)"
           @blur="onBlur($event)"
           value="{{ english_to_persian_num($quantity) }}"
           class="w-14 h-10 text-center border-t border-b border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-pars-500 text-gray-800">

    <div wire:click.prevent="decrease()"
         class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-pars-500 active:text-white rounded-l-xl cursor-pointer select-none transition-all duration-200 text-lg font-bold">
        -
    </div>
</div>