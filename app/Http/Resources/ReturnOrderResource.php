<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderResource extends JsonResource
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
            'order_id' => $this->order->prefix . $this->order->order_code,
            'reason' => $this->reason,
            'amount' => $this->amount,
            'status' => $this->status,
            'quantity' =>$this->returnProduct?->sum('quantity'),
            'payment_status' => $this->payment_status ? 'Paid' : 'Unpaid',
            'reject_note' => $this->reject_note,
            'return_date' => $this->created_at->format('d F, Y'),
            'return_address' => $this->return_address,
        ];
    }
}
