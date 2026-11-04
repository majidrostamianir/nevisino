<?php

namespace App\Jobs;

use App\Models\BasalamProduct;
use App\Services\Basalam\SyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateBasalamSku implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(public int $listingId) {}

    public function handle(SyncService $sync): void
    {
        $listing = BasalamProduct::find($this->listingId);

        if (!$listing || !$listing->basalam_product_id) {
            return;
        }

        $newSku = $listing->product_variant_id
            ? 'bslm_' . $listing->product_id . '_' . $listing->product_variant_id
            : 'bslm_' . $listing->product_id;

        $sync->updateProduct($listing->basalam_product_id, ['sku' => $newSku]);
        $listing->update(['basalam_sku' => $newSku]);
    }
}