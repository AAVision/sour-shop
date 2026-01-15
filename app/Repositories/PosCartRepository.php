<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Requests\PosCartRequest;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\OrderVatTax;
use App\Models\PosCart;
use App\Models\PosCartProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class PosCartRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return PosCart::class;
    }

    public static function getLatestCart(Request $request)
    {
        $shop = generaleSetting('shop');

        $name = $request->name ?? null;

        return self::query()
            ->where('shop_id', $shop->id)
            ->when($name, function ($query) use ($name) {
                return $query->where('name', $name);
            })
            ->whereCreatedBy($request->user()->id)
            ->latest('id')
            ->first();
    }

    public static function storeByRequest(PosCartRequest $request, Product $product)
    {
        $shop = generaleSetting('shop');

        $posCart = self::query()
            ->where('shop_id', $shop->id)
            ->where('name', $request->name)
            ->whereCreatedBy($request->user()->id)
            ->first();

        if ($posCart) {
            self::attachProduct($posCart, $product->id, $request);

            $calculateAmount = self::calculateTotal($posCart);
            $posCart->update([
                'subtotal' => $calculateAmount['subtotal'],
                'total' => $calculateAmount['total'],
                'discount' => $calculateAmount['discount'],
            ]);

            return $posCart;
        }

        $posCart = self::create([
            'name' => $request->name ?? str()->random(12),
            'shop_id' => $shop->id,
            'created_by' => $request->user()->id,
            'ip_address' => $request->ip(),
            'user_id' => $request->user_id,
        ]);

        self::attachProduct($posCart, $product->id, $request);

        $calculateAmount = self::calculateTotal($posCart);
        $posCart->update([
            'subtotal' => $calculateAmount['subtotal'],
            'total' => $calculateAmount['total'],
            'discount' => (float) $calculateAmount['discount'],
        ]);

        return $posCart;
    }

    public static function updateByRequest(PosCartRequest $request, PosCartProduct $posCartProduct)
    {
        $posCartProduct->update([
            'quantity' => $request->quantity,
            'color' => $request->color,
            'size' => $request->size,
            'unit' => $request->unit,
        ]);
        if (function_exists('module_exists') && module_exists('Purchase')) {
            $posCartProduct->update([
                'sku_no' => json_encode([]),
            ]);
        }

        $calculateAmount = self::calculateTotal($posCartProduct->posCart);

        $posCartProduct->posCart->update([
            'subtotal' => $calculateAmount['subtotal'],
            'total' => $calculateAmount['total'],
            'discount' => $calculateAmount['discount'],
        ]);

        return $posCartProduct->posCart;
    }

    private static function attachProduct(PosCart $postCart, $productID, $request)
    {
        // check if product exist
        $existCartProduct = PosCartProduct::where('pos_cart_id', $postCart->id)->where('product_id', $productID)->where('color', $request->color)->where('size', $request->size)->first();

        if ($existCartProduct) {
            $existCartProduct->update([
                'quantity' => $request->quantity,
            ]);

            if (function_exists('module_exists') && module_exists('Purchase')) {
                $existCartProduct->update([
                    'sku_no' => json_encode([]),
                ]);
            }

            return $existCartProduct;
        }

        // attach new product
        return $postCart->products()->attach($productID, [
            'quantity' => $request->quantity,
            'color' => $request->color,
            'size' => $request->size,
            'unit' => $request->unit ?? null,
        ]);
    }

    private static function calculateTotal(PosCart $posCart)
    {
        $subtotal = 0;
        $total = 0;
        $discount = 0;

        foreach ($posCart->products as $product) {
            $productPrice = $product->discount_price > 0 ? $product->discount_price : $product->price;

            $mainProduct = Product::find($product->id);

            $size = $mainProduct->sizes()->where('id', $product->pivot->size)->first();
            $color = $mainProduct->colors()->where('id', $product->pivot->color)->first();

            $colorPrice = $color?->pivot->price ?? 0;
            $sizePrice = $size?->pivot->price ?? 0;
            $extraPrice = $colorPrice + $sizePrice;

            $subtotal += $product->pivot->quantity * ($productPrice + $extraPrice);
        }

        $total = $subtotal - ($posCart->discount ?? 0);

        if ($posCart->coupon) {
            $couponDiscount = CouponRepository::getCouponDiscountAmount($posCart->coupon, $subtotal)['discount_amount'];

            if ($couponDiscount > 0) {
                $discount = $couponDiscount;
                $total = $subtotal - $discount;
            }
        }

        return [
            'subtotal' => $subtotal,
            'total' => $total,
            'discount' => $discount,
        ];
    }

    public static function applyCoupon($request, Coupon $coupon, PosCart $postCart)
    {
        if ($coupon) {
            $getDiscount = CouponRepository::getCouponDiscountAmount($coupon, $postCart->subtotal);

            if ($getDiscount['discount_amount'] > 0) {
                $totalAmount = $postCart->subtotal - $getDiscount['discount_amount'];

                $postCart->update([
                    'discount' => $getDiscount['discount_amount'],
                    'total' => $totalAmount,
                    'coupon_id' => $coupon->id,
                ]);
            }
        }

        return $postCart;
    }

    public static function removeCoupon(PosCart $postCart)
    {
        $postCart->update([
            'coupon_id' => null,
            'discount' => 0,
        ]);

        $calculateAmount = self::calculateTotal($postCart);

        $postCart->update([
            'subtotal' => $calculateAmount['subtotal'],
            'total' => $calculateAmount['total'],
            'discount' => $calculateAmount['discount'],
        ]);

        return $postCart;
    }

    public static function destroyProduct($request, PosCart $postCart)
    {
        $postCartProduct = PosCartProduct::find($request->pos_cart_id);
        if ($postCartProduct) {
            $postCartProduct->delete();
        }

        $calculateAmount = self::calculateTotal($postCart);
        $postCart->update([
            'subtotal' => $calculateAmount['subtotal'],
            'total' => $calculateAmount['total'],
            'discount' => $calculateAmount['discount'],
        ]);

        return $postCart;
    }

    public static function storeOrder(PosCart $posCart, $request)
    {
        $shop = generaleSetting('shop');

        if ($request->order_type == 'draft') {
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
            }
            $posCart->update([
                'is_draft' => true,
                'user_id' => $customer?->user?->id ?? null,
            ]);
            self::createNewCart();

            return true;
        }

        $paymentMethod = $request->payment_method == 'cash' ? PaymentMethod::CASH->value : PaymentMethod::ONLINE->value;

        $vatTaxes = VatTaxRepository::getActiveVatTaxes();
        $allVatTaxes = [];
        $totalTaxAmount = 0;

        $subtotal = $posCart->subtotal;

        foreach ($vatTaxes ?? [] as $vatTax) {
            if ($vatTax?->name && $vatTax?->percentage > 0 && $subtotal > 0) {
                $taxAmount = round($subtotal * ($vatTax->percentage / 100), 2);

                $allVatTaxes[] = (object) [
                    'name' => $vatTax->name,
                    'percentage' => $vatTax->percentage,
                    'amount' => $taxAmount,
                ];

                $totalTaxAmount += $taxAmount;
            }
        }
        $total = $posCart->total ?? 0;

        $lastOrderId = self::query()->max('id');
        $order = OrderRepository::create([
            'shop_id' => $shop->id,
            'pos_order' => true,
            'customer_id' => $request->customer_id ?? null,
            'order_code' => str_pad($lastOrderId + 1, 6, '0', STR_PAD_LEFT),
            'prefix' => $shop->prefix ?? 'RC',
            'coupon_id' => $posCart->coupon_id,
            'delivery_charge' => 0,
            'total_amount' => $posCart->subtotal,
            'coupon_discount' => $posCart->discount,
            'tax_amount' => $totalTaxAmount,
            'payable_amount' => $total + $totalTaxAmount,
            'payment_method' => $paymentMethod,
            'order_status' => OrderStatus::DELIVERED->value,
            'instruction' => $request->note,
            'payment_status' => PaymentStatus::PAID->value,
        ]);

        foreach ($posCart->products as $product) {
            $quantity = $product->quantity - $product->pivot->quantity;
            $product->update([
                'quantity' => ($quantity > 0) ? $quantity : 0,
            ]);

            $sku = $product->pivot->sku_no ? collect(json_decode($product->pivot->sku_no, true))->implode(', ') : null;

            $size = $product->sizes()?->where('id', $product->pivot->size)->first();
            $color = $product->colors()?->where('id', $product->pivot->color)->first();
            $price = $product->discount_price > 0 ? $product->discount_price : $product->price;
            $attachData = [
                'quantity' => $product->pivot->quantity,
                'price' => $price,
                'unit' => $product->pivot->unit,
                'color' => $color?->name,
                'size' => $size?->name,
                'buying_price' => $product->buyingPrice() ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (function_exists('module_exists') && module_exists('Purchase')) {
                $attachData['sku'] = $sku;
            }
            $order->products()->attach($product->id, $attachData);

            if (function_exists('module_exists') && module_exists('Purchase')) {
                $skuArray = json_decode($product->pivot->sku_no, true);

                if (is_array($skuArray) && !empty($skuArray)) {
                    $productSkus = \Modules\Purchase\App\Models\ProductSku::whereIn('sku', $skuArray)->get();
                    foreach ($productSkus as $productSku) {
                        $productSku->update([
                            'in_stock' => false,
                            'quantity' => 0,
                        ]);
                    }
                }
            }

            if (function_exists('module_exists') && module_exists('Purchase')) {
                $order->productStockOuts()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity
                ]);
            }
        }

        // store order vat taxes
        foreach ($allVatTaxes ?? [] as $vatTax) {
            if (! $vatTax) {
                continue;
            }
            OrderVatTax::create([
                'order_id' => $order->id,
                'name' => $vatTax->name,
                'percentage' => $vatTax->percentage,
                'amount' => $vatTax->amount,
            ]);
        }

        $posCart->products()->detach();
        $posCart->delete();

        self::createNewCart();

        return $order;
    }

    private static function createNewCart()
    {
        $shop = generaleSetting('shop');

        return self::create([
            'name' => str()->random(12),
            'shop_id' => $shop->id,
            'created_by' => request()->user()->id,
            'ip_address' => request()->ip(),
            'is_draft' => false,
        ]);
    }
}
