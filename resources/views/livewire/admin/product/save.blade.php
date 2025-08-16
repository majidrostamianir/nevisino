<div>
    <div class="sm:flex justify-between">
        <div>
            <select class="rounded-2xl m-2" wire:model.live="categoryId">
                <option value="{{ null }}">دسته بندی</option>
                @foreach(\App\Models\Category::query()->whereNotNull('parent_id')->get() as $value)
                    <option value="{{ $value->id }}">{{ $value->title }}</option>
                @endforeach
            </select>
            @error('categoryId')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="text" class="w-full rounded-2xl bg-white " placeholder="عنوان"
                   wire:model="title">
            @error('title')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="طول"
                   wire:model="length">
            @error('length')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="عرض"
                   wire:model="width">
            @error('width')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="ارتفاع"
                   wire:model="height">
            @error('height')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="وزن"
                   wire:model="weight">
            @error('weight')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="قیمت"
                   wire:model="price">
            @error('price')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="w-full m-2">
            <input type="number" class="w-full rounded-2xl bg-white " placeholder="موجودی"
                   wire:model="inventory">
            @error('inventory')
            <span class="text-xs text-red-500 font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="relative w-full m-2">
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
                        placeholder="جستجوی آدرس...">
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
    <div class="w-full text-center my-2">
        <button
            class="w-1/2 rounded-2xl  p-1.5 cursor-pointer bg-pars-500 hover:bg-pars-600 text-white transition-all "
            wire:click="save()">ذخیره و آپلود عکس ها
        </button>
    </div>
    @if($product->id)
        <livewire:admin.product.upload :product="$product" />
    @endif

</div>
