@props(['text' => '', 'position' => 'bottom'])

@php
    $positionClasses = [
        'top' => 'bottom-full left-1/2 transform -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 transform -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 transform -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 transform -translate-y-1/2 ml-2',
    ];
    
    $arrowClasses = [
        'top' => 'bottom-[-4px] left-1/2 transform -translate-x-1/2 border-t-gray-800 border-l-transparent border-r-transparent border-b-transparent',
        'bottom' => 'top-[-4px] left-1/2 transform -translate-x-1/2 border-b-gray-800 border-l-transparent border-r-transparent border-t-transparent',
        'left' => 'right-[-4px] top-1/2 transform -translate-y-1/2 border-r-gray-800 border-t-transparent border-b-transparent border-l-transparent',
        'right' => 'left-[-4px] top-1/2 transform -translate-y-1/2 border-l-gray-800 border-t-transparent border-b-transparent border-r-transparent',
    ];
@endphp

<div x-data="{ open: false }" class="relative inline-block">
    {{-- دکمه علامت سوال --}}
    <button @click="open = !open" @click.away="open = false"
            type="button"
            class="focus:outline-none hover:opacity-75 transition-opacity cursor-pointer">
        <svg class="w-4 h-4 text-orange-500 hover:text-orange-600 transition-all duration-300 hover:scale-110" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" transform="scale(-1, 1)">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
        </svg>
    </button>
    
    {{-- محتوای تولتیپ --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute z-50 w-64 p-3 bg-gray-800 text-white rounded-lg shadow-xl {{ $positionClasses[$position] }}">
        
        {{-- فلش --}}
        <div class="absolute w-2 h-2 bg-gray-800 transform rotate-45 {{ $arrowClasses[$position] }}"></div>
        
        {{-- متن --}}
        <div class="text-xs leading-relaxed text-right">
            {{ $text }}
        </div>
    </div>
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
</div>

