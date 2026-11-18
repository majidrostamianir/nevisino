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
use Throwable;

class UpdateBasalamSku implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 100;

    public array $backoff = [300, 600, 900, 1800, 3600];

    public function __construct(public int $listingId) {}

    public function retryUntil(): \DateTime
    {
        return now()->addHours(12);
    }

    public function handle(SyncService $sync): void
    {
        $remaining = $sync->cooldownRemaining();
        if ($remaining > 0) {
            $this->release($remaining);
            return;
        }

        $listing = BasalamProduct::find($this->listingId);

        if (!$listing || !$listing->basalam_product_id) {
            return;
        }

        $newSku = $listing->product_variant_id
            ? 'bslm_' . $listing->product_id . '_' . $listing->product_variant_id
            : 'bslm_' . $listing->product_id;

        try {
            $sync->updateProduct($listing->basalam_product_id, ['sku' => $newSku]);
            $listing->update(['basalam_sku' => $newSku]);
        } catch (Throwable $e) {
            if ((int) $e->getCode() === 429) {
                $releaseIn = max($sync->cooldownRemaining(), 60);

                Log::info('Basalam rate limit, releasing job (sku)', [
                    'listing_id' => $this->listingId,
                    'release_in' => $releaseIn,
                ]);

                $this->release($releaseIn);
                return;
            }

            throw $e;
        }
    }
}