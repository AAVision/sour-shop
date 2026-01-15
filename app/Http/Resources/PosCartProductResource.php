<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosCartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainProduct = Product::find($this->id);
        $size = $mainProduct->sizes()->where('id', $this->pivot->size)->first();
        $color = $mainProduct->colors()->where('id', $this->pivot->color)->first();

        $colorPrice = $color?->pivot->price ?? 0;
        $sizePrice = $size?->pivot->price ?? 0;
        $extraPrice = $colorPrice + $sizePrice;

        $price = $this->price + $extraPrice;
        $discountPrice = $this->discount_price > 0 ? $this->discount_price + $extraPrice : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand?->name ?? null,
            'thumbnail' => $this->thumbnail,
            'price' => (float) $price,
            'discount_price' => (float) $discountPrice,
            'order_qty' => (int) $this->pivot->quantity,
            'color' => $color ?? null,
            'size' => $size ?? null,
            'pos_cart_id' => $this->pivot?->id ?? null,
            'unit' => $this->unit?->name ?? null,
            'sku_numbers' => json_decode($this->pivot->sku_no) ?? [],
        ];
    }
}
