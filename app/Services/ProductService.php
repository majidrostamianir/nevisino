<?php

namespace App\Services;

use App\Jobs\SyncProductToBasalam;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductService
{
    // ─────────────────────────────────────────────
    //  محصول (Product)
    // ─────────────────────────────────────────────

    /**
     * ساخت محصول جدید
     */
    public function create(array $data): Product
    {
        $product = Product::create($data);

        $this->maybeSync($product, updateOnly: false);

        return $product;
    }

    /**
     * آپدیت فیلدهای محصول
     */
    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        $this->maybeSync($product, updateOnly: true);

        return $product;
    }

    /**
     * تنظیم موجودی محصول به مقدار مشخص
     */
    public function setProductStock(Product $product, int $newStock): Product
    {
        $product->stock = max(0, $newStock);
        $product->save();

        $this->maybeSync($product, updateOnly: true);

        return $product;
    }

    /**
     * کم کردن موجودی محصول بعد از فروش (محصول بدون واریانت)
     */
    public function decrementProductStock(Product $product, int $quantity = 1): Product
    {
        $product->stock = max(0, ($product->stock ?? 0) - $quantity);
        $product->save();

        $this->maybeSync($product, updateOnly: true);

        return $product;
    }

    /**
     * تغییر قیمت محصول
     */
    public function setPrice(Product $product, int $newPrice): Product
    {
        $product->price = $newPrice;
        $product->price_updated_at = now();
        $product->save();

        $this->maybeSync($product, updateOnly: true);

        return $product;
    }

    /**
     * سینک دستی به باسلام (بدون تغییر چیزی در محصول)
     */
    public function syncToBasalam(Product $product): void
    {
        SyncProductToBasalam::dispatch($product);
    }

    // ─────────────────────────────────────────────
    //  واریانت (ProductVariant)
    // ─────────────────────────────────────────────

    /**
     * تنظیم موجودی واریانت
     */
    public function setVariantStock(ProductVariant $variant, int $newStock): ProductVariant
    {
        $variant->stock = max(0, $newStock);
        $variant->save();

        // سینک از طریق محصول والد انجام می‌شود
        $this->maybeSync($variant->product, updateOnly: true);

        return $variant;
    }

    /**
     * کم کردن موجودی واریانت بعد از فروش
     */
    public function decrementVariantStock(ProductVariant $variant, int $quantity = 1): ProductVariant
    {
        $variant->stock = max(0, ($variant->stock ?? 0) - $quantity);
        $variant->save();

        // سینک از طریق محصول والد
        $this->maybeSync($variant->product, updateOnly: true);

        return $variant;
    }

    // ─────────────────────────────────────────────
    //  سفارش (Order)
    // ─────────────────────────────────────────────

    /**
     * کم کردن موجودی همه آیتم‌های یک سفارش
     * هم محصولات بدون واریانت، هم واریانت‌دارها را مدیریت می‌کند
     */
    public function decrementStockForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->variant_id && $item->variant) {
                $this->decrementVariantStock($item->variant, (int) $item->quantity);
            } elseif ($item->product) {
                $this->decrementProductStock($item->product, (int) $item->quantity);
            }
        }
    }

    // ─────────────────────────────────────────────
    //  Internals
    // ─────────────────────────────────────────────

    /**
     * اگر سینک خودکار روشن است، Job را در صف قرار بده.
     * اگر داخل تراکنش دیتابیس باشیم، بعد از commit ارسال می‌شود.
     */
    protected function maybeSync(Product $product, bool $updateOnly): void
    {
        if (!config('basalam.auto_sync', true)) {
            return;
        }

        if (DB::transactionLevel() > 0) {
            DB::afterCommit(function () use ($product, $updateOnly) {
                SyncProductToBasalam::dispatch($product, $updateOnly, skipPhotos: true);
            });
        } else {
            SyncProductToBasalam::dispatch($product, $updateOnly, skipPhotos: true);
        }
    }
}