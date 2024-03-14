<?php

namespace App\Listeners;

use App\Events\SyncToPawoon;
use App\Models\Category;
use App\Models\Product;
use App\Services\Pawoon\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncronizingPawoon implements ShouldQueue
{
    use InteractsWithQueue;

    private Service $service;

    /**
     * Handle the event.
     */
    public function handle(SyncToPawoon $event): void
    {
        $this->service = Service::make($event->merchant->integration);

        $categories = [];
        $products = [];
        $images = [];

        foreach ($this->service->categories() as $category) {
            if (Category::query()->where('external_id', $category['external_id'])->count()) {
                continue;
            }

            $categories[] = $category;
        }

        foreach ($this->service->products($event->merchant->external_id) as $product) {
            if (Product::query()->where('external_id', $product['external_id'])->count()) {
                continue;
            }

            if ($product['image']) {
                $images[] = $product;
            }

            $products[] = $product;
        }

        $event->merchant->categories()->createMany($categories);
        $event->merchant->products()->createMany($products)->each(function (Product $product) use ($images) {
            $imageURL = collect($images)->where('external_id', $product->external_id)->value('image');

            if (! $imageURL) {
                return;
            }

            $product->addMediaFromUrl($imageURL)->toMediaCollection('banner');
        });
    }
}
