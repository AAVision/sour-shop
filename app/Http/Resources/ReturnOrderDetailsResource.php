<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // dd($this->returnProduct);
        return [

            'id' => $this->id,
            'order_id' => $this->order->prefix . $this->order->order_code,
            'reason' => $this->reason,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status ? 'Paid' : 'Unpaid',
            'shop_name'=>$this->shop->name ?? '',
            'shop_logo'=>$this->shop->logo ?? '',
            'shop_rating'=> (float) number_format($this->shop?->averageRating, 1, '.', ''),
            'reject_note' => $this->reject_note,
            'return_date' => $this->created_at->format('d F, Y'),
            'return_address' => $this->return_address,
            'customer_name' =>$this->customer->user->name ?? '',
            'customer_phone' =>$this->customer->user->phone ?? '',
            'customer_email' =>$this->customer->user->email ?? '',
            'return_order_products' => ReturnOrderProductResource::collection($this->returnProduct),
        ];
    }
}
