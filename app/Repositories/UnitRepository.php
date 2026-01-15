<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\UnitRequest;
use App\Models\TranslateUtility;
use App\Models\Unit;

class UnitRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Unit::class;
    }

    /**
     * Store unit by request.
     *
     * @param  UnitRequest  $request  The unit request
     */
    public static function storeByRequest(UnitRequest $request): Unit
    {
        $shop = generaleSetting('rootShop');

        $unit = self::create([
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
                'unit_id' => $unit->id,
                'name' => $name,
                'lang' => $lang,
            ]);
        }

        return $unit;
    }

    /**
     * Update unit by request.
     *
     * @param  UnitRequest  $request  The unit request
     * @param  Unit  $unit  The unit to update
     * @return Unit The updated unit
     */
    public static function updateByRequest(UnitRequest $request, Unit $unit): Unit
    {
        $unit->update([
            'name' => $request->name,
        ]);

        // update and create translation
        foreach ($request->names ?? [] as $lang => $name) {
            if (! $lang || ! $name) {
                continue;
            }
            TranslateUtility::updateOrCreate([
                'unit_id' => $unit->id,
                'lang' => $lang,
            ], [
                'name' => $name,
            ]);
        }

        return $unit;
    }
}
