<div>
    <div class="sm:flex">
        <div class="flex flex-col w-full ml-2">
            <input type="text" class="w-full pr-4 rounded-2xl" placeholder="عنوان دسته بندی" wire:model="title">
            @error('title')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col w-full ml-2">
            <input type="number" class="w-full pr-4 rounded-2xl" placeholder="سطح" wire:model="order">
            @error('order')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col w-full ml-2">
            <select class="w-full rounded-2xl pr-4" placeholder="" wire:model="parent_id">
                <option value="{{ null }}">سرگروه</option>
                @foreach($categories as $parent)
                    <option value="{{ $parent->id }}">{{$parent->title}}</option>
                @endforeach
            </select>
            @error('parent_id')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <button class="bg-pars-500 hover:bg-pars-600 text-white px-4 ml-2 rounded-2xl cursor-pointer"
                wire:click.prevent="save()">ثبت
        </button>
    </div>

    <ul class="mt-6">
        @foreach($categories as $parent)
            <li class="p-1 rounded-md m-1 w-fit bg-pars-500 text-white cursor-pointer"
                wire:key="cat-{{ $category->id }}"
                wire:click.prevent="changeCategory({{ $parent->id }})">
                {{ $parent->title }}
                <br>
                {{$parent->order}}
            </li>

            @if($parent->children->count())
                <ul class="mr-6 mt-1 mb-5 flex">
                    @foreach($parent->children as $child)
                        <li class="bg-pars-400 text-pars-600 p-1 rounded-md m-1 cursor-pointer"
                            wire:key="{{ $child->id }}"
                            wire:click.prevent="changeCategory({{ $child->id }})">
                            {{ $child->title }}
                            <br>
                            {{$child->order}}
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </ul>
</div>
