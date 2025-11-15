<?php

namespace App\Livewire\Home;


use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $rawProducts = \App\Models\Product::query()
            ->whereNotNull('discounted_price')
            ->inRandomOrder()
            ->limit(10)
            ->get(['id', 'title', 'dashed_title', 'price','discounted_price']);

        $productsForJs = $rawProducts->map(function ($p) {
            return [
                'image' => asset('storage/products/' . $p->id . '/small/1.webp'),
                'name' => $p->title ?? 'بدون نام',
                'discounted_price' => (int) $p->discounted_price,
                'price' => (int) $p->price,
                'link' => route('product-page', ['title' => $p->dashed_title ?? 'unknown'])
            ];
        })->values()->toArray();

        $topProducts = \App\Models\Product::select('products.*')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // اضافه شد
            ->where('orders.status', 'paid') // فقط سفارش‌های پرداخت‌شده
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->groupBy('products.id')
            ->where(function ($q) {
                // محصولات بدون واریانت → فقط اگر stock > 0 باشند
                $q->where(function ($noVar) {
                    $noVar->where(function ($c) {
                        $c->whereNull('variant')->orWhere('variant', '');
                    })
                        ->where('stock', '>', 0);
                })

                    // محصولات دارای واریانت → حداقل یک واریانت موجود
                    ->orWhere(function ($hasVar) {
                        $hasVar->whereNotNull('variant')
                            ->whereHas('variants', function ($v) {
                                $v->where('stock', '>', 0);
                            });
                    });
            })
            ->orderByDesc('total_sold')
            ->take(10) // حالا ۱۰ تا می‌گیریم چون کاروسل ۶ تاییه و ۱۰ تا داریم
            ->get();


        return view('livewire.home.index' , compact('rawProducts', 'productsForJs' , 'topProducts'));
    }
}
