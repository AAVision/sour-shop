<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shop = User::role('root')->whereHas('shop')->first()?->shop;

        $colors = $shop->colors;
        $sizes = $shop->sizes;
        $categories = $shop->categories;
        $units = $shop->units;

        for ($i = 1; $i <= rand(50, 120); $i++) {
            $product = Product::factory()->create([
                'unit_id' => $units->random()?->id,
            ]);

            for ($j = 0; $j < 4; $j++) {
                $media = Media::factory()->create();
                $product->medias()->attach($media);
            }

            $product->colors()->attach($colors->random(3));
            $product->sizes()->attach($sizes->random(4));
            $product->categories()->attach($categories->random(1));
        }
    }
}
