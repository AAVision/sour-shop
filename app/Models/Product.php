<?php

namespace App\Models;

use App\Models\Scopes\hasSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

#[ScopedBy([hasSubscription::class])]
class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['thumbnail'];

    /**
     * Retrieve the shop that this model belongs to.
     *
     * @return BelongsTo The shop that this model belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * get the translations that owns the product.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /**
     * Retrieve the categories associated with the current model.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Retrieve the categories associated with the current model.
     */
    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'product_subcategories');
    }

    /**
     * Retrieve the flash sales associated with the model.
     *
     * @return BelongsToMany The flash sales associated with the model.
     */
    public function flashSales(): BelongsToMany
    {
        $currentDateTime = Carbon::now();

        return $this->belongsToMany(FlashSale::class, 'flash_sale_products', 'product_id', 'flash_sale_id')
            ->withPivot('price', 'quantity', 'discount', 'sale_quantity')
            ->where('status', 1)
            ->where(function ($query) use ($currentDateTime) {
                $query->where('start_date', '<=', $currentDateTime->toDateString())
                    ->where('end_date', '>=', $currentDateTime->toDateString())
                    ->where(function ($query) use ($currentDateTime) {
                        $query->where('start_time', '<=', $currentDateTime->toTimeString())
                            ->orWhere('end_time', '>=', $currentDateTime->toTimeString());
                    });
            })->latest('id');
    }

    /**
     * Get the video media record associated with the model.
     *
     * @return BelongsTo The video media record associated with the model.
     */
    public function videoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'video_id');
    }

    /**
     * Retrieves the video associated with the model.
     *
     * @return Attribute The video attribute.
     */
    public function video(): Attribute
    {
        $video = null;

        if ($this->videoMedia && $this->videoMedia->type == 'file' && Storage::exists($this->videoMedia->src)) {
            $video = (object) [
                'id' => $this->videoMedia->id,
                'url' => Storage::url($this->videoMedia->src),
                'type' => $this->videoMedia->type,
            ];
        } elseif ($this->videoMedia && $this->videoMedia->type != 'file' && $this->videoMedia->src != null) {
            $video = (object) [
                'id' => $this->videoMedia->id,
                'url' => $this->videoMedia->src,
                'type' => $this->videoMedia->type,
            ];
        }

        return new Attribute(
            get: fn() => $video
        );
    }

    /**
     * Get the media record associated with the model.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Create a thumbnail for the media, with a default image if none is present.
     */
    public function thumbnail(): Attribute
    {
        $thumbnail = asset('default/default.jpg');
        if ($this->media && Storage::exists($this->media->src)) {
            $thumbnail = Storage::url($this->media->src);
        }

        return new Attribute(
            get: fn() => $thumbnail
        );
    }

    /**
     * Get the medias for the product.
     */
    public function medias(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_thumbnails');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_attachments');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(ProductLicense::class);
    }

    /**
     * Generate thumbnails for the medias.
     */
    public function thumbnails(): Collection
    {
        $thumbnails = collect([]);

        if (request()->is('api/*')) {
            if ($this->videoMedia && $this->videoMedia->type == 'file' && Storage::exists($this->videoMedia->src)) {
                $thumbnails[] = (object) [
                    'id' => $this->videoMedia->id,
                    'thumbnail' => null,
                    'url' => Storage::url($this->videoMedia->src),
                    'type' => $this->videoMedia->type,
                ];
            } elseif ($this->videoMedia && $this->videoMedia->type != 'file' && $this->videoMedia->src != null) {
                $thumbnails[] = (object) [
                    'id' => $this->videoMedia->id,
                    'thumbnail' => null,
                    'url' => $this->videoMedia->src,
                    'type' => $this->videoMedia->type,
                ];
            }

            $thumbnails[] = (object) [
                'id' => $this->media?->id,
                'thumbnail' => $this->thumbnail,
                'url' => null,
                'type' => 'image',
            ];
        }

        foreach ($this->medias as $media) {
            $thumbnail = asset('default/default.jpg');
            if ($media && Storage::exists($media->src)) {
                $thumbnail = Storage::url($media->src);
            }
            $thumbnails[] = (object) [
                'id' => $media?->id,
                'thumbnail' => $thumbnail,
                'url' => null,
                'type' => 'image',
            ];
        }

        return $thumbnails;
    }

    /**
     * Generate additional thumbnails for the medias.
     */
    public function additionalAttachments(): Collection
    {
        $attachments = collect([]);
        foreach ($this->attachments as $attachment) {
            $file = asset('default/default.jpg');
            if ($attachment && Storage::exists($attachment->src)) {
                $file = Storage::url($attachment->src);
            }
            $attachments[] = (object) [
                'id' => $attachment?->id,
                'url' => $file,
                'extension' => strtolower(pathinfo($attachment->src, PATHINFO_EXTENSION)),
            ];
        }

        return $attachments;
    }

    public function additionalThumbnails(): Collection
    {
        $thumbnails = collect([]);
        foreach ($this->medias as $media) {
            $thumbnail = asset('default/default.jpg');
            if ($media && Storage::exists($media->src)) {
                $thumbnail = Storage::url($media->src);
            }
            $thumbnails[] = (object) [
                'id' => $media?->id,
                'thumbnail' => $thumbnail,
            ];
        }

        return $thumbnails;
    }

    /**
     * Retrieves the reviews associated with this object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The reviews associated with this object.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Calculates the average rating of the reviews.
     *
     * @return Attribute The average rating attribute.
     */
    public function averageRating(): Attribute
    {
        $avgRating = $this->reviews()->avg('rating');

        return new Attribute(
            get: fn() => (float) number_format($avgRating > 0 ? $avgRating : 0, 1, '.', '')
        );
    }

    /**
     * Retrieves the colors associated with this model.
     */
    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_colors')->withPivot('price', 'product_id');
    }

    /**
     * sizes function.
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_sizes')->withPivot('price', 'product_id');
    }

    /**
     * get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * get the unit that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Retrieve the orders associated with the model.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_products')->withPivot('quantity', 'color', 'unit', 'size', 'price');
    }

    /**
     * Filter the given builder by active status.
     *
     * @param  Builder  $builder  The builder to filter.
     * @return Builder The filtered builder.
     */
    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', true)->where('is_approve', true)->whereHas('shop', function ($query) {
            return $query->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            });
        });
    }

    /**
     * Calculate the discount percentage based on the given price and discount price.
     */
    public static function getDiscountPercentage($price, $discountPrice)
    {
        return $discountPrice ? ($price - $discountPrice) * 100 / $price : 0;
    }

    /**
     * get the favorites from the model.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function purchaseProducts(): ?HasMany
    {
        if (function_exists('module_exists') && module_exists('Purchase')) {
            return $this->hasMany(\Modules\Purchase\App\Models\PurchaseProduct::class);
        }
        return null;
    }
    public function productStockOuts(): ?HasMany
    {
        if (function_exists('module_exists') && module_exists('Purchase')) {
            return $this->hasMany(\Modules\Purchase\App\Models\ProductStockOut::class);
        }
        return null;
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function buyingPrice()
    {
        if (module_exists('Purchase')) {
            $latestPurchase = \Modules\Purchase\App\Models\PurchaseProduct::where('product_id', $this->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($latestPurchase) {
                return $latestPurchase->quantity > 0
                    ? $latestPurchase->price / $latestPurchase->quantity
                    : $this->buy_price;
            }
        }
        return $this->buy_price;
    }
}
