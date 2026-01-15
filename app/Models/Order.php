<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Support\Str;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Scopes\PosOrderFalse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'order_status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
    ];

    /**
     * Get all of the products for the Order.
     */
    public function products(): BelongsToMany
    {
        $pivotColumns = ['quantity', 'color', 'unit', 'size', 'price'];

        if (Schema::hasColumn('order_products', 'sku')) {
            $pivotColumns[] = 'sku';
        }
        return $this->belongsToMany(Product::class, 'order_products')->withPivot($pivotColumns)->withoutGlobalScopes();
    }

    /**
     * Get all of the vat taxes for the Order.
     */
    public function vatTaxes(): HasMany
    {
        return $this->hasMany(OrderVatTax::class, 'order_id');
    }

    /**
     * Get the customer that owns the Order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the shop for the Order.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the coupon for the Order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id')->withTrashed();
    }

    /**
     * Get the address for the Order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Get the payments for the Order.
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'order_payments');
    }

    /**
     * Get the driver order for the Order.
     */
    public function driverOrder(): BelongsTo
    {
        return $this->belongsTo(DriverOrder::class, 'id', 'order_id');
    }

    /**
     * apply global scope
     */
    protected static function booted()
    {
        static::addGlobalScope(new PosOrderFalse);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function () {
            self::clearOrderCache();
        });

        static::updated(function () {
            self::clearOrderCache();
        });

        static::deleted(function () {
            self::clearOrderCache();
        });
    }

    protected static function clearOrderCache()
    {
        $cacheKeys = [
            'admin_all_orders',
            'shop_all_orders',
        ];

        foreach (OrderStatus::cases() as $status) {
            $cacheKeys[] = 'admin_status_'.Str::camel($status->value);
            $cacheKeys[] = 'shop_status_'.Str::camel($status->value);
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

     public function productStockOuts(): ?HasMany
    {
        if (function_exists('module_exists') && module_exists('Purchase')) {
            return $this->hasMany(\Modules\Purchase\App\Models\ProductStockOut::class);
        }
        return null;
    }
    public function scopeCompleted($query)
    {
        return $query->where('order_status','Delivered');
    }
    public function scopeCancelled($query)
    {
        return $query->where('order_status','Cancelled');
    }
}
