<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\ProductRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\RecentView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class ProductRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Product::class;
    }

    public static function recentView(Product $product)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            RecentView::where('product_id', $product->id)->where('user_id', $user->id)->firstOrCreate([
                'product_id' => $product->id,
                'user_id' => $user->id,
            ])?->update(['updated_at' => now()]);
        }

        return $product;
    }

    /**
     * Sanitizes a string by removing invalid or non-printable characters.
     *
     * @param  string  $input
     * @return string
     */
    public static function sanitizeUnicode($input)
    {
        $cleanedInput = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $input);
        $cleanedInput = preg_replace('/[\xF0-\xF9][\x80-\xBF][\xF0\x9F]{3}/u', '', $input);
        $cleanedInput = preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/u', '', $cleanedInput);
        $cleanedInput = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $cleanedInput);
        $cleanedInput = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $cleanedInput);

        return $cleanedInput;
    }

    /**
     * store new product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     *                                                      return \App\Models\Product
     */
    public static function storeByRequest(ProductRequest $request): Product
    {
        $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'thumbnail');
        $isDigital = false;

        if (isset($request->is_digital)) {
            $isDigital = true;
        }

        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $videoMedia = self::videoCreateOrUpdate($request);
        $description = Purifier::clean(self::sanitizeUnicode($request->description));

        $keywords = implode(',', $request->meta_keywords ?? []);

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $request->name,
            'description' => $description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand,
            'unit_id' => $request->unit,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' =>  $isDigital ? ($request->quantity > 1 ? $request->quantity : 999999) : $request->quantity,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail->id,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_digital' => $isDigital,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'video_id' => $videoMedia ? $videoMedia->id : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $keywords ? Str::limit($keywords, 200, '') : null,
        ]);

        foreach ($request->names ?? [] as $key => $value) {
            if (! $key || ! $value) {
                continue;
            }

            $description = array_key_exists($key, $request->descriptions ?? []) ? $request->descriptions[$key] : null;
            $shortDescription = array_key_exists($key, $request->short_descriptions ?? []) ? $request->short_descriptions[$key] : null;

            ProductTranslation::create([
                'product_id' => $product->id,
                'lang' => $key,
                'name' => $value,
                'description' => $description,
                'short_description' => $shortDescription,
            ]);
        }

        if ($request->is('api/*')) {
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
                $product->colors()->sync($colors);
            }
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }
        }

        foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
            $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
            $product->medias()->attach($thumbnail->id);
        }

        foreach ($request->digitalAttachment ?? [] as $digitalAttachment) {
            $thumbnail = MediaRepository::storeByRequest($digitalAttachment, 'products/digital_attachment');
            $product->attachments()->attach($thumbnail->id);
        }

        if(!empty($request->license_key)){
            foreach ($request->license_key ?? [] as $license) {
                ProductLicenseRepository::storeByRequest($product->id, $license);
            }
        }

        return $product;
    }

    /**
     * Update the product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     *                                                      return \App\Models\Product
     */
    public static function updateByRequest(ProductRequest $request, Product $product): Product
    {
        $thumbnail = $product->media;
        if ($request->hasFile('thumbnail') && $thumbnail) {
            $thumbnail = MediaRepository::updateByRequest(
                $request->thumbnail,
                'products',
                'image',
                $thumbnail
            );
        }

        if ($request->hasFile('thumbnail') && $thumbnail == null) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'image');
        }

        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->update_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $videoMedia = self::videoCreateOrUpdate($request, $product);
        $description = Purifier::clean(self::sanitizeUnicode($request->description));
        $keywords = implode(',', $request->meta_keywords ?? []);

        self::update($product, [
            'name' => $request->name,
            'description' => $description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand ?? null,
            'unit_id' => $request->unit ?? null,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $product->is_digital ? ($request->quantity > 1 ? $request->quantity : 999999) : $request->quantity ?? 0,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => false,
            'is_approve' => $isAdmin ? true : $approve,
            'video_id' => $videoMedia ? $videoMedia->id : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $keywords ? Str::limit($keywords, 200, '') : null,
        ]);

        foreach ($request->names ?? [] as $key => $value) {
            if (! $key || ! $value) {
                continue;
            }

            $description = array_key_exists($key, $request->descriptions ?? []) ? $request->descriptions[$key] : null;
            $shortDescription = array_key_exists($key, $request->short_descriptions ?? []) ? $request->short_descriptions[$key] : null;

            ProductTranslation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'lang' => $key,
                ],
                [
                    'name' => $value,
                    'description' => $description,
                    'short_description' => $shortDescription,
                ]
            );
        }

        $product->colors()->detach();
        if ($request->is('api/*')) {
            $colors = [];
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
            }
            $product->colors()->attach($colors);
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        $product->sizes()->detach();
        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }
        }

        if ($request->is('api/*')) {
            self::updateAdditionThumbnails($request->previousThumbnail, $product);
        } else {
            foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
                $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
                $product->medias()->attach($thumbnail->id);
            }

            self::updatePreviousThumbnail($request->previousThumbnail);
        }

        if ($request->is('api/*')) {
            self::updateAdditionAttachments($request->attachmentPreviousThumbnail, $product);
        } else {
            foreach ($request->digitalAttachment ?? [] as $digitalAttachment) {
                $attachment = MediaRepository::storeByRequest($digitalAttachment, 'products/digital_attachment');
                $product->attachments()->attach($attachment->id);
            }

            self::updatePreviousThumbnail($request->attachmentPreviousThumbnail);
        }

        if(!empty($request->license_key)){
            foreach ($request->license_key ?? [] as $license) {
                ProductLicenseRepository::storeByRequest($product->id, $license);
            }
        }

        if(!empty($request->previous_license_key)){
            foreach ($request->previous_license_key ?? [] as $key => $license) {
                ProductLicenseRepository::updateByRequest($product->id, $license , $key);
            }
        }

        return $product;
    }

    private static function videoCreateOrUpdate($request, $product = null): ?Media
    {
        $media = $product?->videoMedia;
        $uploadVideoRequest = $request->uploadVideo;

        if (! $uploadVideoRequest || ! is_countable($uploadVideoRequest)) {
            return $media;
        }

        $type = $uploadVideoRequest['type'];
        $url = isset($uploadVideoRequest[$type . '_' . 'url']) ? $uploadVideoRequest[$type . '_' . 'url'] : null;

        if ($media && $type == 'file' && isset($uploadVideoRequest['file']) && is_file($uploadVideoRequest['file'])) {
            return MediaRepository::updateByRequest(
                $uploadVideoRequest['file'],
                'products',
                'file',
                $media
            );
        } elseif ($media && $type != 'file' && $url != null) {

            // Replace the width and height attributes in the iframe
            $customWidth = '100%';
            $customHeight = '650';
            $customizedIframe = preg_replace(['/width="(\d+(%?))"/', '/height="(\d+(%?))"/'], ["width=\"$customWidth\"", "height=\"$customHeight\""], $url);

            $media->update([
                'src' => $customizedIframe,
                'type' => $type,
            ]);

            return $media;
        }

        if (! $media && $type == 'file' && isset($uploadVideoRequest['file']) && is_file($uploadVideoRequest['file'])) {
            return MediaRepository::storeByRequest(
                $uploadVideoRequest['file'],
                'products',
                'file'
            );
        } elseif (! $media && $type != 'file' && $url != null) {

            $width = '100%';
            $height = '650';
            $customizedIframe = preg_replace(['/width="(\d+(%?))"/', '/height="(\d+(%?))"/'], ["width=\"$width\"", "height=\"$height\""], $url);

            return Media::create([
                'src' => $customizedIframe,
                'type' => $type,
            ]);
        }

        return $media;
    }

    /**
     * store new product from bulk import.
     */
    public static function bulkItemStore($rows, $folders = null)
    {
        $invalidRows = [];

        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        $total = 0;

        $folders = $folders !== null ? array_keys($folders) : [];

        $galleryPath = 'gallery/shop' . $shop->id;

        foreach ($rows as $row) {

            $createData = [];

            for ($i = 0; $i <= 13; $i++) {

                if ($i == 1) {
                    $createData['name'] = $row[$i];
                } elseif ($i == 2) {

                    $explodeThumbnails = explode(',', $row[$i]);

                    $thumbnails = [];
                    foreach ($explodeThumbnails as $thumbnail) {
                        $storeFile = null;
                        foreach ($folders as $folder) {
                            if (Storage::disk('public')->exists($galleryPath . '/' . $folder)) {
                                $files = File::files(Storage::disk('public')->path($galleryPath . '/' . $folder));
                                foreach ($files as $file) {
                                    if (basename($file) == $thumbnail) {
                                        $storeFile = $file;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($storeFile) {
                            $thumbnails[] = $storeFile;
                        }
                    }
                    $createData['thumbnails'] = $thumbnails;
                } elseif ($i == 3) {
                    $selectCategories = explode(',', $row[$i]);
                    $categories = [];
                    foreach ($selectCategories as $categoryName) {

                        $category = $rootShop->categories()->where('name', $categoryName)->first();

                        if ($category) {
                            $categories[] = $category->id;
                        }
                    }
                    $createData['categories'] = $categories;
                } elseif ($i == 4) {
                    $selectedSubCategories = explode(',', $row[$i]);
                    $subCategories = [];
                    foreach ($selectedSubCategories as $subCategoryName) {
                        $subCategory = $rootShop->subcategories()->where('name', $subCategoryName)->first();
                        if ($subCategory) {
                            $subCategories[] = $subCategory->id;
                        }
                    }
                    $createData['subCategories'] = $subCategories;
                } elseif ($i == 5) {
                    $brand = $rootShop->brands()->where('name', $row[$i])->first();
                    $createData['brand'] = $brand ? $brand->id : null;
                } elseif ($i == 6) {
                    $selectColors = explode(',', $row[$i]);
                    $colors = [];
                    foreach ($selectColors as $colorName) {
                        $color = $rootShop->colors()->where('name', $colorName)->first();
                        if ($color) {
                            $colors[] = $color->id;
                        }
                    }
                    $createData['colors'] = $colors;
                } elseif ($i == 7) {
                    $selectSizes = explode(',', $row[$i]);
                    $sizes = [];
                    foreach ($selectSizes as $sizeName) {
                        $size = $rootShop->sizes()->where('name', $sizeName)->first();
                        if ($size) {
                            $sizes[] = $size->id;
                        }
                    }
                    $createData['sizes'] = $sizes;
                } elseif ($i == 8) {
                    $createData['price'] = $row[$i];
                } elseif ($i == 9) {
                    $createData['discount_price'] = $row[$i];
                } elseif ($i == 10) {
                    $createData['sku'] = $row[$i];
                } elseif ($i == 11) {
                    $createData['stock_quantity'] = $row[$i];
                } elseif ($i == 12) {
                    $createData['short_description'] = $row[$i];
                } elseif ($i == 13) {
                    $createData['description'] = $row[$i];
                }
            }

            if ($createData['name'] != null && $createData['price'] != null && count($createData['categories']) != 0) {

                if ($createData['price'] < $createData['discount_price']) {
                    $createData['discount_price'] = $createData['price'];
                }

                self::storeBulkProduct($createData);

                $total = $total + 1;
            }
        }

        return $total;
    }

    /**
     * store new product from bulk import.
     *
     * @return Product
     */
    private static function storeBulkProduct($data)
    {
        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $thumbnail = $data['thumbnails'] ? $data['thumbnails'][0] : null;

        $media = self::storeMedia($thumbnail);

        $additionalThumbnails = $data['thumbnails'] ? array_slice($data['thumbnails'], 1) : [];

        $medias = [];
        foreach ($additionalThumbnails as $thumbnail) {
            $hasMedia = self::storeMedia($thumbnail);
            if ($hasMedia) {
                $medias[] = $hasMedia;
            }
        }

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? 'description',
            'short_description' => $data['short_description'] ?? 'short description',
            'brand_id' => $data['brand'] ?? null,
            'price' => $data['price'] ?? 0,
            'discount_price' => $data['discount_price'] ?? 0,
            'quantity' => $data['stock_quantity'] ?? 1,
            'min_order_quantity' => 1,
            'media_id' => $media,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'code' => $data['sku'] ?? random_int(100000, 999999),
        ]);

        $product->categories()->sync($data['categories'] ?? []);
        $product->subCategories()->sync($data['subCategories'] ?? []);
        $product->colors()->sync($data['colors'], []);
        $product->sizes()->sync($data['sizes'], []);

        $product->medias()->attach($medias);

        return $product;
    }

    public static function storeMedia($thumbnail)
    {
        if ($thumbnail != null) {

            $realPath = $thumbnail->getRealPath();

            $path = 'thumbnails';

            $fileName = random_int(100000, 999999) . date('YmdHis') . '.' . pathinfo($realPath, PATHINFO_EXTENSION);

            $storagePath = Storage::disk('public')->putFileAs($path, $thumbnail, $fileName);

            $media = Media::create([
                'name' => pathinfo($storagePath, PATHINFO_FILENAME),
                'src' => $storagePath,
                'type' => 'image',
                'original_name' => basename($realPath),
                'extension' => pathinfo($storagePath, PATHINFO_EXTENSION),
            ]);

            return $media->id;
        }

        return null;
    }

    /**
     * Update the previous thumbnails.
     *
     * @param  array  $previousThumbnails  The array of previous thumbnails
     */
    private static function updatePreviousThumbnail($previousThumbnails)
    {
        foreach ($previousThumbnails ?? [] as $thumbnail) {
            if (array_key_exists('file', $thumbnail) && array_key_exists('id', $thumbnail) && $thumbnail['file'] != null) {
                $media = Media::find($thumbnail['id']);

                MediaRepository::updateByRequest(
                    $thumbnail['file'],
                    'products',
                    'image',
                    $media
                );
            }
        }
    }

    /**
     * Update the additional thumbnails.
     *
     * @param  array  $additionalThumbnails  The array of additional thumbnails
     * @param  Product  $product
     */
    private static function updateAdditionThumbnails($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->medias()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->medias()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->medias()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }

    private static function updateAdditionAttachments($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->attachments()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->attachments()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->attachments()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }
}
