<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $casts = [
        'price' => 'integer',
        'price_previous' => 'integer',
        'bulk_price' => 'integer',
        'bulk_price_previous' => 'integer',
        'installment_price' => 'integer',
        'installment_price_previous' => 'integer',
        'discounted_price' => 'integer',
        'discounted_price_previous' => 'integer',
        'discounted_installment_price' => 'integer',
        'discounted_installment_price_previous' => 'integer',
        'price_updated_at' => 'datetime',
        'bulk_price_updated_at' => 'datetime',
        'installment_price_updated_at' => 'datetime',
        'discounted_price_updated_at' => 'datetime',
        'discounted_installment_price_updated_at' => 'datetime',
        'stock' => 'integer',
        'sold_quantity' => 'integer',
        'weight' => 'integer',
    ];

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

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_product')
            ->withPivot('attribute_id')
            ->withTimestamps();
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
    public function getFinalPriceAttribute()
    {
        return \App\Helpers\PriceHelper::getProductPrice($this);
    }
}
