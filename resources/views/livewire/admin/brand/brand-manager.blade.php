<div class="h-[86vh] w-full bg-pars-100 rounded-2xl shadow-md overflow-y-auto">
    {{-- فرم افزودن/ویرایش برند --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden m-4">
        <div class="bg-gradient-to-r from-pars-500 to-pars-800 text-white px-4 py-3">
            <h3 class="font-bold">{{ $editingId ? 'ویرایش برند' : 'افزودن برند جدید' }}</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <small class="mr-2 text-gray-700 font-medium">نام برند</small>
                    <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="name">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <small class="mr-2 text-gray-700 font-medium">slug</small>
                    <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="slug">
                    @error('slug') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <small class="mr-2 text-gray-700 font-medium">لوگو (آدرس)</small>
                    <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="logo" placeholder="/storage/brands/logo.png">
                    @error('logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <small class="mr-2 text-gray-700 font-medium">وبسایت</small>
                    <input type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="website" placeholder="https://example.com">
                    @error('website') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <small class="mr-2 text-gray-700 font-medium">توضیحات</small>
                    <textarea rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="description"></textarea>
                    @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <small class="mr-2 text-gray-700 font-medium">ترتیب</small>
                    <input type="number" class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="order">
                </div>
                <div>
                    <small class="mr-2 text-gray-700 font-medium">وضعیت</small>
                    <select class="w-full border border-gray-300 rounded-xl px-3 py-2 mt-1" wire:model="status">
                        <option value="1">فعال</option>
                        <option value="0">غیرفعال</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="save" class="bg-gradient-to-r from-pars-500 to-pars-800 hover:from-pars-600 hover:to-pars-900 text-white px-6 py-2 rounded-xl">
                    {{ $editingId ? 'بروزرسانی' : 'ذخیره' }}
                </button>
                @if($editingId)
                    <button wire:click="$set('editingId', null)" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-xl">
                        انصراف
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- جستجو --}}
    <div class="m-4">
        <input type="text" wire:model.live.debounce.500ms="searchQuery" class="w-full border border-gray-300 rounded-xl px-3 py-2" placeholder="جستجوی برند...">
    </div>

    {{-- جدول برندها --}}
    <div class="rounded-xl shadow-lg overflow-hidden m-4">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">#</th>
                <th class="px-4 py-3 text-sm font-semibold">نام برند</th>
                <th class="px-4 py-3 text-sm font-semibold">slug</th>
                <th class="px-4 py-3 text-sm font-semibold">وضعیت</th>
                <th class="px-4 py-3 text-sm font-semibold">ترتیب</th>
                <th class="px-4 py-3 text-sm font-semibold">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($brands as $index => $brand)
                <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm">{{ english_to_persian_num($index + 1) }}</td>
                    <td class="px-4 py-3 text-sm font-medium">{{ $brand->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $brand->slug }}</td>
                    <td class="px-4 py-3 text-sm">
                        <button wire:click="toggleStatus({{ $brand->id }})" class="px-2 py-1 rounded-lg text-xs font-medium {{ $brand->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $brand->status ? 'فعال' : 'غیرفعال' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $brand->order }}</td>
                    <td class="px-4 py-3 text-sm">
                        <button wire:click="edit({{ $brand->id }})" class="text-yellow-600 hover:text-yellow-800 ml-2">✏️</button>
                        <button wire:click="delete({{ $brand->id }})" class="text-red-600 hover:text-red-800" onclick="return confirm('حذف شود؟')">🗑️</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if(session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>