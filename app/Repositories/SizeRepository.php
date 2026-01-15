<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\Models\TranslateUtility;

class SizeRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Size::class;
    }

    /**
     * store new size.
     *
     * @param  \App\Http\Requests\SizeRequest  $request
     *                                                   return \App\Models\Size
     * */
    public static function storeByRequest(SizeRequest $request): Size
    {
        $shop = generaleSetting('rootShop');

        $size = self::create([
            'name' => $request->name,
            'shop_id' => $shop->id,
            'is_active' => true,
        ]);

        // create translation
        foreach ($request->names ?? [] as $lang => $name) {
            if (! $lang || ! $name) {
                continue;
            }
            TranslateUtility::create([
                'size_id' => $size->id,
                'name' => $name,
                'lang' => $lang,
            ]);
        }

        return $size;
    }

    /**
     * Update the size.
     *
     * @param  \App\Http\Requests\SizeRequest  $request
     *                                                   return \App\Models\Size
     * */
    public static function updateByRequest(SizeRequest $request, Size $size): Size
    {
        $size->update([
            'name' => $request->name,
        ]);

        // update and create translation
        foreach ($request->names ?? [] as $lang => $name) {
            if (! $lang || ! $name) {
                continue;
            }
            TranslateUtility::updateOrCreate([
                'size_id' => $size->id,
                'lang' => $lang,
            ], [
                'name' => $name,
            ]);
        }

        return $size;
    }
}
