<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use App\Enums\ReturnOderStatus;
use App\Http\Controllers\Controller;
use App\Repositories\WalletRepository;
use App\Repositories\ReturnOrderRepository;
use App\Repositories\TransactionRepository;

class ReturnOrderController extends Controller
{
    public function index()
    {
        $returnOrder = ReturnOrderRepository::query()->latest('id')->paginate(20);
        return view('admin.returnOrder.index', compact('returnOrder'));
    }

    public function show(ReturnOrder $returnOrder)
    {
        $returnStatus = ReturnOderStatus::cases();
        return view('admin.returnOrder.show', compact('returnOrder', 'returnStatus'));
    }

    public function paymentStatus(ReturnOrder $returnOrder)
    {
        if ($returnOrder->status == 'Pending') {
            return back()->with('error', __('Return order is not approved yet'));
        }
        if ($returnOrder->status == 'Cancelled') {
            return back()->with('error', __('Return order is Cancelled'));
        }
        if ($returnOrder->payment_status == 1) {
            return back()->with('error', __('Payment status updated successfully'));
        }

       $returnOrder->update(['payment_status' => 1, 'status' => 'Refunded']);

        $this->updateWalletAndTransaction($returnOrder);

        return back()->with('success', __('Payment status updated successfully'));
    }


    private function updateWalletAndTransaction($returnOrder)
    {

        $generaleSetting = generaleSetting('setting');

        $commission = 0;

        if ($generaleSetting?->commission_charge != 'monthly') {

            if ($generaleSetting?->commission_type != 'fixed') {
                $commission = $returnOrder->amount * $generaleSetting->commission / 100;
            } else {
                $commission = $generaleSetting->commission ?? 0;
            }
        }
        $amount = $returnOrder->amount;

        $wallet = $returnOrder->shop->user->wallet;

       WalletRepository::updateByRequest($wallet, $amount, 'debit');

       TransactionRepository::storeByRefundRequest($wallet, $commission, 'credit', true, true, 'admin commission removal for refund order', 'refundorder');
    }

    public function returnReject(ReturnOrder $returnOrder, Request $request)
    {
        $returnOrder->update([
            'status' => $request->status,
            'reject_note' => $request->reject_note
        ]);
        return back()->with('success', __('Return order Cancelled successfully'));
    }
}
