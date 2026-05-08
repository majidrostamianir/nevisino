<div class="p-4">
    
    <!-- پیام‌ها -->
    @if(session()->has('message'))
        <div class="fixed top-5 right-5 z-50 p-3 bg-green-100 text-green-700 rounded-lg shadow-lg text-sm">
            {{ session('message') }}
        </div>
    @endif
    
    @if(session()->has('error'))
        <div class="fixed top-5 right-5 z-50 p-3 bg-red-100 text-red-700 rounded-lg shadow-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session()->has('info'))
        <div class="fixed top-5 right-5 z-50 p-3 bg-blue-100 text-blue-700 rounded-lg shadow-lg text-sm">
            {{ session('info') }}
        </div>
    @endif
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        
        <!-- بخش بازه‌های قیمتی -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-4">
            <h3 class="font-bold text-gray-800 mb-3 text-sm">🎯 بازه‌های قیمتی</h3>
            
            <div class="space-y-2">
                @foreach($priceRanges as $index => $range)
                    <div wire:click="selectRange({{ $index }})"
                         wire:key="range-{{ $index }}"
                         class="border rounded-lg p-2 cursor-pointer transition-all text-sm
                            {{ $selectedRangeIndex == $index ? 'bg-blue-50 border-blue-500 shadow-md' : 'bg-white border-gray-200 hover:border-blue-300' }}">
                        <div class="flex justify-between items-center">
                            <div class="font-medium">
                                  {{english_to_persian_num( number_format($range['min']))  }} تا {{  english_to_persian_num(number_format($range['max']))}} تومان
                            </div>
                            @if($range['percent'] != 0)
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $range['percent'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $range['percent'] > 0 ? '▲' : '▼' }} {{ abs($range['percent']) }}%
                            </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ english_to_persian_num(\App\Models\Product::query()->whereBetween('price', [$range['min']+1, $range['max']])->count()) }} محصول
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- بخش تنظیم درصد و محصولات -->
        <div class="lg:col-span-3 bg-white rounded-xl shadow-lg p-4">
            
            <!-- فرم تنظیم درصد -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="font-bold text-gray-700 text-sm">
                        بازه انتخاب شده:
                        <span class="text-blue-600">
                            {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['min'])) }} -
                            {{ english_to_persian_num(number_format($priceRanges[$selectedRangeIndex]['max'])) }} تومان
                        </span>
                    </span>
                    
                    <div class="flex items-center gap-2">
                        <input type="number"
                               step="0.5"
                               x-on:wheel.prevent
                               wire:model.live.debounce.500ms="selectedPercent"
                               class="w-24 px-2 py-1 border rounded-lg text-center text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="درصد">
                        <span class="text-sm">%</span>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="$set('selectedType', 'increase')"
                                wire:key="btn-increase"
                                class="cursor-pointer px-3 py-1 rounded-lg text-sm transition {{ $selectedType == 'increase' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                            ▲ افزایش
                        </button>
                        <button wire:click="$set('selectedType', 'decrease')"
                                wire:key="btn-decrease"
                                class="cursor-pointer px-3 py-1 rounded-lg text-sm transition {{ $selectedType == 'decrease' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                            ▼ کاهش
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- آمار محصولات تحت تاثیر -->
            @if($showProducts && $priceRanges[$selectedRangeIndex]['percent'] != 0)
                <div class="mb-3 flex gap-3 text-sm">
                    <span class="text-green-600 bg-green-50 px-3 py-1 rounded-full">▲ افزایش: {{ $totalIncrease }}</span>
                    <span class="text-red-600 bg-red-50 px-3 py-1 rounded-full">▼ کاهش: {{ $totalDecrease }}</span>
                    <span class="text-gray-600 bg-gray-50 px-3 py-1 rounded-full">📦 مجموع: {{ count($affectedProducts) }}</span>
                </div>
            @endif
            
            <!-- جدول محصولات با اسکرول مجزا -->
            <div class="overflow-x-auto rounded-lg shadow">
                @if($showProducts && count($affectedProducts) > 0)
                    <table class="min-w-full text-right bg-white">
                        <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md">
                            <th class="px-4 py-3 text-sm font-semibold">نام محصول</th>
                            <th class="px-4 py-3 text-sm font-semibold">قیمت فعلی</th>
                            @if($priceRanges[$selectedRangeIndex]['percent'] != 0)
                                <th class="px-4 py-3 text-sm font-semibold">قیمت جدید</th>
                                <th class="px-4 py-3 text-sm font-semibold">تغییر</th>
                                <th class="px-4 py-3 text-sm font-semibold">درصد</th>
                            @endif
                            <th class="px-4 py-3 text-sm font-semibold">قیمت تخفیف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($affectedProducts as $index => $product)
                            <tr wire:key="product-{{ $product['id'] }}"
                                class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ english_to_persian_num(\Illuminate\Support\Str::limit($product['name'], 50)) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                                    {{ english_to_persian_num(number_format($product['old_price'])) }} تومان
                                </td>
                                
                                @if($priceRanges[$selectedRangeIndex]['percent'] != 0)
                                    <td class="px-4 py-3 text-sm font-bold {{ $product['price_change'] > 0 ? 'text-green-600' : ($product['price_change'] < 0 ? 'text-red-600' : '') }}">
                                        {{ english_to_persian_num(number_format($product['new_price'])) }} تومان
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $product['price_change'] > 0 ? 'text-green-600' : ($product['price_change'] < 0 ? 'text-red-600' : '') }}">
                                        @if($product['price_change'])
                                            <span class="inline-flex items-center gap-1">
                                            {{ $product['price_change'] > 0 ? '▲' : '▼' }}
                                                {{ english_to_persian_num(number_format(abs($product['price_change']))) }} تومان
                                        </span>
                                        @else
                                            -
                                @endif
                                <td class="px-4 py-3 text-sm">
                                    @if($product['price_change_percent'] != 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $product['price_change_percent'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $product['price_change_percent'] > 0 ? '+' : '' }}{{ english_to_persian_num($product['price_change_percent']) }}%
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                @endif
                                
                                <td class="px-4 py-3 text-sm">
                                    @if($product['old_discounted_price'])
                                        <div class="text-gray-500 line-through text-xs">
                                            {{ english_to_persian_num(number_format($product['old_discounted_price'])) }} تومان
                                        </div>
                                        @if($product['new_discounted_price'])
                                            <div class="text-green-600 font-medium mt-1">
                                                {{ english_to_persian_num(number_format($product['new_discounted_price'])) }} تومان
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-xl">
                        <div class="text-4xl mb-2">📭</div>
                        <p class="text-gray-500">هیچ محصولی در این بازه قیمتی وجود ندارد</p>
                    </div>
                @endif
            </div>
            <!-- دکمه تایید و اعمال نهایی -->
            @if($showProducts && count($affectedProducts) > 0 && $priceRanges[$selectedRangeIndex]['percent'] != 0)
                <div class="mt-4 pt-3  flex justify-end">
                    <button wire:click="applyChanges"
                            wire:loading.attr="disabled"
                            class="cursor-pointer bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition font-medium">
                        <span wire:loading.remove>✅ تایید و اعمال تغییرات روی {{ english_to_persian_num(count($affectedProducts)) }} محصول</span>
                        <span wire:loading>⏳ در حال اعمال...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>