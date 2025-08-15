<div>
    <div class="flex">
        <input type="text" class="w-full rounded-2xl" placeholder="عنوان آدرس" wire:model="title">
        <button class="bg-pars-500 hover:bg-pars-600 text-white px-4 rounded-2xl mr-2"
                wire:click.prevent="save()">ثبت
        </button>
    </div>
    @error('title')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
    <div class="flex p-4 justify-between">
        <div class="w-full flex flex-col ml-1">
            <select class="border-orange-400 bg-white border px-8 rounded-2xl"  wire:model.live="menuId">
                <option value="0">انتخاب کنید</option>
                @foreach($menus as $value)
                    <option value="{{ $value->id }}" wire:key="{{ $value->id }}">{{ $value->title }}</option>
                @endforeach
            </select>
            @error('menuId')
            <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

    </div>
    <ul class="flex flex-wrap mt-6 overflow-y-scroll h-[33vh]">
        @foreach($urls as $value)
            <li class="bg-pars-400 text-pars-500 p-1 rounded-md m-1 cursor-pointer" wire:key="{{ $value->id }}"
                wire:click.prevent="setUrl({{ $value->id }})">{{ $value->title }}</li>
        @endforeach
    </ul>
</div>
