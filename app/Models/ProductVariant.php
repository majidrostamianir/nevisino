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
    // در مدل Product
    public function getVariantName($variantId): string
    {
        $variant = ProductVariant::find($variantId);
        return $variant ? $variant->name : '';
    }


}
