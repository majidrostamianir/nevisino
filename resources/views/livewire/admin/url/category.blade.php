<div>
    <div class="flex items-start">
        <div class="flex flex-col w-full">
            <input type="text" class="w-full rounded-2xl" placeholder="عنوان دسته بندی" wire:model="title">
            @error('title')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="h-full">
            <button class="bg-pars-500 hover:bg-pars-600 text-white px-4 rounded-2xl h-[42px]"
                    wire:click.prevent="save()">ثبت
            </button>
        </div>
    </div>

    <ul class="flex flex-wrap mt-6">
        @foreach($categories as $value)
            <li class="bg-pars-400 text-pars-500 p-1 rounded-md m-1 cursor-pointer" wire:key="{{ $value->id }}"
                wire:click.prevent="setCategory({{ $value->id }})">{{ $value->title }}</li>
        @endforeach
    </ul>
</div>
