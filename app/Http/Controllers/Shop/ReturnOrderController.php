<?php

namespace App\Http\Controllers\Shop;

use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use App\Enums\ReturnOderStatus;
use App\Http\Controllers\Controller;
use App\Repositories\ReturnOrderRepository;

class ReturnOrderController extends Controller
{
    public function index()
    {
        $shopId = auth()->user()->shop->id;
        $returnOrder = ReturnOrderRepository::query()->where('shop_id', $shopId)->latest('id')->paginate(20);
        return view('shop.returnOrder.index', compact('returnOrder'));
    }

    public function show(ReturnOrder $returnOrder)
    {
        if ($returnOrder->shop_id != auth()->user()->shop->id) {
          //  abort(404);
        }
        $returnStatus = ReturnOderStatus::cases();
        return view('shop.returnOrder.show', compact('returnOrder', 'returnStatus'));
    }

    public function refundIndex()
    {
        $shopId = auth()->user()->shop->id;
        $returnOrder = ReturnOrderRepository::query()->where('shop_id', $shopId)->where('payment_status', 1)->latest('id')->paginate(20);
        return view('shop.returnOrder.index', compact('returnOrder'));
    }

    public function statusChange(ReturnOrder $returnOrder, Request $request)
    {
        $request->validate(['status' => 'required']);

        $shopId = auth()->user()->shop->id;

        if ($returnOrder->shop_id != $shopId) {
            abort(404);
        }

        if ($returnOrder->payment_status == 1) {
            return back()->with('error', __('Already paid for this order'));
        }

        $returnOrder->update(['status' => $request->status]);

        return back()->with('success', __('Status updated successfully'));
    }
}
