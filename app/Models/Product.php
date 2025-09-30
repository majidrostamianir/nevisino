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
        $q = self::normalize($q);
        $keywords = explode(' ', $q);

        // بررسی وجود کلمات کوتاه یا عدد
        $hasShort = collect($keywords)->contains(fn($w) => is_numeric($w) || mb_strlen($w) < 4);

        $results = collect();

        // حالت fulltext
        $fulltextResults = self::select('id', 'title', 'dashed_title')
            ->selectRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance", [$q])
            ->whereRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE)", [$q])
            ->orderByDesc('relevance')
            ->limit($limit)
            ->get();

        $results = $results->merge($fulltextResults);

        // اگر کلمه کوتاه/عدد وجود داشت → LIKE هم بزن
        if ($hasShort) {
            $query = self::select('id', 'title', 'dashed_title');

            foreach ($keywords as $word) {
                $query->where('title', 'LIKE', "%{$word}%");
            }

            $query->selectRaw("
                (
                    " . implode(' + ', array_map(fn($w) => "IF(title LIKE '%{$w}%',1,0)", $keywords)) . "
                ) as relevance
            ");

            $likeResults = $query->orderByDesc('relevance')->limit($limit)->get();

            $results = $results->merge($likeResults);
        }

        // حذف رکوردهای تکراری و محدود کردن به n نتیجه
        return $results->unique('id')->sortByDesc('relevance')->take($limit)->values();
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
