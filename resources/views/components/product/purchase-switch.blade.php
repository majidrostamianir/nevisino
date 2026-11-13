@props(['switch'])

@php
    $iconPath = $switch['icon'] === 'installment'
        ? 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
        : 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z';
@endphp

<div x-data="{ open: false }"
     x-effect="document.body.style.overflow = open ? 'hidden' : ''"
     @keydown.escape.window="open = false"
     class="mt-6">
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl  p-5 bg-white shadow-sm">

        <div class="flex items-center gap-4 min-w-0">
            <span @class([
                'flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br text-white shrink-0',
                'from-ghesti-500 to-ghesti-600' => $switch['color'] === 'ghesti',
                'from-naghdi-500 to-naghdi-600' => $switch['color'] === 'naghdi',
            ])>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                </svg>
            </span>

            <div class="min-w-0">
                <div class="font-bold text-lg text-gray-600">{{ $switch['title'] }}</div>
                <div class="text-xs text-gray-500 mt-1 ">{{ $switch['description'] }}</div>
            </div>
        </div>

        <button type="button" @click="open = true"
                @class([
                    'cursor-pointer inline-flex items-center gap-2 text-white text-sm font-medium px-5 h-11 rounded-xl shadow-md transition shrink-0',
                    'bg-ghesti-500 hover:bg-ghesti-600 shadow-ghesti-500/30' => $switch['color'] === 'ghesti',
                    'bg-naghdi-500 hover:bg-naghdi-600 shadow-naghdi-500/30' => $switch['color'] === 'naghdi',
                ])>
            {{ $switch['btn'] }}
        </button>
    </div>

    <template x-teleport="body">
        <div x-show="open" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4">

            {{-- بک‌دراپ: تنها چیزی که ترنزیشن opacity داره --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-black/40"
                 @click="open = false"></div>

            {{-- محتوا --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">

                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $switch['btn'] }}</h3>
                @if(\App\Helpers\Site::isGhesti())
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        برای خرید نقدی این محصول وارد فروشگاه نویسینو می‌شوید
                    </p>
                @else
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        برای خرید ۴ قسطه این محصول وارد فروشگاه نویسینو قسطی می‌شوید
                    </p>
                @endif
                <div class="flex gap-2 justify-end">
                    <button type="button"
                            @click="document.activeElement.blur(); open = false"
                            class="cursor-pointer bg-gray-100 text-gray-700 text-sm px-5 h-10 rounded-xl hover:bg-gray-200">
                        انصراف
                    </button>
                    <a href="{{ $switch['url'] }}"
                            @class([
                                'cursor-pointer inline-flex items-center text-white text-sm font-medium px-5 h-10 rounded-xl shadow-md transition',
                                'bg-ghesti-500 hover:bg-ghesti-600 shadow-ghesti-500/30' => $switch['color'] === 'ghesti',
                                'bg-naghdi-500 hover:bg-naghdi-600 shadow-naghdi-500/30' => $switch['color'] === 'naghdi',
                            ])>
                        تایید و ادامه
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>