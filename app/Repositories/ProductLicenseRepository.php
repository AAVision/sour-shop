<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\ProductLicense;

class ProductLicenseRepository extends Repository
{
    public static function model()
    {
        return ProductLicense::class;
    }

    public static function storeByRequest($product_id , $license)
    {
        return self::create([
            'product_id' => $product_id,
            'product_license' => $license,
        ]);
    }

    public static function updateByRequest($product_id , $license,$key)
    {
        $productLicense = ProductLicenseRepository::query()->where('id', $key)->where('product_id', $product_id)->first();
        $productLicense->product_license = $license;
        return $productLicense->save();
    }
}
