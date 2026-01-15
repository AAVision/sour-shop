<?php

namespace Database\Seeders;

use App\Models\ThemeColor;
use Illuminate\Database\Seeder;

class ThemeColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ThemeColor::truncate();

        $this->defaultColor();

        // Create a color only on local
        if (app()->environment('local')) {
            $this->DemoColors();
        }
    }

    private function defaultColor()
    {
        $color = [
            'primary' => '#EE456B',
            'secondary' => '#FEE5E8',
            'variant_50' => '#FFF1F3',
            'variant_100' => '#FEE5E8',
            'variant_200' => '#FCCFD6',
            'variant_300' => '#FAA7B5',
            'variant_400' => '#F7758F',
            'variant_500' => '#EE456B',
            'variant_600' => '#DD2C5C',
            'variant_700' => '#B91747',
            'variant_800' => '#9B1642',
            'variant_900' => '#84173E',
            'variant_950' => '#4A071D',
            'is_default' => true,
        ];

        ThemeColor::create($color);
    }

    private function DemoColors()
    {
        $colors = [
            [
                'primary' => '#a855f7',
                'secondary' => '#f3e8ff',
                'variant_50' => '#faf5ff',
                'variant_100' => '#f3e8ff',
                'variant_200' => '#e9d5ff',
                'variant_300' => '#d8b4fe',
                'variant_400' => '#c084fc',
                'variant_500' => '#a855f7',
                'variant_600' => '#9333ea',
                'variant_700' => '#7e22ce',
                'variant_800' => '#6b21a8',
                'variant_900' => '#581c87',
                'variant_950' => '#3b0764',
                'is_default' => false,
            ],
            [
                'primary' => '#8b5cf6',
                'secondary' => '#ede9fe',
                'variant_50' => '#f5f3ff',
                'variant_100' => '#ede9fe',
                'variant_200' => '#ddd6fe',
                'variant_300' => '#c4b5fd',
                'variant_400' => '#a78bfa',
                'variant_500' => '#8b5cf6',
                'variant_600' => '#7c3aed',
                'variant_700' => '#6d28d9',
                'variant_800' => '#5b21b6',
                'variant_900' => '#4c1d95',
                'variant_950' => '#2e1065',
                'is_default' => false,
            ],
        ];

        ThemeColor::insert($colors);
    }
}
