<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shop = User::role('root')->whereHas('shop')->first()?->shop;

        $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        foreach ($sizes as $size) {
            $shop->sizes()->create([
                'name' => $size,
            ]);
        }
    }
}
