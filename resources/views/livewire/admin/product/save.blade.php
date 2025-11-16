<div>
    <div class="sm:flex sm:flex-wrap justify-between">
        <div class="sm:w-3/12 p-1">
            <select class="w-full rounded-2xl" wire:model.live="categoryId">
                <option value="{{ null }}">دسته بندی</option>
                @foreach(\App\Models\Category::query()->whereNotNull('parent_id')->get() as $value)
                    <option value="{{ $value->id }}">{{ $value->title }}</option>
                @endforeach
            </select>
            @error('categoryId')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="text" class="w-full rounded-2xl bg-white pr-2" placeholder="ویژگی"
                   wire:model.blur="variant">
            @error('variant')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="text" class="w-full rounded-2xl bg-white pr-2" placeholder="عنوان و آدرس"
                   wire:model="title">
            @error('title')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="text" class="w-full rounded-2xl bg-white pr-2" placeholder="کد کالا"
                   wire:model="code">
            @error('code')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
           <select class="w-full rounded-2xl" wire:model="size">
               <option value="1">اندازه پاکت پستی</option>
           </select>
            @error('size')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="number" class="w-full rounded-2xl bg-white pr-2" placeholder="وزن"
                   wire:model="weight">
            @error('weight')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="number" class="w-full rounded-2xl bg-white pr-2" placeholder="قیمت"
                   wire:model="price">
            @error('price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="number" class="w-full rounded-2xl bg-white pr-2" placeholder="قیمت با تخفیف"
                   wire:model="discounted_price">
            @error('discounted_price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="sm:w-3/12 p-1 ">
            <input type="number" class="w-full rounded-2xl bg-white pr-2" placeholder="موجودی"
                   wire:model="stock" @if($variant) disabled @endif>
            @error('stock')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="relative sm:w-9/12 p-1">
            <div class="w-full relative rounded-2xl bg-white">
                <div class="px-2 flex flex-wrap items-center gap-1">
                    @foreach ($selectedUrls as $key => $value)
                        <div class="bg-pars-400 text-pars-500 px-2 py-1 mt-1 rounded flex items-center space-x-1">
                            <span>{{ $value }}</span>
                            <button wire:click="removeUrl({{ $key }})" class="text-red-500 pr-1 text-sm">x</button>
                        </div>
                    @endforeach
                    <input
                        class="rounded-2xl"
                        type="text"
                        wire:focus="focus"
                        wire:click.away="blur"
                        wire:model.live.debounce.300ms="query"
                        placeholder="جستجوی سردسته ها...">
                </div>
                @if ($isFocused && !empty($urls))
                    <ul class="absolute z-10 bg-white  mt-1 rounded shadow w-full max-h-60 overflow-y-auto">
                        @foreach ($urls as $key=> $value)
                            <li wire:click="selectUrl({{ $key }})"
                                class="px-2 py-1 cursor-pointer hover:bg-pars-400 hover:text-pars-500">
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

    @if($variant)
        <div class="w-full   rounded-2xl p-2">
            <h3 class="font-bold text-gray-700 mb-2">ویژگی ها</h3>
            @foreach($variants as $i => $variant)
                <div class="flex gap-2 items-center mb-2">
                    <span>{{ $i+1 }} .</span>
                    <input type="text" class="rounded-2xl w-1/3 pr-2"
                           placeholder="نام ویژگی (مثلاً قرمز،  بتمن)"
                           wire:model="variants.{{ $i }}.name">
                    @error('variants.' . $i  .'.name')
                    <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
{{--                    <input type="number" class="rounded-2xl w-1/4 pr-2"--}}
{{--                           placeholder="قیمت (اختیاری)"--}}
{{--                           wire:model="variants.{{ $i }}.price">--}}
{{--                    @error('variants.' . $i  .'.price')--}}
{{--                    <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>--}}
{{--                    @enderror--}}
                    <input type="number" class="rounded-2xl w-1/4 pr-2"
                           placeholder="موجودی"
                           wire:model="variants.{{ $i }}.stock">
                    @error('variants.' . $i  .'.stock')
                    <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                    <button type="button" class="text-red-500 font-bold px-2 cursor-pointer" wire:click="removeVariant({{ $i }})">
                        حذف
                    </button>
                </div>
            @endforeach

            <button type="button"
                    class="bg-pars-500 hover:bg-pars-600 cursor-pointer text-white text-sm px-3 py-1 rounded-2xl"
                    wire:click="addVariant">+ افزودن ویژگی
            </button>
        </div>
    @endif

    <div class="w-full text-center my-2">
        <button
            class="w-1/2 rounded-2xl  p-1.5 cursor-pointer bg-pars-500 hover:bg-pars-600 text-white transition-all "
            wire:click="save()">ذخیره و آپلود عکس ها
        </button>
    </div>

    @if($product->id)
       <div class="sm:flex sm:flex-wrap">
           @foreach($product->variants as $value)
               <livewire:admin.product.upload :product="$product" :variant="$value"/>
           @endforeach
           <livewire:admin.product.upload :product="$product"/>
       </div>
    @endif

</div>
