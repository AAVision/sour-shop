<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\ReturnOrderDetail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnOrderRequest;
use App\Http\Resources\ReturnOrderResource;
use App\Repositories\ReturnOrderRepository;
use App\Http\Resources\ReturnOrderDetailsResource;

class ReturnOrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $customer = auth()->user()->customer;

        $returnOrders = $customer->returnOrders()->latest('id');

        $total = $returnOrders->count();

        // paginate
        $returnOrders = $returnOrders->when($perPage && $page, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        })->get();

        return $this->json('returnOrders', [
            'total' => $total,
            'returnOrders' => ReturnOrderResource::collection($returnOrders),
        ]);
    }
    public function show(ReturnOrder $returnOrder)
    {
        $customer = auth()->user()->customer;

        $returnOrder = $customer->returnOrders()->where('id', $returnOrder->id)->first();

        return $this->json('returnOrders', [
            'returnOrders' => ReturnOrderDetailsResource::make($returnOrder),
        ]);
    }

    public function store(ReturnOrderRequest $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        $days = generaleSetting()->return_order_within_days;

        if ($order->created_at->diffInDays(now()) > $days) {
            return $this->json("Cannot return order after {$days} days", [], 422);
        }
        if ($order->order_status->value != 'Delivered') {
            return $this->json("This Order is not Delivered yet", [], 422);
        }

        foreach ($request->product_ids as $productId) {
            $orderProduct = $order->products()->where('product_id', $productId)->first();

            if (! $orderProduct) {
                return $this->json("Product with ID {$productId} not found in this order", [], 422);
            }
        }

        $alreadyReturned = ReturnOrderDetail::whereIn('product_id', $request->product_ids)
            ->whereHas('returnOrder', function ($q) use ($request) {
                $q->where('order_id', $request->order_id);
            })
            ->pluck('product_id')
            ->toArray();

        if (!empty($alreadyReturned)) {
            return $this->json("Products with IDs " . implode(', ', $alreadyReturned) . " already returned", [], 422);
        }

        $returnOrder = ReturnOrderRepository::storeByRequest($request);

        return $this->json('Order return successfully done', [
            'returnOrder' => ReturnOrderResource::make($returnOrder),
        ]);
    }
}
