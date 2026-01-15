<?php

namespace App\Http\Controllers\API\Seller;

use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use App\Enums\ReturnOderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Enum;
use App\Http\Resources\ReturnOrderResource;
use App\Http\Resources\ReturnOrderDetailsResource;

class ReturnOrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;
        $shop = auth()->user()->shop;

        $returnOrders = $shop->returnOrders()->latest('id');

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
        $shop = auth()->user()->shop;

        $returnOrder = $shop->returnOrders()->where('id', $returnOrder->id)->first();

        if ($returnOrder->shop_id != $shop->id) {
            return $this->json('error', __('You do not have permission to view this order'));
        }

        return $this->json('returnOrders', [
            'returnOrders' => ReturnOrderDetailsResource::make($returnOrder),
        ]);
    }

    public function statusChange(ReturnOrder $returnOrder, Request $request)
    {
        $request->validate(['status' => ['required', new Enum(ReturnOderStatus::class)]]);

        if ($returnOrder->payment_status == 1) {
            return $this->json('error', __('Already paid for this order'));
        }

        $shop = auth()->user()->shop;

        if ($returnOrder->shop_id != $shop->id) {
            return $this->json('error', __('You do not have permission to update this order'));
        }

        $returnOrder->update(['status' => $request->status]);

        return $this->json('success', __('Status updated successfully'));
    }
}
