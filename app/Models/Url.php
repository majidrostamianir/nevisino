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

        // کوئری پایه روی title_tag
        $query = self::select('id', 'title_tag as title', 'dashed_url');

        // شرط‌های LIKE برای هر کلمه
        foreach ($keywords as $word) {
            $query->where('title_tag', 'LIKE', "%{$word}%");
        }

        // محاسبه relevance بر اساس تعداد match
        $query->selectRaw("
        (
            " . implode(' + ', array_map(fn($w) => "IF(title_tag LIKE '%{$w}%',1,0)", $keywords)) . "
        ) as relevance
    ");

        // اگر کلمه کوتاه/عدد نبود، همین یک سرچ کافیه
        if (!$hasShort) {
            return $query->orderByDesc('relevance')->limit($limit)->get()->values();
        }

        // اگر کلمه کوتاه بود هم همین کوئری جواب میده
        return $query->orderByDesc('relevance')->limit($limit)->get()->values();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
