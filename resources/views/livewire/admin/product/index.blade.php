<div>
    <div class="overflow-hidden rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="shadow">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">
                    <input wire:model.live="query" class="rounded-2xl bg-white h-8 px-2" placeholder="جستجوی عنوان ...">
                </th>
                <th class="px-4 py-2">قیمت</th>
                <th class="px-4 py-2">تصویر</th>
                <th class="px-4 py-2">لینک در سایت</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $index => $product)
                <tr class=" border-b ">
                    <td class="px-4 py-2">{{ $product['id'] }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.product.save' , ['product'=>$product]) }}">{{ $product['title'] }}</a>
                    </td>
                    <td class="px-4 py-2">{{ english_to_persian_num(number_format($product['discounted_price'] ?? $product['price'])) }}</td>
                    <td class="px-4 py-2">
                        @if(Storage::disk('public')->exists('products/' . $product->id . '/small/1.webp'))
                            <img width="150" class="rounded-xl"
                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}" alt="">
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('product-page' , ['title' => $product->dashed_url, 'npi'=>$product->id]) }}" target="_blank" >
                            برو
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
       </div>
</div>
