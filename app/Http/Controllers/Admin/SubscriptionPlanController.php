<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ShopSubscription;
use App\Models\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Repositories\ShopRepository;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Repositories\ShopSubscriptionRepository;
use App\Repositories\SubscriptionPlanRepository;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $subscriptionPlans = SubscriptionPlanRepository::query()->paginate(20);

        return view('admin.subscription-plan.index', compact('subscriptionPlans'));
    }

    public function create()
    {
        return view('admin.subscription-plan.create');
    }

    public function store(SubscriptionPlanRequest $request)
    {
        SubscriptionPlanRepository::storeByRequest($request);

        return to_route('admin.subscription-plan.index')->withSuccess(__('Created successfully'));
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plan.edit', compact('subscriptionPlan'));
    }

    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        SubscriptionPlanRepository::updateByRequest($request, $subscriptionPlan);

        return to_route('admin.subscription-plan.index')->withSuccess(__('Updated successfully'));
    }

    public function statusToggle(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update([
            'is_active' => ! $subscriptionPlan->is_active,
        ]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return back()->withSuccess('deleted successfully');
    }

    public function subscriptionList(Request $request)
    {
        $shop = $request->shop;

        $subscriptions = ShopSubscription::when($shop, function ($query) use ($shop) {
            return $query->where('shop_id', $shop);
        })->latest()->paginate(20);

        $shops = ShopRepository::query()->isActive()->get();

        return view('admin.subscription-plan.list', compact('subscriptions', 'shops'));
    }

    public function subscriptionStatus(ShopSubscription $shopSubscription)
    {
        try {
            $saleLimit = $shopSubscription->sale_limit;
            $remainingSales = $shopSubscription->sale_limit;

            $currentSubscription = ShopSubscriptionRepository::query()
                ->where('shop_id', $shopSubscription->shop_id)
                ->where('status', SubscriptionStatus::ACTIVE->value)
                ->first();

            if ($currentSubscription) {
                if ($saleLimit && $currentSubscription->remaining_sales) {
                    $saleLimit = $saleLimit + $currentSubscription->remaining_sales;
                    $remainingSales = $saleLimit + $currentSubscription->remaining_sales;
                }

                $currentSubscription->update([
                    'status' => SubscriptionStatus::CANCELLED,
                ]);
            }
            $shopSubscription->update([
                'starts_at' => now(),
                'ends_at' => $shopSubscription->duration ? now()->addDays($shopSubscription->duration) : null,
                'sale_limit' => $saleLimit,
                'remaining_sales' => $remainingSales,
                'status' => SubscriptionStatus::ACTIVE,
            ]);
            return back()->withSuccess(__('Status updated successfully'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
}
