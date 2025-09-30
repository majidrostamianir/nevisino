<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class EmallsProductsController extends Controller
{
    public function list(Request $request)
    {
        $page    = (int) $request->input('page', 1);
        $perPage = (int) $request->input('item_per_page', 50000);

        $query = Product::query()->with('category'); // فرض می‌کنیم relation category داری

        $totalItems = $query->count();
        $products   = $query->forPage($page, $perPage)->get();

        $items = $products->map(function ($product) {
            return [
                "id"           => (string)( $product->id),
                "title"        => $product->title,
                "price"        => (int) $product->price,
                "old_price"    => null,
                "category"     => $product->category->title ?? "نامشخص",
                "image" => asset('storage/products/' . $product->id . '/large/1.webp'),
                "color"        =>  null,
                "guarantee"    => null,
                "is_available" => $product->stock > 0,
                "url"          => url("/product/{$product->dashed_title}"),
            ];
        });

        return response()->json([
            "success"       => true,
            "total_items"   => $totalItems,
            "pages_count"   => 1,
            "item_per_page" => $perPage,
            "page_num"        => $page,
            "products"      => $items,

        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
