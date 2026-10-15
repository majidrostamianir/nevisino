<div>
    <button wire:click="refreshCategories"
            wire:loading.attr="disabled"
            class="cursor-pointer bg-pars-600 hover:bg-pars-700 text-white px-4 py-2 rounded-lg text-sm mb-4">
        <span wire:loading.remove wire:target="refreshCategories">🔄 بروزرسانی لیست دسته‌ها</span>
        <span wire:loading wire:target="refreshCategories">در حال دریافت...</span>
    </button>

    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold whitespace-nowrap" colspan="4">
                    تعداد کل دسته‌های باسلام: {{ english_to_persian_num(count($basalamCategories)) }}
                </th>
            </tr>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold w-1/4">دسته سایت</th>
                <th class="px-4 py-3 text-sm font-semibold">دسته باسلام</th>
                <th class="px-4 py-3 text-sm font-semibold w-40">واحد فروش</th>
                <th class="px-4 py-3 text-sm font-semibold w-24">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($categories as $category)
                @include('livewire.admin.basalam.partials.category-row', [
                    'category' => $category,
                    'level' => 0,
                ])
            @endforeach
            </tbody>
        </table>
    </div>

    @if ($categories->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-xl mt-4">
            <p class="text-gray-500">هیچ دسته‌ای یافت نشد</p>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-emerald-100 text-emerald-800 p-3 rounded-lg mt-4 text-sm">✅ {{ session('success') }}</div>
    @endif
    @if (session()->has('warning'))
        <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg mt-4 text-sm">⚠️ {{ session('warning') }}</div>
    @endif

    <div class="mt-6 sticky bottom-4 flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
                class="cursor-pointer bg-gradient-to-r from-pars-500 to-pars-800 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all disabled:opacity-50">
            <span wire:loading.remove wire:target="save">💾 ذخیره همه </span>
            <span wire:loading wire:target="save">در حال ذخیره...</span>
        </button>
    </div>
</div>