<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? '',
            'product_price' => $this->product->price ?? '',
            'thumbnail' => $this->product->thumbnail ?? '',
            'quantity' => $this->quantity,
            'color' => $this->color,
            'size' => $this->size,
            'unit' => $this->unit,
            'price' => $this->price,
        ];
    }
}
