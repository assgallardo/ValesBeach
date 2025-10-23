<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Breakfast',
                'description' => 'Start your day with our delicious breakfast selections',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Lunch',
                'description' => 'Fresh and satisfying lunch options',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Dinner',
                'description' => 'Premium dinner entrees and specialties',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Dessert',
                'description' => 'Sweet endings to complete your dining experience',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Snacks',
                'description' => 'Light bites and appetizers',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Drinks',
                'description' => 'Refreshing beverages and specialty drinks',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
