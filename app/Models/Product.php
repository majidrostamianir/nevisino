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

        // fulltext
        $results = self::select('id', 'title' ,'dashed_title')
            ->selectRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance", [$q])
            ->whereRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE)", [$q])
            ->orderByDesc('relevance')
            ->limit($limit)
            ->get();

        // fallback
        if ($results->isEmpty()) {
            $keywords = explode(' ', $q);

            $query = self::select('id', 'title' , 'dashed_title');
            foreach ($keywords as $word) {
                $query->where('title', 'LIKE', "%{$word}%");
            }

            $query->selectRaw("
                (
                    " . implode(' + ', array_map(fn($w) => "IF(title LIKE '%{$w}%',1,0)", $keywords)) . "
                ) as relevance
            ");

            $results = $query->orderByDesc('relevance')->limit($limit)->get();
        }

        return $results;
    }

    public static function normalize($query)
    {
        $query = trim($query);
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $query = str_replace($persian, $english, $query);
        return preg_replace('/\s+/', ' ', $query);
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
