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
                'name' => 'Appetizers',
                'description' => 'Start your meal with our delicious appetizers and small plates',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Salads',
                'description' => 'Fresh and healthy salads made with locally sourced ingredients',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Soups',
                'description' => 'Warm and comforting soups perfect for any time of day',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Seafood',
                'description' => 'Fresh catch from the Caribbean waters, prepared to perfection',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Steaks & Grills',
                'description' => 'Premium cuts of meat grilled to your preference',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Pasta',
                'description' => 'Traditional and modern pasta dishes with house-made sauces',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Caribbean Specialties',
                'description' => 'Authentic Caribbean flavors and traditional dishes',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Vegetarian',
                'description' => 'Plant-based dishes full of flavor and nutrition',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet endings to complete your dining experience',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Beverages',
                'description' => 'Refreshing drinks, cocktails, and specialty beverages',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Kids Menu',
                'description' => 'Child-friendly meals that kids will love',
                'sort_order' => 11,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
