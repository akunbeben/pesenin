<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrowseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'scan' => $this->resource['scan'] ?? null,
            'table' => $this->resource['table'] ?? null,
            'summary' => $this->resource['summary'] ?? null,
            'meta' => [
                'message' => $this->resource['meta']['message'] ?? null,
                'code' => $this->resource['meta']['code'] ?? null,
            ],
        ];
    }
}
