<?php

namespace App\Http\Resources;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = request()->header('accept-language') ?? 'en';

        $price = $this->pivot->price ?? 0;
        $product = ProductRepository::find($this->pivot?->product_id);

        // Calculate VAT extra price
        $amount = 0;
        if ($product) {
            foreach ($product->vatTaxes ?? [] as $tax) {
                if ($tax->percentage > 0) {
                    $amount += $price * ($tax->percentage / 100);
                }
            }
        }
        $price += $amount;

        $translation = $lang != 'en' ? $this->translations()?->where('lang', $lang)->first() : null;

        return [
            'id' => $this->id,
            'name' => $translation ? $translation->name : $this->name,
            'color_code' => $this->color_code,
            'price' => (float) number_format($price, 2, '.', ''),
        ];
    }
}
