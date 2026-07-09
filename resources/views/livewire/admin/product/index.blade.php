<div>
    {{-- پیام‌ها --}}
    @if(session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    {{-- فیلترها و کنترل‌ها --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 bg-white p-4 rounded-lg shadow">
        {{-- انتخاب دسته‌بندی با جستجو --}}
        <div class="flex items-center gap-2" x-data="{
            open: false,
            search: '',
            selected: 'all',
            options: @js($categories->pluck('title', 'id')->prepend('همه دسته‌بندی‌ها', 'all')->toArray()),
            get filteredOptions() {
                if (!this.search) return this.options;
                return Object.fromEntries(
                    Object.entries(this.options).filter(([key, value]) =>
                        value.toLowerCase().includes(this.search.toLowerCase())
                    )
                );
            },
            selectOption(key) {
                this.selected = key;
                this.search = '';
                this.open = false;
                $wire.set('selectedCategory', key);
            }
        }" class="relative">
            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">دسته‌بندی:</label>
            <div class="relative">
                <div @click="open = !open"
                     class="w-56 cursor-pointer rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500 bg-white flex items-center justify-between">
                    <span x-text="options[selected] || 'انتخاب کنید'"></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div x-show="open" @click.away="open = false"
                     class="absolute z-50 w-56 mt-1 bg-white rounded-lg border border-gray-200 shadow-lg max-h-60 overflow-hidden">
                    <div class="p-2 border-b border-gray-100">
                        <input type="text"
                               x-model="search"
                               @click.stop
                               placeholder="جستجوی دسته‌بندی..."
                               class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500">
                    </div>
                    <div class="overflow-y-auto max-h-48">
                        <template x-for="(label, key) in filteredOptions" :key="key">
                            <div @click="selectOption(key)"
                                 class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 transition-colors"
                                 :class="selected === key ? 'bg-pars-50 text-pars-700' : ''">
                                <span x-text="label"></span>
                            </div>
                        </template>
                        <div x-show="Object.keys(filteredOptions).length === 0"
                             class="px-3 py-2 text-sm text-gray-500 text-center">
                            موردی یافت نشد
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- انتخاب URL با جستجو --}}
        <div class="flex items-center gap-2" x-data="{
            open: false,
            search: '',
            selected: 'all',
            options: @js($urls->pluck('title_tag', 'id')->map(function($item, $key) {
                return $item ? $key . ' - ' . $item : 'صفحه ' . $key;
            })->prepend('همه صفحات', 'all')->toArray()),
            get filteredOptions() {
                if (!this.search) return this.options;
                return Object.fromEntries(
                    Object.entries(this.options).filter(([key, value]) =>
                        value.toLowerCase().includes(this.search.toLowerCase())
                    )
                );
            },
            selectOption(key) {
                this.selected = key;
                this.search = '';
                this.open = false;
                $wire.set('selectedUrl', key);
            }
        }" class="relative">
            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">صفحه:</label>
            <div class="relative">
                <div @click="open = !open"
                     class="w-56 cursor-pointer rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500 bg-white flex items-center justify-between">
                    <span x-text="options[selected] || 'انتخاب کنید'"></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div x-show="open" @click.away="open = false"
                     class="absolute z-50 w-56 mt-1 bg-white rounded-lg border border-gray-200 shadow-lg max-h-60 overflow-hidden">
                    <div class="p-2 border-b border-gray-100">
                        <input type="text"
                               x-model="search"
                               @click.stop
                               placeholder="جستجوی صفحه..."
                               class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500">
                    </div>
                    <div class="overflow-y-auto max-h-48">
                        <template x-for="(label, key) in filteredOptions" :key="key">
                            <div @click="selectOption(key)"
                                 class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 transition-colors"
                                 :class="selected === key ? 'bg-pars-50 text-pars-700' : ''">
                                <span x-text="label"></span>
                            </div>
                        </template>
                        <div x-show="Object.keys(filteredOptions).length === 0"
                             class="px-3 py-2 text-sm text-gray-500 text-center">
                            موردی یافت نشد
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- کنترل درصد سود در هدر --}}
        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">درصد سود:</label>
            <input type="number"
                   wire:model.live="profitPercent"
                   class="w-16 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                   min="0" max="1000">
            <span class="text-xs text-gray-500">%</span>
        </div>
        {{-- تعداد محصولات --}}
        <span class="text-sm text-gray-500 mr-auto">
            تعداد: {{ english_to_persian_num($products->count()) }} محصول
        </span>
    </div>

    {{-- جدول محصولات --}}
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">شناسه</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">محصول</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت خرید</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت اصلی</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت تخفیفی</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت عمده</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت اقساطی</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">اقساطی تخفیف</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">عملیات</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">موجودی</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">تصویر</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $index => $product)
                @php
                    // تشخیص واریانت
                    $hasVariant = !is_null($product->variant);

                    // محاسبه موجودی محصول
                    if ($product->variant && $product->selectedVariant < 1000) {
                        $stock = $product->variants()->sum('stock');
                    } elseif ($product->variant && $product->selectedVariant >= 1000) {
                        $stock = \App\Models\ProductVariant::find($product->selectedVariant)?->stock ?? 0;
                    } else {
                        $stock = $product->stock;
                    }
                @endphp
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap">
                        {{ english_to_persian_num($product['id']) }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            {{-- لینک مشاهده در سایت --}}
                            <a href="{{ route('product-page' , ['title' => $product->dashed_url, 'npi'=>$product->id]) }}"
                               target="_blank"
                               class="inline-flex items-center justify-center text-pars-600 hover:text-pars-800 transition-colors"
                               title="مشاهده در سایت">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                            </a>

                            {{-- لینک ترب با آیکون جدید --}}
                            @if($product->torob_url)
                                <a href="{{ $product->torob_url }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center hover:opacity-80 transition-opacity"
                                   title="مشاهده در ترب">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 88 88">
                                        <g fill="none">
                                            <path fill="#FF6B00" d="M79.245 20.322l-.627.726c-2.474 2.656-5.814 4.2-9.462 4.313-.16.023-.342.023-.502.023-.456 0-.878-.023-1.31-.068 8.595 10.1 9.895 24.968 2.222 36.579-7.638 11.542-21.774 16.252-34.428 12.484h-.08c-5.13-1.34-10.944.68-14.022 5.414 3.192-4.847 2.736-11.055-.798-15.322v-.045c-8.333-10.079-9.473-24.72-1.87-36.16C26.692 15.67 42.766 11.22 56.195 17.031c-.57-1.476-.935-3.065-1.026-4.688-.114-2.95.741-5.788 2.371-8.171-18.012-5.902-38.532.567-49.476 17.137C-5.104 41.171.425 67.842 20.261 80.893c19.882 13.064 46.592 7.502 59.702-12.246 10.032-15.174 9.177-34.331-.707-48.348l-.011.023z"></path>
                                            <path fill="#FF8C00" d="M31.969 10.22c-10.91 1.08-20.463 5.721-24.054 11.191C-5.195 41.216.3 67.853 20.193 80.905l.73-1.112c3.191-4.847 2.735-11.055-.799-15.322v-.045c-8.22-10.079-9.36-24.72-1.835-36.16C26.61 15.67 42.685 11.22 56.114 17.031c0 0-11.4-8.059-24.122-6.81h-.023z"></path>
                                            <path fill="#FFA500" d="M62.544 22.32s-.342-4.086 1.482-7.037c1.824-2.951 5.7-5.845 10.602-5.539 4.925.307 5.723 1.442 5.723 1.442s.114 3.972-1.254 6.242-4.218 5.606-8.322 6.446c-4.104.829-8.231-1.555-8.231-1.555z"></path>
                                            <path fill="#FFB347" d="M61.062 21.173s-.456-3.972 2.28-7.944c2.736-3.973 5.7-4.563 5.7-4.563S68.7 3.218 66.078.608c.228-.057-3.124.08-6.042 3.291-2.577 2.815-4.104 7.264-2.964 11.577 1.14 4.312 3.99 5.697 3.99 5.697z"></path>
                                        </g>
                                    </svg>
                                </a>
                            @endif

                            {{-- لینک ویرایش محصول --}}
                            <a href="{{ route('admin.product.save' , ['product'=>$product]) }}"
                               class="text-pars-600 hover:text-pars-800 font-medium transition-colors">
                                {{ $product['title'] }}
                            </a>

                            @if($hasVariant)
                                <span class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full border border-purple-200 whitespace-nowrap">
                                    تنوع
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- ستون قیمت خرید (ماشین حساب سریع) --}}
                    <td class="px-4 py-3">
                        <input type="text"
                               wire:model="purchasePrices.{{ $product->id }}"
                               wire:key="purchase-{{ $product->id }}"
                               wire:change="calculatePriceFromPurchase({{ $product->id }})"
                               class="w-24 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                               placeholder="قیمت خرید"
                               x-data
                               x-on:input.debounce.500ms="$wire.calculatePriceFromPurchase({{ $product->id }})">
                    </td>

                    {{-- ستون قیمت اصلی --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <input type="text"
                                   wire:model="prices.{{ $product->id }}.price"
                                   wire:key="price-{{ $product->id }}"
                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                                   placeholder="قیمت">
                            {{-- نمایش قیمت قبلی --}}
                            @if(isset($previousPrices[$product->id]['price_previous']) && $previousPrices[$product->id]['price_previous'])
                                <div class="text-xs text-gray-500 flex flex-col">
                                    <span>قبلی: {{ english_to_persian_num(number_format($previousPrices[$product->id]['price_previous'])) }} تومان</span>
                                    @if(isset($priceUpdates[$product->id]['price_updated_at']) && $priceUpdates[$product->id]['price_updated_at'])
                                        <span class="text-[10px] text-gray-400">
                                            {{ english_to_persian_num(verta($priceUpdates[$product->id]['price_updated_at'])->format('H:i - Y/m/d')) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ستون قیمت تخفیفی --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <input type="text"
                                   wire:model="prices.{{ $product->id }}.discounted_price"
                                   wire:key="discount-{{ $product->id }}"
                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                                   placeholder="تخفیف">
                            @if(isset($previousPrices[$product->id]['discounted_price_previous']) && $previousPrices[$product->id]['discounted_price_previous'])
                                <div class="text-xs text-gray-500">
                                    قبلی: {{ english_to_persian_num(number_format($previousPrices[$product->id]['discounted_price_previous'])) }} تومان
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ستون قیمت عمده --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <input type="text"
                                   wire:model="prices.{{ $product->id }}.bulk_price"
                                   wire:key="bulk-{{ $product->id }}"
                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                                   placeholder="عمده">
                            @if(isset($previousPrices[$product->id]['bulk_price_previous']) && $previousPrices[$product->id]['bulk_price_previous'])
                                <div class="text-xs text-gray-500">
                                    قبلی: {{ english_to_persian_num(number_format($previousPrices[$product->id]['bulk_price_previous'])) }} تومان
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ستون قیمت اقساطی --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <input type="text"
                                   wire:model="prices.{{ $product->id }}.installment_price"
                                   wire:key="installment-{{ $product->id }}"
                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                                   placeholder="اقساطی">
                            @if(isset($previousPrices[$product->id]['installment_price_previous']) && $previousPrices[$product->id]['installment_price_previous'])
                                <div class="text-xs text-gray-500">
                                    قبلی: {{ english_to_persian_num(number_format($previousPrices[$product->id]['installment_price_previous'])) }} تومان
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ستون قیمت اقساطی تخفیفی --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <input type="text"
                                   wire:model="prices.{{ $product->id }}.discounted_installment_price"
                                   wire:key="discount-installment-{{ $product->id }}"
                                   class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                                   placeholder="اقساطی تخفیف">
                            @if(isset($previousPrices[$product->id]['discounted_installment_price_previous']) && $previousPrices[$product->id]['discounted_installment_price_previous'])
                                <div class="text-xs text-gray-500">
                                    قبلی: {{ english_to_persian_num(number_format($previousPrices[$product->id]['discounted_installment_price_previous'])) }} تومان
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- ستون عملیات --}}
                    <td class="px-4 py-3">
                        <button wire:click="updatePrice({{ $product->id }})"
                                wire:target="updatePrice({{ $product->id }})"
                                wire:loading.attr="disabled"
                                wire:key="save-{{ $product->id }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border border-green-400 text-green-500 cursor-pointer hover:bg-green-100 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="updatePrice({{ $product->id }})">💾</span>
                            <span wire:loading wire:target="updatePrice({{ $product->id }})" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            ذخیره
                        </button>
                    </td>

                    {{-- ستون موجودی --}}
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        @if($stock > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                                ✓ {{ english_to_persian_num(number_format($stock)) }} عدد
                            </span>
                        @elseif($stock == 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                ✗ ناموجود
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                                ⚠ نامشخص
                            </span>
                        @endif
                    </td>

                    {{-- ستون تصویر --}}
                    <td class="px-4 py-3">
                        @if(Storage::disk('public')->exists('products/' . $product->id . '/small/1.webp'))
                            <img width="50" height="50" class="rounded-lg object-cover shadow-sm"
                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                 alt="{{ $product['title'] }}">
                        @else
                            <span class="text-gray-400 text-xs">بدون تصویر</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($products->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <p class="text-gray-500">هیچ محصولی یافت نشد</p>
            </div>
        @endif
    </div>
</div>