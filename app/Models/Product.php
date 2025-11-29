<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function urls()
    {
        return $this->belongsToMany(Url::class)->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public static function search($q, $limit = 10)
    {
        $keywords = explode(' ', $q);

        // --- Search in products.title_tag ---
        $productQuery = Product::select('id', 'title_h1', 'dashed_url')
            ->selectRaw("(
            " . implode(' + ', array_map(fn($w) => "IF(title_tag LIKE '%{$w}%',1,0)", $keywords)) . "
        ) as relevance");

        foreach ($keywords as $word) {
            $productQuery->where('title_tag', 'LIKE', "%{$word}%");
        }

        $productResults = $productQuery
            ->orderByDesc('relevance')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'type' => 'product',
                'id' => $r->id,
                'title' => $r->title_tag,
                'dashed_url' => $r->dashed_url,
                'relevance' => $r->relevance
            ]);

        // --- Search in categories.title ---
        $categoryQuery = Category::select('id', 'title_h1')
            ->selectRaw("(
            " . implode(' + ', array_map(fn($w) => "IF(title_tag LIKE '%{$w}%',1,0)", $keywords)) . "
        ) as relevance");

        foreach ($keywords as $word) {
            $categoryQuery->where('title_tag', 'LIKE', "%{$word}%");
        }

        $categoryResults = $categoryQuery
            ->orderByDesc('relevance')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'type' => 'category',
                'id' => $r->id,
                'title' => $r->title_tag,
                'dashed_url' => null,
                'relevance' => $r->relevance
            ]);

        // --- Merge + sort + limit ---
        return collect()
            ->merge($productResults)
            ->merge($categoryResults)
            ->sortByDesc('relevance')
            ->take($limit)
            ->values();
    }

    public static function normalize($query)
    {
        $query = trim($query);

        // تبدیل اعداد فارسی به انگلیسی
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $query = str_replace($persian, $english, $query);

        // حذف فاصله‌های اضافه
        return preg_replace('/\s+/', ' ', $query);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
// قیمت نهایی → اگه ورینت نداره همون قیمت محصول، اگر داره مینیمم قیمت ورینت‌ها
//    public function getFinalPriceAttribute(): int
//    {
//        if ($this->variants()->count() > 0) {
//            return $this->variants()->min('price') ?? $this->price;
//        }
//        return $this->price;
//    }
//
//    // موجودی نهایی → اگر ورینت نداره همون موجودی محصول، اگر داره مجموع موجودی ورینت‌ها
//    public function getFinalStockAttribute(): int
//    {
//        if ($this->variants()->count() > 0) {
//            return $this->variants()->sum('stock');
//        }
//        return $this->stock;
//    }
}
