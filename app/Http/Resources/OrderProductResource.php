<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\ReturnOrderDetail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load('brand', 'reviews');

        $review = $this->reviews()->where('customer_id', auth()->user()->customer?->id)->where('product_id', $this->id)->where('order_id', $request->order_id)->first();

        $price = $this->pivot->price > 0 ? $this->pivot->price : ($this->discount_price > 0 ? $this->discount_price : $this->price);

        $isReturned = ReturnOrderDetail::where('product_id', $this->id)
            ->whereHas('returnOrder', function ($q) use ($request) {
                $q->where('order_id', $request->order_id);
            })
            ->exists();
        $isReturnable = ! $isReturned;

        $license = $this->licenses()->where('product_id', $this->id)->where('user_id', Auth::guard('api')->user()->id)->where('order_id', $request->order_id)->first();
        $licenseDownloadLink = $license ? route('download.product.license', ['license' => $license->product_license, 'order_id' => $request->order_id]) : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand?->name ?? null,
            'thumbnail' => $this->thumbnail,
            'price' => (float) $this->price,
            'discount_price' => (float) $this->discount_price > 0 ? $price : 0,
            'order_qty' => (int) $this->pivot->quantity,
            'color' => $this->pivot->color ?? null,
            'size' => $this->pivot->size ?? null,
            'rating' => $review ? (float) $review->rating : null,
            'unit' => $this->pivot->unit ?? null,
             'is_returned' => $isReturnable,
             'is_digital' => (bool) $this->is_digital,
            'license' => $license,
            'license_download_link' => $license ? $licenseDownloadLink : null,
            'attachments' => $this->additionalAttachments(),
        ];
    }
}
