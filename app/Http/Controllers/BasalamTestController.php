<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Support\Facades\Http;

class BasalamTestController extends Controller
{
    public function send()
    {
        $token = env('BASALAM_ACCESS_TOKEN');
        $vendorId = 1318497;

        for ($x = 61; $x <= 86; $x++) {
            $product = Product::query()->find($x);
            $productImagesPath = public_path('storage/products/' . $product->id . '/large/');

            // همه فایل‌های webp که عددشان کمتر از 1000 است
            $files = glob($productImagesPath . '*.webp');

            // فقط فایل‌هایی که نامشان عددی است و کمتر از 1000
            $files = array_filter($files, function ($file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                return is_numeric($filename) && (int)$filename < 1000;
            });

            // مرتب‌سازی عددی
            usort($files, function ($a, $b) {
                return (int)pathinfo($a, PATHINFO_FILENAME) - (int)pathinfo($b, PATHINFO_FILENAME);
            });

            $photoIds = [];
            foreach ($files as $filePath) {
                $filename = pathinfo($filePath, PATHINFO_BASENAME);

                $uploadResponse = Http::withToken($token)
                    ->attach('file', file_get_contents($filePath), $filename)
                    ->post('https://uploadio.basalam.com/v3/files', [
                        'file_type' => 'product.photo'
                    ])
                    ->json();

                if (!isset($uploadResponse['id'])) {
                    return response()->json([
                        'error' => 'Image upload failed',
                        'file' => $filename,
                        'response' => $uploadResponse
                    ]);
                }

                $photoIds[] = $uploadResponse['id'];
            }

            if (ceil(($product->price * 1.07) / 1000) * 1000 < 1000)
                $price = 10000;
            else
                $price =ceil(($product->price * 1.07) / 1000) * 1000;
            // فقط اگر حداقل یک عکس آپلود شد
            if (!empty($photoIds)) {
                // اگر موجودی نال بود، مجموع واریانت‌ها را محاسبه کن
                $stock = $product->stock;
                if (is_null($stock)) {
                    // فرض می‌کنیم واریانت‌ها با همان id محصول مرتبط هستند و ستون 'variant' پر شده
                    $stock = \DB::table('products')
                        ->where('title', $product->title)
                        ->whereNotNull('variant')
                        ->sum('stock');
                    // اگر باز هم null شد، صفر قرار بده
                    $stock = $stock ?? 0;
                }
               if ($product->weight == 0 )
                   $weight = 150;
               else
                   $weight = $product->weight;
                $productData = [
                    "name" => $product->title,
                    "brief" => '',
                    "description" => '',
                    "status" => 2976,
                    "preparation_days" => 1,
                    "weight" => $weight ,
                    "package_weight" => $weight + 200,
                    "primary_price" => $price,
                    "stock" => $stock,
                    "sku" => $product->code,
                    "packaging_dimensions" => [
                        "height" => 30,
                        "length" => 30,
                        "width" => 30
                    ],
                    "is_wholesale" => false,
                    "photo" => $photoIds[0],
                    "photos" => $photoIds,
                    "category_id" => 569
                ];

                $response = Http::withToken($token)
                    ->post("https://core.basalam.com/v4/vendors/{$vendorId}/products", $productData)
                    ->json();
                var_dump($response);
            }

        }
    }


}
