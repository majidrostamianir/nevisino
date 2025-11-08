<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-scroll">

    <div class="w-full relative text-center" x-data="{ open: false }" @click.away="open = false">
        <p class="my-3">
            <strong class="text-lg ml-4">{{ $url->title }}</strong>
        </p>

        <input
            class="w-1/2 border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
            type="text"
            wire:model.live.debounce.300ms="query"
            placeholder="افزودن محصول..."
            @focus="open = true"
        >

        @if(!empty($products))
            <ul
                x-show="open"
                x-transition
                class="absolute left-1/2 -translate-x-1/2 z-10 bg-white border border-gray-300 mt-1 rounded shadow w-1/2 max-h-40 overflow-y-auto"
            >
                @foreach ($products as $key => $value)
                    @php
                        $exists = $urlProducts->contains('id', $key);
                    @endphp

                    <li
                        wire:click="{{ $exists ? '' : 'addProduct(' . $key . ')' }}"
                        class="px-2 py-1 cursor-pointer
                           {{ $exists ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-pars-400 hover:text-pars-500' }}"
                        @click="open = false"
                    >
                        {{ $value }}
                        @if($exists)
                            <span class="text-xs text-green-500 ml-2">(افزوده شده)</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>


    <div class="mt-8">
        <table class="min-w-full border-collapse text-sm text-right">
            <tbody>
            @foreach($urlProducts as $index => $value)
                <tr class=" hover:bg-pars-50 shadow-xs transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-medium cursor-pointer">
                        <img class="w-20 rounded shadow"
                             src="{{ asset('storage/products/' . $value->id . '/small/1.webp') }}"
                             alt="">
                    </td>
                    <td class="px-4 py-3 font-medium cursor-pointer">
                        {{ $value->title }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="cursor-pointer text-red-500" wire:click.prevent="removeProduct('{{ $value->id }}')">حذف</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
