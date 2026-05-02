<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    public function urls()
    {
        return $this->belongsToMany(Url::class)->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function attrs()
    {
        return $this->belongsToMany(Attr::class)->withTimestamps();
    }
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'type' => 'product',
            'title' => self::normalize($this->title),
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

    public function getStoryImageAttribute()
    {
        return asset("storage/products/{$this->id}/small/1.webp");
    }
    public function hasValidStock(): bool
    {
        // اگر واریانت دارد
        if ($this->variants()->exists()) {
            return $this->variants()->where('stock', '>', 0)->exists();
        }

        // اگر واریانت ندارد
        return $this->stock > 0;
    }

}
