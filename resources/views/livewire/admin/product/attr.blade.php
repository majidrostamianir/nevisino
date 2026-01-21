<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    <div class="flex flex-wrap gap-4 mb-6 p-4 bg-pars-100 rounded-2xl">
        <div class="md:flex w-full gap-4">
            <div class="sm:w-1/3 ">
                <small class="pr-2">دسته بندی</small>
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
            <div class="sm:w-1/3 ">
                <small class="mr-2">عنوان ویژگی</small>
                <input
                    type="text"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    placeholder="عنوان"
                    wire:model="title">
                @error('title')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="sm:w-1/3 ">
                <small class="mr-2">مقدار ویژگی</small>
                <input
                    type="text"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400"
                    placeholder="مقدار"
                    wire:model="value">
                @error('value')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="sm:w-1/3 ">
                <div class="mt-6"></div>
                <button
                    class="w-full bg-pars-500 hover:bg-pars-600 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all"
                    wire:click.prevent="save()">
                    ثبت
                </button>
            </div>
        </div>
    </div>

    <!-- جدول داده‌ها -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full border-collapse text-sm text-right">
            <thead class="bg-pars-400 text-white">
            <tr>
                <th class="px-4 py-3 font-semibold">#</th>
                <th class="px-4 py-3 font-semibold">دسته بندی</th>
                <th class="px-4 py-3 font-semibold">عنوان</th>
                <th class="px-4 py-3 font-semibold">مقدار</th>
                <th class="px-4 py-3 font-semibold">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($attrs as $index => $value)
                <tr
                    wire:key="{{ $value->id }}"
                    class=" hover:bg-pars-50 border-b border-gray-100 transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ \App\Models\Category::find($value->category_id)->title }}</td>
                    <td class="px-4 py-3 text-gray-600 cursor-pointer" wire:click="setAttr('{{ $value->id }}')" >{{ $value->title }}</td>
                    <td class="px-4 py-3 text-gray-600 cursor-pointer" wire:click="setAttr('{{ $value->id }}')" >{{ $value->value }}</td>
                    <td class="px-4 py-3 text-red-600 cursor-pointer" wire:click="delAttr('{{ $value->id }}')" >حذف</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
