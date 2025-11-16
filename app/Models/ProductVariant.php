<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id' , 'name',  'stock',
//        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // اگر قیمت نداشت، قیمت محصول رو برگردونه
//    public function getFinalPriceAttribute(): int
//    {
//        return $this->price ?? $this->product->price;
//    }

}
