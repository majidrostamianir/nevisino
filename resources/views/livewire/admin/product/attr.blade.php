<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    {{-- فرم ثبت ویژگی --}}
    <div class="flex flex-wrap gap-4 mb-6 p-4 bg-pars-100 rounded-2xl">
        <div class="md:flex w-full gap-4">
            <div class="sm:w-1/3 flex flex-col">
                <small class="mr-2 text-gray-700 font-medium mb-1">دسته بندی</small>
                <select class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                        wire:model.live="categoryId">
                    <option value="{{ null }}">انتخاب دسته بندی</option>
                    @foreach(\App\Models\Category::query()->whereNotNull('parent_id')->get() as $value)
                        <option value="{{ $value->id }}">{{ $value->title }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="sm:w-1/3 flex flex-col">
                <small class="mr-2 text-gray-700 font-medium mb-1">عنوان ویژگی</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="مثال: رنگ"
                       wire:model="title">
                @error('title')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="sm:w-1/3 flex flex-col">
                <small class="mr-2 text-gray-700 font-medium mb-1">مقدار ویژگی</small>
                <input type="text"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all"
                       placeholder="مثال: قرمز"
                       wire:model="value">
                @error('value')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="sm:w-1/3 flex flex-col justify-end">
                <button class="w-full bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md mt-6">
                    ثبت ویژگی
                </button>
            </div>
        </div>
    </div>

    {{-- جدول ویژگی‌ها با تم جدید --}}
    <div class="rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">#</th>
                <th class="px-4 py-3 text-sm font-semibold">دسته بندی</th>
                <th class="px-4 py-3 text-sm font-semibold">عنوان</th>
                <th class="px-4 py-3 text-sm font-semibold">مقدار</th>
                <th class="px-4 py-3 text-sm font-semibold">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($attrs as $index => $value)
                <tr wire:key="{{ $value->id }}"
                    class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-600">{{ english_to_persian_num($value->id) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium  text-pars-700">
                            {{ \App\Models\Category::find($value->category_id)->title }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="cursor-pointer hover:text-pars-600 transition-colors font-medium text-gray-800"
                              wire:click="setAttr('{{ $value->id }}')">
                            {{ $value->title }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 cursor-pointer hover:text-pars-600 transition-colors"
                        wire:click="setAttr('{{ $value->id }}')">
                        {{ $value->value }}
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="delAttr('{{ $value->id }}')"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors cursor-pointer"
                                onclick="return confirm('آیا از حذف این ویژگی مطمئن هستید؟')">
                            🗑️ حذف
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($attrs->isEmpty())
            <div class="text-center py-12 bg-gray-50">
                <p class="text-gray-500">هیچ ویژگی‌ای یافت نشد</p>
            </div>
        @endif
    </div>
</div>
4
