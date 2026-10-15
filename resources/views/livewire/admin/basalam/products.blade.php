<div class="p-6" dir="rtl">
    <h1 class="text-2xl font-bold mb-4">مدیریت سینک باسلام</h1>

    @if (session()->has('success'))
        <div class="bg-emerald-100 text-emerald-800 p-3 rounded-lg mb-4 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg mb-4 text-sm">
            ⚠️ {{ session('warning') }}
        </div>
    @endif

    {{-- آمار --}}
    <div class="grid grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow p-3 text-center">
            <div class="text-gray-500 text-xs">کل محصولات</div>
            <div class="text-xl font-bold">{{ english_to_persian_num($stats['total']) }}</div>
        </div>
        <div class="bg-emerald-50 rounded-lg shadow p-3 text-center">
            <div class="text-emerald-700 text-xs">سینک‌شده</div>
            <div class="text-xl font-bold text-emerald-700">{{ english_to_persian_num($stats['synced']) }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-3 text-center">
            <div class="text-yellow-700 text-xs">در انتظار</div>
            <div class="text-xl font-bold text-yellow-700">{{ english_to_persian_num($stats['pending']) }}</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-3 text-center">
            <div class="text-red-700 text-xs">ناموفق</div>
            <div class="text-xl font-bold text-red-700">{{ english_to_persian_num($stats['failed']) }}</div>
        </div>
    </div>

    {{-- فیلترها و دکمه‌های عملیات --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <select wire:model.live="statusFilter" class="border rounded-lg px-3 py-2 text-sm">
            <option value="all">همه</option>
            <option value="synced">سینک‌شده</option>
            <option value="pending">در انتظار</option>
            <option value="failed">ناموفق</option>
            <option value="none">بدون سینک</option>
        </select>



        <label class="flex items-center gap-2 text-sm bg-white border rounded-lg px-3 py-2">
            <input type="checkbox" wire:model="skipPhotos" class="rounded"/>
            بدون آپلود عکس
        </label>

        <div class="flex-1"></div>
        <div class="flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
            <span class="text-sm text-gray-600">📈 درصد سود:</span>
            <input type="number"
                   wire:model="profitPercent"
                   wire:blur="saveProfitPercent"
                   step="0.5"
                   min="0"
                   max="200"
                   class="w-20 rounded-lg border-gray-300 text-sm py-1 px-2 text-center">
            <span class="text-sm text-gray-600">%</span>
        </div>

        <button wire:click="syncSelected" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            سینک انتخاب‌شده‌ها ({{ english_to_persian_num(count($selected)) }})
        </button>

        <button wire:click="updateSkus" wire:confirm="SKU انتخاب‌شده‌ها آپدیت بشه؟"
                class="cursor-pointer bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            🏷️ آپدیت SKU
        </button>

        <button wire:click="syncFailed" class="cursor-pointer bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
            سینک ناموفق‌ها
        </button>

        <button wire:click="clearFailed" wire:confirm="همه محصولات ناموفق به حالت 'در انتظار' برگردن؟"
                class="cursor-pointer bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
            ریست ناموفق‌ها
        </button>
        <button
                wire:click="toggleForceStock"
                class="{{ $this->forceStock ? 'cursor-pointer bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white px-4 py-2 rounded-lg text-sm"
        >
            {{ $this->forceStock ? '🔴 موجودی = ۱ (روشن)' : '⚪ موجودی واقعی' }}
        </button>
    </div>

    {{-- جدول --}}
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" wire:change="toggleSelectAll(@json($products->pluck('id')->toArray()))"/>
                </th>
                <th class="px-4 py-3">
                    <input wire:model.live.debounce.500ms="search"
                           class="w-fit rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm"
                           placeholder="🔍 جستجوی محصول...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold">دسته</th>
                <th class="px-4 py-3 text-sm font-semibold">وضعیت</th>
                <th class="px-4 py-3 text-sm font-semibold">باسلام ID</th>
                <th class="px-4 py-3 text-sm font-semibold">SKU</th>
                <th class="px-4 py-3 text-sm font-semibold">آخرین سینک</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                @php
                    $hasVariants = $product->variants()->exists();
                    $mainListing  = $product->basalamProducts->whereNull('product_variant_id')->first();
                    $mainStatus   = $mainListing?->status;
                    $mainBasalamId = $mainListing?->basalam_product_id;
                    $mainSku      = $mainListing?->basalam_sku;
                    $mainLastSync = $mainListing?->synced_at;
                @endphp

                {{-- ردیف اصلی: محصول --}}
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ in_array($product->id, $selected) ? 'bg-blue-50' : ($loop->even ? 'bg-gray-50/50' : 'bg-white') }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" wire:model.live="selected" value="{{ $product->id }}"/>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-800">{{ $product->title }}</div>
                        <div class="text-xs text-gray-400">
                            id: {{ english_to_persian_num($product->id) }}
                            @if ($hasVariants)
                                • {{ english_to_persian_num($product->variants->count()) }} واریانت
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $product->category?->title ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if ($hasVariants)
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700">
                                🎨 واریانت‌دار
                            </span>
                        @elseif (!$mainListing)
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">
                                — سینک نشده
                            </span>
                        @elseif ($mainStatus === 'failed')
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                ❌ ناموفق
                            </span>
                        @elseif ($mainStatus === 'pending')
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700">
                                ⏳ در انتظار
                            </span>
                        @else
                            <span class="inline-flex text-nowrap items-center px-2 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700">
                                ✅ سینک‌شده
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600" dir="ltr">
                        {{ $hasVariants ? '—' : ($mainBasalamId ?? '—') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600" dir="ltr">
                        {{ $hasVariants ? '—' : ($mainSku ?? '—') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $hasVariants ? '—' : ($mainLastSync ? english_to_persian_num((string) verta($mainLastSync)) : '—') }}
                    </td>
                </tr>

                {{-- ردیف‌های واریانت --}}
                @if ($hasVariants)
                    @foreach ($product->variants as $variant)
                        @php
                            $vListing = $product->basalamProducts->where('product_variant_id', $variant->id)->first();
                            $vStatus  = $vListing?->status;
                            $vSku     = $vListing?->basalam_sku;
                        @endphp
                        <tr class="border-b border-gray-100 bg-gray-50/60 transition-colors hover:bg-gray-100/60 text-xs">
                            <td class="px-4 py-2"></td>
                            <td class="px-4 py-2">
                                <span class="text-gray-400">↳</span>
                                <span class="text-gray-700">{{ $variant->name }}</span>
                                <span class="text-gray-400">(stock: {{ english_to_persian_num($variant->stock ?? 0) }})</span>
                            </td>
                            <td class="px-4 py-2 text-gray-400">—</td>
                            <td class="px-4 py-2">
                                @if (!$vListing)
                                    <span class="inline-flex text-nowrap items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500">سینک نشده</span>
                                @elseif ($vStatus === 'failed')
                                    <span class="inline-flex text-nowrap items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">❌ ناموفق</span>
                                @elseif ($vStatus === 'pending')
                                    <span class="inline-flex text-nowrap items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700">⏳</span>
                                @else
                                    <span class="inline-flex text-nowrap items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700">✅</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-600" dir="ltr">
                                {{ $vListing?->basalam_product_id ?? '—' }}
                            </td>
                            <td class="px-4 py-2 text-gray-600" dir="ltr">
                                {{ $vSku ?? '—' }}
                            </td>
                            <td class="px-4 py-2 text-gray-400">
                                {{ $vListing?->synced_at ? english_to_persian_num((string) verta($vListing->synced_at)) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 bg-gray-50">
                        <p class="text-gray-500">محصولی یافت نشد</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>