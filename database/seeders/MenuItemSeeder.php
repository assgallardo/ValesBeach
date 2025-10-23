<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $breakfast = MenuCategory::where('name', 'Breakfast')->first();
        $lunch = MenuCategory::where('name', 'Lunch')->first();
        $dinner = MenuCategory::where('name', 'Dinner')->first();
        $dessert = MenuCategory::where('name', 'Dessert')->first();
        $snacks = MenuCategory::where('name', 'Snacks')->first();
        $drinks = MenuCategory::where('name', 'Drinks')->first();

        $menuItems = [
            // Breakfast - 1 item
            [
                'menu_category_id' => $breakfast->id,
                'name' => 'Caribbean Breakfast Platter',
                'description' => 'Eggs, bacon, sausage, fried plantains, and Johnny cakes',
                'price' => 14.99,
                'ingredients' => ['eggs', 'bacon', 'sausage', 'plantains', 'johnny cakes'],
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 15,
                'is_featured' => true,
                'calories' => 680,
                'popularity_score' => 85,
            ],

            // Lunch - 1 item
            [
                'menu_category_id' => $lunch->id,
                'name' => 'Grilled Chicken Caesar Salad',
                'description' => 'Classic Caesar salad with grilled chicken breast and house-made croutons',
                'price' => 15.99,
                'ingredients' => ['romaine lettuce', 'chicken breast', 'parmesan', 'croutons'],
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 15,
                'calories' => 420,
                'popularity_score' => 82,
            ],

            // Dinner - 1 item
            [
                'menu_category_id' => $dinner->id,
                'name' => 'Grilled Ribeye Steak',
                'description' => '12oz prime ribeye grilled to perfection, served with seasonal vegetables',
                'price' => 32.99,
                'ingredients' => ['ribeye steak', 'mixed vegetables', 'herbs', 'butter'],
                'allergens' => ['dairy'],
                'preparation_time' => 20,
                'is_gluten_free' => true,
                'is_featured' => true,
                'calories' => 720,
                'popularity_score' => 90,
            ],

            // Dessert - 1 item
            [
                'menu_category_id' => $dessert->id,
                'name' => 'Caribbean Rum Cake',
                'description' => 'Traditional Caribbean rum cake with vanilla ice cream',
                'price' => 9.99,
                'ingredients' => ['rum', 'cake', 'vanilla ice cream', 'raisins'],
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 10,
                'is_featured' => true,
                'calories' => 380,
                'popularity_score' => 85,
            ],

            // Snacks - 1 item
            [
                'menu_category_id' => $snacks->id,
                'name' => 'Coconut Shrimp',
                'description' => 'Jumbo shrimp coated in coconut flakes, served with pineapple dipping sauce',
                'price' => 16.99,
                'ingredients' => ['shrimp', 'coconut', 'pineapple', 'breadcrumbs'],
                'allergens' => ['shellfish', 'gluten'],
                'preparation_time' => 18,
                'is_featured' => true,
                'calories' => 340,
                'popularity_score' => 88,
            ],

            // Drinks - 1 item
            [
                'menu_category_id' => $drinks->id,
                'name' => 'Tropical Piña Colada',
                'description' => 'Classic tropical cocktail with rum, coconut cream, and fresh pineapple',
                'price' => 12.99,
                'ingredients' => ['rum', 'coconut cream', 'pineapple juice', 'ice'],
                'allergens' => [],
                'preparation_time' => 5,
                'is_featured' => true,
                'calories' => 280,
                'popularity_score' => 90,
            ],
        ];

        foreach ($menuItems as $item) {
            // Convert arrays to JSON for storage
            if (isset($item['ingredients'])) {
                $item['ingredients'] = json_encode($item['ingredients']);
            }
            if (isset($item['allergens'])) {
                $item['allergens'] = json_encode($item['allergens']);
            }
            
            MenuItem::create($item);
        }
    }
}
