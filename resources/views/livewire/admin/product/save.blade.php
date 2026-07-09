@push('editor')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-rtl@1.0.0/dist/quill-rtl.min.js"></script>

    <style>
        .ql-editor {
            direction: rtl;
            text-align: right;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100px;
        }
        .ql-toolbar.ql-snow {
            direction: rtl;
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .ql-container.ql-snow {
            border-radius: 0 0 0.75rem 0.75rem;
        }
        .ql-toolbar.ql-snow .ql-formats {
            margin-left: 15px;
            margin-right: 0;
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }
        .nav-btn:hover:not(.disabled) {
            background-color: rgba(255,255,255,0.2);
            border-radius: 12px;
        }
        .nav-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .icon {
            font-size: 18px;
            line-height: 1;
        }

        /* استایل برای کامبوباکس جستجو */
        .searchable-select {
            position: relative;
        }
        .searchable-select .dropdown-menu {
            position: absolute;
            z-index: 50;
            width: 100%;
            margin-top: 4px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow: hidden;
        }
        .searchable-select .dropdown-search {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
        }
        .searchable-select .dropdown-search input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 6px 12px;
            font-size: 0.875rem;
            outline: none;
        }
        .searchable-select .dropdown-search input:focus {
            border-color: #8B5CF6;
            ring: 1px solid #8B5CF6;
        }
        .searchable-select .dropdown-options {
            overflow-y: auto;
            max-height: 150px;
        }
        .searchable-select .dropdown-options .option-item {
            padding: 8px 16px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.15s;
        }
        .searchable-select .dropdown-options .option-item:hover {
            background-color: #f3f4f6;
        }
        .searchable-select .dropdown-options .option-item.selected {
            background-color: #EDE9FE;
            color: #5B21B6;
        }
        .searchable-select .dropdown-options .no-result {
            padding: 8px 16px;
            text-align: center;
            color: #9CA3AF;
            font-size: 0.875rem;
        }
        .searchable-select .selected-display {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            background: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }
        .searchable-select .selected-display:focus {
            border-color: #8B5CF6;
            ring: 1px solid #8B5CF6;
        }
        .searchable-select .selected-display .arrow {
            transition: transform 0.2s;
        }
        .searchable-select .selected-display .arrow.open {
            transform: rotate(180deg);
        }

        .price-history {
            font-size: 0.7rem;
            color: #6B7280;
            margin-top: 2px;
            padding-right: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 8px;
        }
        .price-history span {
            background: #F3F4F6;
            padding: 1px 8px;
            border-radius: 12px;
        }
    </style>
@endpush

<div>
    {{-- دکمه‌های قبلی و بعدی --}}
    <div class="flex justify-between w-full mb-4">
        @php
            $prevId = $product->id - 1;
            $nextId = $product->id + 1;
            $prevProduct = \App\Models\Product::find($prevId);
            $nextProduct = \App\Models\Product::find($nextId);
        @endphp

        <div class="bg-gray-400 rounded-2xl px-2 py-1 text-white">
            <a href="{{ $prevProduct ? route('admin.product.save', $prevProduct->id) : '#' }}"
               class="nav-btn prev {{ !$prevProduct ? 'disabled' : '' }}"
               title="محصول قبلی">
                <span class="icon">→</span>
                <span class="label">{{ $prevProduct ? $prevProduct->title : 'محصول قبلی' }}</span>
            </a>
        </div>

        <div class="bg-gray-400 rounded-2xl px-2 py-1 text-white">
            <a href="{{ $nextProduct ? route('admin.product.save', $nextProduct->id) : '#' }}"
               class="nav-btn next {{ !$nextProduct ? 'disabled' : '' }}"
               title="محصول بعدی">
                <span class="label">{{ $nextProduct ? $nextProduct->title : 'محصول بعدی' }}</span>
                <span class="icon">←</span>
            </a>
        </div>
    </div>

    <div class="sm:flex sm:flex-wrap justify-between">
        {{-- دسته بندی با جستجو --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">دسته بندی</small>
            <div class="searchable-select" x-data="{
                open: false,
                search: '',
                selected: @entangle('categoryId').defer,
                options: @js(\App\Models\Category::whereNotNull('parent_id')->pluck('title', 'id')->prepend('دسته بندی', '')->toArray()),
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
                    $wire.set('categoryId', key);
                }
            }">
                <div class="relative">
                    <div @click="open = !open"
                         class="selected-display">
                        <span x-text="options[selected] || 'انتخاب کنید'"></span>
                        <svg class="arrow w-4 h-4 text-gray-400" :class="open ? 'open' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <div x-show="open" @click.away="open = false"
                         class="dropdown-menu">
                        <div class="dropdown-search">
                            <input type="text"
                                   x-model="search"
                                   @click.stop
                                   placeholder="جستجوی دسته بندی..."
                                   class="w-full rounded-xl border border-gray-300 px-3 py-1.5 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500">
                        </div>
                        <div class="dropdown-options">
                            <template x-for="(label, key) in filteredOptions" :key="key">
                                <div @click="selectOption(key)"
                                     class="option-item"
                                     :class="selected === key ? 'selected' : ''">
                                    <span x-text="label"></span>
                                </div>
                            </template>
                            <div x-show="Object.keys(filteredOptions).length === 0"
                                 class="no-result">
                                موردی یافت نشد
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @error('categoryId')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- برند با جستجو --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">برند</small>
            <div class="searchable-select" x-data="{
                open: false,
                search: '',
                selected: @entangle('brandId').defer,
                options: @js(\App\Models\Brand::where('status', true)->orderBy('order')->pluck('name', 'id')->prepend('انتخاب برند', '')->toArray()),
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
                    $wire.set('brandId', key);
                }
            }">
                <div class="relative">
                    <div @click="open = !open"
                         class="selected-display">
                        <span x-text="options[selected] || 'انتخاب کنید'"></span>
                        <svg class="arrow w-4 h-4 text-gray-400" :class="open ? 'open' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <div x-show="open" @click.away="open = false"
                         class="dropdown-menu">
                        <div class="dropdown-search">
                            <input type="text"
                                   x-model="search"
                                   @click.stop
                                   placeholder="جستجوی برند..."
                                   class="w-full rounded-xl border border-gray-300 px-3 py-1.5 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500">
                        </div>
                        <div class="dropdown-options">
                            <template x-for="(label, key) in filteredOptions" :key="key">
                                <div @click="selectOption(key)"
                                     class="option-item"
                                     :class="selected === key ? 'selected' : ''">
                                    <span x-text="label"></span>
                                </div>
                            </template>
                            <div x-show="Object.keys(filteredOptions).length === 0"
                                 class="no-result">
                                موردی یافت نشد
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @error('brandId')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- تنوع --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">تنوع</small>
            <input type="text" class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="تنوع" wire:model.blur="variant">
            @error('variant')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- کد کالا --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">کد کالا</small>
            <input type="text" class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="کد کالا" wire:model="code">
            @error('code')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- عنوان و آدرس --}}
        <div class="sm:w-full p-1">
            <small class="pr-2">عنوان و آدرس</small>
            <input type="text" class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="عنوان و آدرس" wire:model="title">
            @error('title')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- لینک ترب --}}
        <div class="sm:w-full p-1">
            <small class="pr-2">لینک ترب</small>
            <input type="url" class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="https://torob.com/..." wire:model="torob_url">
            @error('torob_url')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- توضیحات --}}
        <div class="sm:w-full p-1 pb-20" wire:ignore>
            <small class="mr-2 text-gray-700 font-medium mb-1">توضیحات</small>
            <div id="editor" class="bg-white rounded-xl shadow-sm"></div>
            @error('description')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- بخش قیمت‌ها --}}
    <div class="sm:flex sm:flex-wrap justify-between mt-4 border-t border-gray-200 pt-4">
        <h3 class="w-full text-lg font-bold text-gray-700 mb-3 pr-2">💰 قیمت‌ها</h3>

        {{-- قیمت اصلی --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">قیمت اصلی <span class="text-red-500">*</span></small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت اصلی به تومان" wire:model="prices.price">
            @error('prices.price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
            @if($product->exists && $product->price)
                <div class="price-history">
                    <span>قبلی: {{ english_to_persian_num(number_format($product->price_previous ?? 0)) }} تومان</span>
                    <span>بروزرسانی: {{ english_to_persian_num(verta($product->price_updated_at)->format('Y-m-d H:i:s')) }}</span>
                </div>
            @endif
        </div>

        {{-- قیمت عمده --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">قیمت عمده</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت عمده به تومان" wire:model="prices.bulk_price">
            @error('prices.bulk_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
            @if($product->exists && $product->bulk_price)
                <div class="price-history">
                    <span>قبلی: {{ english_to_persian_num(number_format($product->bulk_price_previous ?? 0)) }} تومان</span>
                    <span>بروزرسانی: {{ english_to_persian_num(verta($product->bulk_price_updated_at)->format('H:i:s')) }}</span>
                </div>
            @endif
        </div>

        {{-- قیمت اقساطی --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">قیمت اقساطی</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت اقساطی به تومان" wire:model="prices.installment_price">
            @error('prices.installment_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
            @if($product->exists && $product->installment_price)
                <div class="price-history">
                    <span>قبلی: {{ english_to_persian_num(number_format($product->installment_price_previous ?? 0)) }} تومان</span>
                    <span>بروزرسانی: {{ english_to_persian_num(verta($product->installment_price_updated_at)->format('H:i:s')) }}</span>
                </div>
            @endif
        </div>

        {{-- قیمت تخفیف‌خورده --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">قیمت تخفیف‌خورده</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت تخفیف‌خورده به تومان" wire:model="prices.discounted_price">
            @error('prices.discounted_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
            @if($product->exists && $product->discounted_price)
                <div class="price-history">
                    <span>قبلی: {{ english_to_persian_num(number_format($product->discounted_price_previous ?? 0)) }} تومان</span>
                    <span>بروزرسانی: {{ english_to_persian_num(verta($product->discounted_price_updated_at)->format('H:i:s')) }}</span>
                </div>
            @endif
        </div>

        {{-- قیمت اقساطی تخفیف‌خورده --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">قیمت اقساطی تخفیف‌خورده</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت اقساطی تخفیف‌خورده به تومان" wire:model="prices.discounted_installment_price">
            @error('prices.discounted_installment_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
            @if($product->exists && $product->discounted_installment_price)
                <div class="price-history">
                    <span>قبلی: {{ english_to_persian_num(number_format($product->discounted_installment_price_previous ?? 0)) }} تومان</span>
                    <span>بروزرسانی: {{ english_to_persian_num(verta($product->discounted_installment_price_updated_at)->format('H:i:s')) }}</span>
                </div>
            @endif
        </div>

        {{-- موجودی --}}
        <div class="sm:w-4/12 p-1">
            <small class="pr-2">موجودی</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="موجودی" wire:model="stock" @if($variant) disabled @endif>
            @error('stock')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- بخش اندازه و وزن --}}
    <div class="sm:flex sm:flex-wrap justify-between mt-4 border-t border-gray-200 pt-4">
        <h3 class="w-full text-lg font-bold text-gray-700 mb-3 pr-2">📦 ابعاد و وزن</h3>

        {{-- اندازه کارتن با جستجو --}}
        <div class="sm:w-6/12 p-1">
            <small class="pr-2">حداقل اندازه کارتن پستی</small>
            <div class="searchable-select" x-data="{
                open: false,
                search: '',
                selected: @entangle('size').defer,
                options: {
                    '0': 'انتخاب کنید',
                    '1': 'کارتن پستی سایز 1',
                    '2': 'کارتن پستی سایز 2',
                    '3': 'کارتن پستی سایز 3',
                    '4': 'کارتن پستی سایز 4',
                    '5': 'کارتن پستی سایز 5',
                    '6': 'کارتن پستی سایز 6',
                    '7': 'کارتن پستی سایز 7',
                    '8': 'کارتن پستی سایز 8',
                    '9': 'کارتن پستی سایز 9'
                },
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
                    $wire.set('size', key);
                }
            }">
                <div class="relative">
                    <div @click="open = !open"
                         class="selected-display">
                        <span x-text="options[selected] || 'انتخاب کنید'"></span>
                        <svg class="arrow w-4 h-4 text-gray-400" :class="open ? 'open' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <div x-show="open" @click.away="open = false"
                         class="dropdown-menu">
                        <div class="dropdown-search">
                            <input type="text"
                                   x-model="search"
                                   @click.stop
                                   placeholder="جستجوی سایز کارتن..."
                                   class="w-full rounded-xl border border-gray-300 px-3 py-1.5 text-sm focus:border-pars-500 focus:ring-1 focus:ring-pars-500">
                        </div>
                        <div class="dropdown-options">
                            <template x-for="(label, key) in filteredOptions" :key="key">
                                <div @click="selectOption(key)"
                                     class="option-item"
                                     :class="selected === key ? 'selected' : ''">
                                    <span x-text="label"></span>
                                </div>
                            </template>
                            <div x-show="Object.keys(filteredOptions).length === 0"
                                 class="no-result">
                                موردی یافت نشد
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @error('size')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- وزن --}}
        <div class="sm:w-6/12 p-1">
            <small class="pr-2">وزن به گرم <span class="text-red-500">*</span></small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="وزن به گرم" wire:model="weight">
            @error('weight')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- ویژگی‌ها و صفحات --}}
    <div class="sm:flex sm:flex-wrap justify-between mt-4 border-t border-gray-200 pt-4">
        <h3 class="w-full text-lg font-bold text-gray-700 mb-3 pr-2">🏷️ ویژگی‌ها و صفحات</h3>

        {{-- ویژگی های محصول --}}
        <div class="relative sm:w-1/2 p-1">
            <small class="pr-2">ویژگی های محصول</small>
            <div class="w-full relative rounded-2xl bg-white border border-gray-300">
                <div class="px-2 flex flex-wrap items-center gap-1 min-h-[42px]">
                    @foreach ($selectedAttrs as $attributeId => $data)
                        <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-pars-100 text-pars-700">
                            <span>{{ $data['attribute_name'] }} : {{ $data['value'] }}</span>
                            <button wire:click="removeAttr({{ $attributeId }})" class="text-red-600 hover:text-red-800" title="حذف">🗑️</button>
                        </div>
                    @endforeach
                    <input class="flex-1 min-w-[100px] outline-none py-2" type="text" wire:model.live.debounce.300ms="queryAttr" wire:focus="focusAttr" wire:click.away="blurAttr" placeholder="جستجوی ویژگی ها...">
                </div>
                @if ($isFocusedAttr && !empty($attrs))
                    <ul class="absolute z-10 bg-white mt-1 rounded shadow w-full max-h-60 overflow-y-auto">
                        @foreach ($attrs as $item)
                            <li wire:click="selectAttr({{ $item['attribute_id'] }}, {{ $item['value_id'] }}, '{{ $item['attribute_name'] }}', '{{ $item['value'] }}')" class="px-2 py-1 cursor-pointer hover:bg-pars-400 hover:text-pars-500">
                                {{ $item['attribute_name'] }} : {{ $item['value'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- صفحات نمایش دهنده --}}
        <div class="relative sm:w-1/2 p-1">
            <small class="pr-2">صفحات نمایش دهنده این محصول <span class="text-red-500">*</span></small>
            <div class="w-full relative rounded-2xl bg-white border border-gray-300">
                <div class="px-2 flex flex-wrap items-center gap-1 min-h-[42px]">
                    @foreach ($selectedUrls as $key => $value)
                        <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-pars-100 text-pars-700">
                            <span>{{ $value }}</span>
                            <button wire:click="removeUrl({{ $key }})" class="text-red-600 hover:text-red-800" title="حذف">🗑️</button>
                        </div>
                    @endforeach
                    <input class="flex-1 min-w-[100px] outline-none py-2" type="text" wire:focus="focus" wire:click.away="blur" wire:model.live.debounce.300ms="query" placeholder="جستجوی سردسته ها...">
                </div>
                @if ($isFocused && !empty($urls))
                    <ul class="absolute z-10 bg-white mt-1 rounded shadow w-full max-h-60 overflow-y-auto">
                        @foreach ($urls as $key => $value)
                            <li wire:click="selectUrl({{ $key }})" class="px-2 py-1 cursor-pointer hover:bg-pars-400 hover:text-pars-500">
                                {{ $value }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @error('selectedUrls')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- ویژگی‌های تنوع --}}
    @if($variant)
        <div class="w-full rounded-2xl p-2 mt-4 border border-gray-200">
            <h3 class="font-bold text-gray-700 mb-2">🔹 ویژگی‌های تنوع</h3>
            @foreach($variants as $i => $variantItem)
                <div class="flex flex-wrap gap-2 items-center mb-2">
                    <span class="w-8 text-gray-500">{{ $i+1 }} .</span>
                    <input type="text" class="rounded-2xl flex-1 min-w-[150px] pr-2 border border-gray-300" placeholder="نام ویژگی (مثلاً قرمز، بتمن)" wire:model="variants.{{ $i }}.name">
                    @error('variants.' . $i . '.name')
                    <span class="text-xs text-red-500 font-semibold w-full">{{ $message }}</span>
                    @enderror
                    <input type="number" x-on:wheel.prevent class="rounded-2xl w-32 pr-2 border border-gray-300" placeholder="موجودی" wire:model="variants.{{ $i }}.stock">
                    @error('variants.' . $i . '.stock')
                    <span class="text-xs text-red-500 font-semibold w-full">{{ $message }}</span>
                    @enderror
                    <button type="button" class="text-red-500 font-bold px-2 cursor-pointer hover:text-red-700" wire:click="removeVariant({{ $i }})">🗑️ حذف</button>
                </div>
            @endforeach
            <button type="button" class="bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white text-sm px-4 py-1.5 rounded-2xl cursor-pointer" wire:click="addVariant">+ افزودن ویژگی</button>
        </div>
    @endif

    {{-- دکمه ذخیره --}}
    <div class="w-full text-center my-4">
        <button class="w-1/2 rounded-2xl p-2 cursor-pointer bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white transition-all font-bold shadow-lg" wire:click="save()">
            💾 ذخیره و آپلود عکس ها
        </button>
    </div>

    {{-- آپلود عکس --}}
    @if($product->id)
        <div class="sm:flex sm:flex-wrap gap-2">
            @foreach($product->variants as $value)
                <livewire:admin.product.upload :product="$product" :variant="$value"/>
            @endforeach
            <livewire:admin.product.upload :product="$product"/>
        </div>
    @endif
</div>

{{-- اسکریپت Quill --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            direction: 'rtl',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                ]
            }
        });

        quill.root.innerHTML = `{!! $description !!}`;

        quill.on('text-change', function() {
            @this.set('description', quill.root.innerHTML);
        });

        Livewire.on('descriptionUpdated', (content) => {
            if (quill.root.innerHTML !== content) {
                quill.root.innerHTML = content;
            }
        });

    });
</script>