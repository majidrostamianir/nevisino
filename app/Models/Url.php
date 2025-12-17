<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Url extends Model
{
    use Searchable;

    protected $fillable = ['indexing', 'description', 'following'];


    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'type' => 'url',
            'title' => self::normalize($this->title_tag),
        ];
    }

    public static function normalize($query)
    {
        if (!$query) return '';

        $query = trim($query);

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $query = str_replace($persian, $english, $query);

        $query = str_replace(['آ', 'ي', 'ك'], ['ا', 'ی', 'ک'], $query);

        return $query;
    }

}
