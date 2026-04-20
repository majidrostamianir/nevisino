<div>
    <div class="overflow-x-scroll rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="bg-gradient-to-r from-pars-500 to-pars-800 text-white shadow-md">
                <th class="px-4 py-3 text-sm font-semibold">شناسه</th>
                <th class="px-4 py-3">
                    <input wire:model.live.debounce.500ms="query"
                           class="rounded-lg border-0 bg-white/20 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 h-9 px-3 text-sm w-full"
                           placeholder="🔍 جستجوی عنوان ...">
                </th>
                <th class="px-4 py-3 text-sm font-semibold">قیمت</th>
                <th class="px-4 py-3 text-sm font-semibold">موجودی</th>
                <th class="px-4 py-3 text-sm font-semibold">تصویر</th>
                <th class="px-4 py-3 text-sm font-semibold">لینک در سایت</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $index => $product)
                @php
                    // محاسبه موجودی محصول
                    if ($product->variant && $product->selectedVariant < 1000) {
                        $stock = $product->variants()->sum('stock');
                    } elseif ($product->variant && $product->selectedVariant >= 1000) {
                        $stock = \App\Models\ProductVariant::find($product->selectedVariant)?->stock ?? 0;
                    } else {
                        $stock = $product->stock;
                    }
                @endphp
                <tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm text-gray-800">{{ english_to_persian_num($product['id']) }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.product.save' , ['product'=>$product]) }}"
                           class="text-pars-600 hover:text-pars-800 font-medium transition-colors">
                            {{ $product['title'] }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if(!empty($product['discounted_price']) && $product['discounted_price'] < $product['price'])
                            <div class="flex flex-col">
                                <span class="text-gray-500 line-through text-xs">
                                    {{ english_to_persian_num(number_format($product['price'])) }} تومان
                                </span>
                                <span class="text-green-600 font-bold text-sm">
                                    {{ english_to_persian_num(number_format($product['discounted_price'])) }} تومان
                                </span>
                            </div>
                        @else
                            <span class="text-gray-800 font-medium">
                                {{ english_to_persian_num(number_format($product['price'])) }} تومان
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($stock > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                                ✓ {{ english_to_persian_num(number_format($stock)) }} عدد
                            </span>
                        @elseif($stock == 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                ✗ ناموجود
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                                ⚠ نامشخص
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if(Storage::disk('public')->exists('products/' . $product->id . '/small/1.webp'))
                            <img width="60" height="60" class="rounded-lg object-cover shadow-sm"
                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}"
                                 alt="{{ $product['title'] }}">
                        @else
                            <span class="text-gray-400 text-xs">بدون تصویر</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('product-page' , ['title' => $product->dashed_url, 'npi'=>$product->id]) }}"
                           target="_blank"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-pars-500 text-white hover:bg-pars-600 transition-colors">
                            🔗 مشاهده در سایت
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($products->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <p class="text-gray-500">هیچ محصولی یافت نشد</p>
            </div>
        @endif
    </div>
</div>
