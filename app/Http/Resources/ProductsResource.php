<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'price_rupiah' => $this->price_rupiah,
            'availability' => $this->availability,
            'recommended' => $this->recommended,
            'variants' => $this->variants,
            'banner' => $this->banner,
            'thumbnail' => $this->thumbnail,
        ];
    }
}
