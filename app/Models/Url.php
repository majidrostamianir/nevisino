<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{

    protected $fillable = ['indexing' , 'description' , 'following'];
    public static function search($q, $limit = 3)
    {
        $q = Product::normalize($q);
        $keywords = explode(' ', $q);

        // بررسی وجود کلمه کوتاه یا عدد
        $hasShort = collect($keywords)->contains(fn($w) => is_numeric($w) || mb_strlen($w) < 4);

        $results = collect();

        // اول: fulltext
        $fulltextResults = self::select('id', 'title', 'dashed_title')
            ->selectRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance", [$q])
            ->whereRaw("MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE)", [$q])
            ->orderByDesc('relevance')
            ->limit($limit)
            ->get();

        $results = $results->merge($fulltextResults);

        // اگر کلمه کوتاه/عدد بود → LIKE هم بزن
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

        return $results->unique('id')->sortByDesc('relevance')->take($limit)->values();
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
