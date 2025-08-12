<div>
    <div class="overflow-hidden rounded-lg shadow">
        <table class="min-w-full text-right bg-pars-100">
            <thead>
            <tr class="shadow">
                <th class="px-4 py-2">شناسه</th>
                <th class="px-4 py-2">عنوان</th>
                <th class="px-4 py-2">تصویر</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $index => $product)
                <tr class=" border-b ">
                    <td class="px-4 py-2">{{ $product['id'] }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.product.save' , ['product'=>$product]) }}">{{ $product['title'] }}</a>
                    </td>
                    <td class="px-4 py-2">
                        @if(Storage::disk('public')->exists('products/' . $product->id . '/small/1.webp'))
                            <img width="150" class="rounded-xl"
                                 src="{{ asset('storage/products/' . $product->id . '/small/1.webp') }}" alt="">
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="w-full place-items-center mt-4 mb-2">
            {{ $products->links() }}
        </div>
    </div>
</div>
