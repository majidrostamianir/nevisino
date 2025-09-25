<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{

    public static function search($q, $limit = 3)
    {
        $q = Product::normalize($q);
        $keywords = explode(' ', $q);

        $query = self::select('id', 'title' , 'dashed_title');
        foreach ($keywords as $word) {
            $query->where('title', 'LIKE', "%{$word}%");
        }

        return $query->limit($limit)->get();
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
