<?php

namespace App\Livewire\Home;


use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public $ariaArtist , $cClass ,$daftarMashq,$medad,$edari ;
    public function mount()
    {


        $cClassIds = [122 , 123 , 124 , 125];

        $this->cClass = Product::with(['variants' => function($query) {
            $query->where('stock', '>', 0);
        }])
            ->whereIn('id', $cClassIds)
            ->orderByRaw("FIELD(id, " . implode(',', $cClassIds) . ")")
            ->get();


        $ariaArtistIds = [75, 2, 1, 3, 5];

        $this->ariaArtist = Product::with(['variants' => function($query) {
            $query->where('stock', '>', 0);
        }])
            ->whereIn('id', $ariaArtistIds)
            ->orderByRaw("FIELD(id, " . implode(',', $ariaArtistIds) . ")")
            ->get();

        $daftarMashqIds = [60 , 59 , 57 , 64 , 65 , 89 , 131 ,94 , 93];

        $this->daftarMashq  = Product::with(['variants' => function($query) {
            $query->where('stock', '>', 0);
        }])
            ->whereIn('id', $daftarMashqIds)
            ->orderByRaw("FIELD(id, " . implode(',', $daftarMashqIds) . ")")
            ->get();

        $medadIds = [60 , 59 , 57 , 64 , 65 , 89 , 131 ,94 , 93];

        $this->medad  = Product::with(['variants' => function($query) {
            $query->where('stock', '>', 0);
        }])
            ->whereIn('id', $medadIds)
            ->orderByRaw("FIELD(id, " . implode(',', $medadIds) . ")")
            ->get();

        $edariIds = [126,46,114,121,120,44,35,43,34,29,30,28,55,56];

        $this->edari  = Product::with(['variants' => function($query) {
            $query->where('stock', '>', 0);
        }])
            ->whereIn('id', $edariIds)
            ->orderByRaw("FIELD(id, " . implode(',', $edariIds) . ")")
            ->get();
    }
    public function render()
    {
        $availableIds = \App\Models\Product::whereNotNull('discounted_price')
            ->get()
            ->filter(fn($p) => $p->hasValidStock())
            ->pluck('id')
            ->toArray();

        if (empty($availableIds)) {
            $rawProducts = collect();
        } else {
            $rawProducts = \App\Models\Product::whereIn('id', $availableIds)
                ->inRandomOrder()
                ->limit(15)
                ->get(['id', 'title', 'dashed_url', 'price', 'discounted_price']);
        }
        $productsForJs = $rawProducts->map(function ($p) {
            return [
                'image' => asset('storage/products/' . $p->id . '/small/1.webp'),
                'name' => $p->title ?? 'بدون نام',
                'discounted_price' => (int)$p->discounted_price,
                'price' => (int)$p->price,
                'link' => route('product-page', ['title' => $p->dashed_url ?? 'unknown' , 'npi' => $p->id])
            ];
        })->values()->toArray();

        $topProducts = \App\Models\Product::join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->where(function ($q) {
                // محصولات بدون واریانت → فقط اگر stock > 0 باشند
                $q->where(function ($noVar) {
                    $noVar->where(function ($c) {
                        $c->whereNull('variant')->orWhere('variant', '');
                    })->where('stock', '>', 0);
                })
                    // محصولات دارای واریانت → حداقل یک واریانت موجود
                    ->orWhere(function ($hasVar) {
                        $hasVar->whereNotNull('variant')
                            ->whereHas('variants', function ($v) {
                                $v->where('stock', '>', 0);
                            });
                    });
            })
            ->select('products.id', 'products.title', 'products.dashed_url', 'products.price', 'products.discounted_price')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->groupBy('products.id', 'products.title', 'products.dashed_url', 'products.price', 'products.discounted_price')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();


        $paintProducts = Product::query()
            ->whereIn('category_id', [10, 11, 12])
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
            ->orderByDesc('stock')
            ->get();

        $officeProducts = Product::query()
            ->whereIn('category_id', [15,16,17,18])
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
            ->orderByDesc('stock')
            ->get();

        return view('livewire.home.index', compact('rawProducts', 'productsForJs', 'topProducts', 'paintProducts','officeProducts'));
    }
}
