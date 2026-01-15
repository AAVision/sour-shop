<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shop = User::role('root')->whereHas('shop')->first()?->shop;
        $units = [
            'pcs',
            'kg',
            'g',
            'ml',
            'l',
            'pack',
            'dozen',
            'box',
            'bottle',
            'jar',
        ];

        foreach ($units as $unit) {
            $shop->units()->create([
                'name' => $unit,
            ]);
        }
    }
}
