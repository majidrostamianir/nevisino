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
        {{-- دسته بندی --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">دسته بندی</small>
            <select class="w-full rounded-2xl border border-gray-300" wire:model.live="categoryId">
                <option value="{{ null }}">دسته بندی</option>
                @foreach(\App\Models\Category::query()->whereNotNull('parent_id')->get() as $value)
                    <option value="{{ $value->id }}">{{ $value->title }}</option>
                @endforeach
            </select>
            @error('categoryId')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- برند --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">برند</small>
            <select class="w-full rounded-2xl border border-gray-300" wire:model="brandId">
                <option value="">انتخاب برند</option>
                @foreach(\App\Models\Brand::where('status', true)->orderBy('order')->get() as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
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

        {{-- اندازه کارتن --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">حداقل اندازه کارتن پستی</small>
            <select class="w-full rounded-2xl border border-gray-300" wire:model="size">
                <option value="0">انتخاب کنید</option>
                <option value="1">کارتن پستی سایز 1</option>
                <option value="2">کارتن پستی سایز 2</option>
                <option value="3">کارتن پستی سایز 3</option>
                <option value="4">کارتن پستی سایز 4</option>
                <option value="5">کارتن پستی سایز 5</option>
                <option value="6">کارتن پستی سایز 6</option>
                <option value="7">کارتن پستی سایز 7</option>
                <option value="8">کارتن پستی سایز 8</option>
                <option value="9">کارتن پستی سایز 9</option>
            </select>
            @error('size')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- وزن --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">وزن به گرم</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="وزن به گرم" wire:model="weight">
            @error('weight')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- قیمت --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">قیمت به تومان</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت به تومان" wire:model="price">
            @error('price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror

            {{-- نمایش قیمت قبلی و تاریخ بروزرسانی --}}
            @if($product->exists && $product->previous_price)
                <div class="text-xs text-gray-500 mt-1 pr-2">
                    <span>قیمت قبلی: {{ english_to_persian_num(number_format($product->previous_price)) }} تومان</span>
                    <span class="mx-1">|</span>
                    <span>آخرین بروزرسانی: {{ english_to_persian_num(verta($product->price_updated_at)->format('H:i:s'))  }}</span>
                </div>
            @endif
        </div>

        {{-- قیمت با تخفیف --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">قیمت با تخفیف به تومان</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="قیمت با تخفیف به تومان" wire:model="discounted_price">
            @error('discounted_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- موجودی --}}
        <div class="sm:w-3/12 p-1">
            <small class="pr-2">موجودی</small>
            <input type="number" x-on:wheel.prevent class="w-full rounded-2xl bg-white pr-2 border border-gray-300" placeholder="موجودی" wire:model="stock" @if($variant) disabled @endif>
            @error('stock')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>

        {{-- ویژگی های محصول --}}
        <div class="relative sm:w-1/3 p-1">
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
        <div class="relative sm:w-1/3 p-1">
            <small class="pr-2">صفحات نمایش دهنده این محصول</small>
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
            <h3 class="font-bold text-gray-700 mb-2">ویژگی‌های تنوع</h3>
            @foreach($variants as $i => $variantItem)
                <div class="flex flex-wrap gap-2 items-center mb-2">
                    <span class="w-8">{{ $i+1 }} .</span>
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