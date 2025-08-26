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
