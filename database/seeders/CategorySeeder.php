<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            "Women's",
            "Men's",
            'Beauty',
            'Jewelry',
            'Home',
            'Kids',
            'Electronics',
            'Sports',
            'Books',
        ];

        $shop = User::role('root')->whereHas('shop')->first()?->shop;

        foreach ($categories as $category) {
            $category = Category::factory()->create([
                'name' => $category,
            ]);

            $shop->categories()->attach($category);
        }
    }
}
