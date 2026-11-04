<div class="flex flex-col h-full">
    {{-- ============================================ --}}
    {{-- پیغام شناور در پایین صفحه --}}
    {{-- ============================================ --}}
    @if(session()->has('message'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter.duration.300ms
             x-transition:leave.duration.300ms
             class="fixed bottom-6 right-6 z-50 max-w-md w-full">
            <div class="rounded-2xl shadow-2xl border p-4 flex items-center justify-between bg-green-600 border-green-700 text-white">
                <span class="flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('message') }}</span>
                </span>
                <button @click="show = false"
                        class="text-white/80 hover:text-white transition-colors text-xl font-bold leading-none">
                    ×
                </button>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter.duration.300ms
             x-transition:leave.duration.300ms
             class="fixed bottom-6 right-6 z-50 max-w-md w-full">
            <div class="rounded-2xl shadow-2xl border p-4 flex items-center justify-between bg-red-600 border-red-700 text-white">
                <span class="flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </span>
                <button @click="show = false"
                        class="text-white/80 hover:text-white transition-colors text-xl font-bold leading-none">
                    ×
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 flex-1">

        {{-- بخش بازه‌های قیمتی (ستون چپ) --}}
        <div class="lg:col-span-1 flex">
            <div class="w-full bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">
                <div class="bg-gray-50 px-4 py-3 border-b flex-shrink-0">
                    <h3 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                        🎯 بازه‌های قیمتی
                        <span class="text-xs text-gray-400 font-normal">({{ english_to_persian_num(count($priceRanges)) }})</span>
                    </h3>
                </div>
                <div class="p-3 flex-1 space-y-1.5 scrollbar-thin scrollbar-thumb-gray-300 overflow-y-auto">
                    @foreach($priceRanges as $index => $range)
                        <div wire:click="selectRange({{ $index }})"
                             wire:key="range-{{ $index }}"
                             class="p-2.5 rounded-xl cursor-pointer transition-all duration-200 border-2
                                {{ $selectedRangeIndex == $index
                                    ? 'border-blue-500 bg-blue-50 shadow-md'
                                    : 'border-transparent bg-gray-50 hover:bg-gray-100 hover:border-gray-300' }}">

                            <div class="flex justify-between items-center">
                                <div class="text-xs font-medium text-gray-700">
                                    {{ english_to_persian_num(number_format($range['min'])) }} -
                                    {{ english_to_persian_num(number_format($range['max'])) }}
                                </div>
                                @if($range['percent'] != 0)
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $range['percent'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $range['percent'] > 0 ? '▲' : '▼' }} {{ abs($range['percent']) }}%
                                    </span>
                                @endif
                            </div>

                            <div class="flex justify-between items-center mt-1">
                                <span class="text-[10px] text-gray-400">
                                    {{ english_to_persian_num(\App\Models\Product::query()->whereBetween('price', [$range['min']+1, $range['max']])->count()) }} محصول
                                </span>
                                @if($selectedRangeIndex == $index)
                                    <span class="text-[10px] text-blue-600 font-medium">✓ انتخاب شده</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- بخش اصلی (ستون راست) --}}
        <div class="lg:col-span-3 flex">
            <div class="w-full bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">

                {{-- ابزارهای تنظیم درصد --}}
                <div class="p-4 bg-gray-50 border-b flex-shrink-0">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">بازه:</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                                {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['min'])) }} -
                                {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['max'])) }} تومان
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">درصد:</span>
                            <input type="number"
                                   step="0.5"
                                   x-on:wheel.prevent
                                   wire:model.live.debounce.500ms="selectedPercent"
                                   class="w-20 px-3 py-1.5 border border-gray-200 rounded-lg text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="۰">
                            <span class="text-sm text-gray-500">%</span>
                        </div>

                        <div class="flex gap-1.5">
                            <button wire:click="$set('selectedType', 'increase')"
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $selectedType == 'increase' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                                ▲ افزایش
                            </button>
                            <button wire:click="$set('selectedType', 'decrease')"
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $selectedType == 'decrease' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                                ▼ کاهش
                            </button>
                        </div>

                        @if($selectedPercent > 0)
                            <span class="text-xs text-gray-500">
                                {{ $selectedType == 'increase' ? 'افزایش' : 'کاهش' }} {{ english_to_persian_num($selectedPercent) }}%
                            </span>
                        @endif
                    </div>
                </div>

                {{-- آمار --}}
                @if($showProducts && $priceRanges[$selectedRangeIndex]['percent'] != 0)
                    <div class="px-4 py-2.5 bg-white border-b flex flex-wrap gap-3 text-xs flex-shrink-0">
                        <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full inline-flex items-center gap-1">
                            <span class="text-base">▲</span> افزایش: {{ english_to_persian_num($totalIncrease) }}
                        </span>
                        <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full inline-flex items-center gap-1">
                            <span class="text-base">▼</span> کاهش: {{ english_to_persian_num($totalDecrease) }}
                        </span>
                        <span class="px-3 py-1 bg-gray-50 text-gray-700 rounded-full inline-flex items-center gap-1">
                            📦 مجموع: {{ english_to_persian_num(count($affectedProducts)) }}
                        </span>
                    </div>
                @endif

                {{-- جدول محصولات --}}
                <div class="flex-1 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
                    @if($showProducts && count($affectedProducts) > 0)
                        <table class="min-w-full text-right">
                            <thead class="sticky top-0 z-10">
                            <tr class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                                <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">محصول</th>
                                <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">قیمت فعلی</th>
                                @if($priceRanges[$selectedRangeIndex]['percent'] != 0)
                                    <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">قیمت جدید</th>
                                    <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">تغییر</th>
                                    <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">درصد</th>
                                @endif
                                <th class="px-4 py-3 text-xs font-semibold whitespace-nowrap">تخفیف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($affectedProducts as $product)
                                <tr wire:key="product-{{ $product['id'] }}"
                                    class="border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                                    <td class="px-4 py-2.5 text-xs text-gray-700 max-w-[150px] truncate" title="{{ $product['name'] }}">
                                        {{ \Illuminate\Support\Str::limit($product['name'], 30) }}
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-gray-800 font-medium whitespace-nowrap">
                                        {{ english_to_persian_num(number_format($product['old_price'])) }}
                                    </td>

                                    @if($priceRanges[$selectedRangeIndex]['percent'] != 0)
                                        <td class="px-4 py-2.5 text-xs font-bold whitespace-nowrap {{ $product['price_change'] > 0 ? 'text-green-600' : ($product['price_change'] < 0 ? 'text-red-600' : '') }}">
                                            {{ english_to_persian_num(number_format($product['new_price'])) }}
                                        </td>
                                        <td class="px-4 py-2.5 text-xs whitespace-nowrap {{ $product['price_change'] > 0 ? 'text-green-600' : ($product['price_change'] < 0 ? 'text-red-600' : 'text-gray-400') }}">
                                            @if($product['price_change'])
                                                {{ $product['price_change'] > 0 ? '▲' : '▼' }}
                                                {{ english_to_persian_num(number_format(abs($product['price_change']))) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-xs whitespace-nowrap">
                                            @if($product['price_change_percent'] != 0)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $product['price_change_percent'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $product['price_change_percent'] > 0 ? '+' : '' }}{{ english_to_persian_num($product['price_change_percent']) }}%
                                                    </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endif

                                    <td class="px-4 py-2.5 text-xs whitespace-nowrap">
                                        @if($product['old_discounted_price'])
                                            <div class="text-gray-400 line-through text-[10px]">
                                                {{ english_to_persian_num(number_format($product['old_discounted_price'])) }}
                                            </div>
                                            @if($product['new_discounted_price'])
                                                <div class="text-green-600 font-medium text-xs mt-0.5">
                                                    {{ english_to_persian_num(number_format($product['new_discounted_price'])) }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-12">
                            <div class="text-5xl mb-3">📭</div>
                            <p class="text-gray-400 font-medium">هیچ محصولی در این بازه قیمتی وجود ندارد</p>
                            <p class="text-xs text-gray-300 mt-1">محصولی با قیمت بین {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['min'])) }} تا {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['max'])) }} تومان یافت نشد</p>
                        </div>
                    @endif
                </div>

                {{-- دکمه اعمال تغییرات --}}
                @if($showProducts && count($affectedProducts) > 0 && $priceRanges[$selectedRangeIndex]['percent'] != 0)
                    <div class="px-4 py-3 bg-gray-50 border-t flex justify-end flex-shrink-0">
                        <button wire:click="applyChanges"
                                wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transition-all duration-200 font-medium text-sm flex items-center gap-2">
                            @if($isApplying)
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                در حال اعمال...
                            @else
                                ✅ تایید و اعمال روی {{ english_to_persian_num(count($affectedProducts)) }} محصول
                            @endif
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>