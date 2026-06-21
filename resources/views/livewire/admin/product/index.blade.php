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
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت اصلی</th>
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap">قیمت تخفیفی</th>
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
                            {{-- آیکون مشاهده در سایت --}}
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

                    {{-- ستون قیمت اصلی --}}
                    <td class="px-4 py-3">
                        <input type="text"
                               wire:model="prices.{{ $product->id }}.price"
                               wire:key="price-{{ $product->id }}"
                               class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                               placeholder="قیمت">
                    </td>

                    {{-- ستون قیمت تخفیفی --}}
                    <td class="px-4 py-3">
                        <input type="text"
                               wire:model="prices.{{ $product->id }}.discounted_price"
                               wire:key="discount-{{ $product->id }}"
                               class="w-28 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500"
                               placeholder="تخفیف">
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="updatePrice({{ $product->id }})"
                                wire:target="updatePrice({{ $product->id }})"
                                wire:loading.attr="disabled"
                                wire:key="save-{{ $product->id }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium  border border-green-400 text-green-500 cursor-pointer hover:bg-green-100  disabled:opacity-50 disabled:cursor-not-allowed">
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