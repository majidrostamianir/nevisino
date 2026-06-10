<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    {{-- دو کادر اصلی --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4">

        {{-- کادر اول: مدیریت ویژگی‌ها --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-pars-500 to-pars-800 text-white px-4 py-3">
                <h3 class="font-bold">مدیریت ویژگی‌ها</h3>
            </div>
            <div class="p-4">
                <div class="flex flex-col gap-3">
                    <div>
                        <small class="mr-2 text-gray-700 font-medium">دسته بندی</small>
                        <select class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all mt-1" wire:model.live="categoryId">
                            <option value="">انتخاب دسته بندی</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                        @error('categoryId') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <small class="mr-2 text-gray-700 font-medium">نام ویژگی</small>
                        <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all mt-1" placeholder="مثال: رنگ، سایز، جنس" wire:model="attributeName">
                        @error('attributeName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="saveAttribute" class="bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md">
                            {{ $editingAttributeId ? 'ویرایش ویژگی' : 'افزودن ویژگی' }}
                        </button>
                        @if($editingAttributeId)
                            <button wire:click="cancelEditAttribute" class="bg-gray-500 hover:bg-gray-600 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md">
                                انصراف
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- کادر دوم: مدیریت مقادیر --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-pars-500 to-pars-800 text-white px-4 py-3">
                <h3 class="font-bold">مدیریت مقادیر ویژگی</h3>
            </div>
            <div class="p-4">
                <div class="flex flex-col gap-3">
                    <div>
                        <small class="mr-2 text-gray-700 font-medium">انتخاب ویژگی</small>
                        <select class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all mt-1" wire:model.live="selectedAttributeForValue">
                            <option value="">انتخاب ویژگی</option>
                            @foreach($allAttributes as $attr)
                                <option value="{{ $attr->id }}">{{ $attr->category->title }} - {{ $attr->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedAttributeForValue') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <small class="mr-2 text-gray-700 font-medium">مقدار</small>
                        <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pars-400 focus:border-transparent transition-all mt-1" placeholder="مثال: آبی، قرمز، سبز" wire:model="attributeValue">
                        @error('attributeValue') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="saveValue" class="bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md">
                            {{ $editingValueId ? 'ویرایش مقدار' : 'افزودن مقدار' }}
                        </button>
                        @if($editingValueId)
                            <button wire:click="cancelEditValue" class="bg-gray-500 hover:bg-gray-600 text-white text-md cursor-pointer px-6 py-2 rounded-xl transition-all shadow-md">
                                انصراف
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلتر دسته بندی برای لیست --}}
    <div class="px-4 pb-2">
        <div class="flex items-center gap-3 bg-white rounded-xl p-3 shadow">
            <label class="text-sm text-gray-700 font-medium">فیلتر بر اساس دسته بندی:</label>
            <select class="border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-pars-400" wire:model.live="filterCategoryId">
                <option value="">همه دسته بندی‌ها</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- جدول نمایش ویژگی‌ها و مقادیر --}}
    <div class="rounded-xl shadow-lg overflow-hidden m-4">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">دسته بندی</th>
                <th class="px-4 py-3 text-sm font-semibold">نام ویژگی</th>
                <th class="px-4 py-3 text-sm font-semibold">مقادیر (تعداد استفاده)</th>
                <th class="px-4 py-3 text-sm font-semibold">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($attributes as $attr)
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm">{{ $attr->category->title }}</td>
                    <td class="px-4 py-3 text-sm font-medium">{{ $attr->name }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex flex-wrap gap-1">
                            @foreach($attr->values as $val)
                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-pars-100 text-pars-700 group">
                                    <span>{{ $val->value }} ({{ english_to_persian_num($val->usage_count) }})</span>
                                    <button wire:click="editValue({{ $val->id }})" class="text-yellow-600 hover:text-yellow-800" title="ویرایش">✏️</button>
                                    <button wire:click="deleteValue({{ $val->id }})" class="text-red-600 hover:text-red-800" onclick="return confirm('حذف شود؟')" title="حذف">🗑️</button>
                                </div>
                            @endforeach
                            @if($attr->values->isEmpty())
                                <span class="text-gray-400 text-xs">بدون مقدار</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <button wire:click="editAttribute({{ $attr->id }})" class="text-yellow-600 hover:text-yellow-800 ml-2" title="ویرایش">✏️</button>
                        <button wire:click="deleteAttribute({{ $attr->id }})" class="text-red-600 hover:text-red-800" onclick="return confirm('ویژگی و تمام مقادیر آن حذف شود؟')" title="حذف">🗑️</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-12 text-gray-500">هیچ ویژگی‌ای یافت نشد</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>