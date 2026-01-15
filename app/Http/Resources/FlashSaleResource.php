<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request, $running = false, $taken = false): array
    {
        if ($running) {
            $products = $this->products()->isActive()->when($taken, function ($query) {
                return $query->take(9);
            })->get();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'thumbnail' => $this->thumbnail,
            'start_date' => $this->start_date.' '.$this->start_time,
            'end_date' => $this->end_date.' '.$this->end_time,
            'products' => $running ? ProductResource::collection($products)->map(fn ($product) => $product->toArray($request, true))->toArray() : null,
        ];
    }
}
