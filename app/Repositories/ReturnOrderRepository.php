<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\OrderProduct;
use App\Enums\ReturnOderStatus;
use Abedin\Maker\Repositories\Repository;

class ReturnOrderRepository extends Repository
{
    public static function model()
    {
        return ReturnOrder::class;
    }


    public static function storeByRequest($request)
    {
        $order = Order::find($request->order_id);
        $totalAmount = 0;
        $returnOrder = self::create([
            'order_id' => $request->order_id,
            'reason' => $request->reason,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'return_address' => $request->return_address,
            'shop_id' => $order->shop_id,
            'customer_id' => auth()->user()->customer->id,
            'status' => ReturnOderStatus::PENDING->value
        ]);
        foreach ($request->product_ids as $key => $productId) {

            $orderProduct = $order->products()->where('product_id', $productId)->first();
            
            $returnOrder->returnProduct()->create([
                'return_order_id' => $returnOrder->id,
                'product_id' => $productId,
                'price' => $orderProduct->pivot->price,
                'quantity' => $orderProduct->pivot->quantity,
                'color' => $orderProduct->pivot->color ?? '',
                'size' => $orderProduct->pivot->size ?? '',
                'unit' => $orderProduct->pivot->unit ?? '',
            ]);

            $totalAmount += $orderProduct->pivot->price * $orderProduct->pivot->quantity;
        }
        $returnOrder->amount = $totalAmount;
        $returnOrder->save();
        return $returnOrder;
    }
}
