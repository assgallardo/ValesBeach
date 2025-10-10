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
        $appetizers = MenuCategory::where('name', 'Appetizers')->first();
        $salads = MenuCategory::where('name', 'Salads')->first();
        $soups = MenuCategory::where('name', 'Soups')->first();
        $seafood = MenuCategory::where('name', 'Seafood')->first();
        $steaks = MenuCategory::where('name', 'Steaks & Grills')->first();
        $pasta = MenuCategory::where('name', 'Pasta')->first();
        $caribbean = MenuCategory::where('name', 'Caribbean Specialties')->first();
        $vegetarian = MenuCategory::where('name', 'Vegetarian')->first();
        $desserts = MenuCategory::where('name', 'Desserts')->first();
        $beverages = MenuCategory::where('name', 'Beverages')->first();
        $kids = MenuCategory::where('name', 'Kids Menu')->first();

        $menuItems = [
            // Appetizers
            [
                'menu_category_id' => $appetizers->id,
                'name' => 'Caribbean Spring Rolls',
                'description' => 'Crispy spring rolls filled with jerk chicken, bell peppers, and tropical fruit salsa',
                'price' => 12.99,
                'ingredients' => ['chicken', 'bell peppers', 'mango', 'cabbage', 'carrots'],
                'allergens' => ['gluten', 'soy'],
                'preparation_time' => 15,
                'is_featured' => true,
                'calories' => 285,
                'popularity_score' => 85,
            ],
            [
                'menu_category_id' => $appetizers->id,
                'name' => 'Conch Fritters',
                'description' => 'Traditional Bahamian conch fritters served with spicy aioli',
                'price' => 14.99,
                'ingredients' => ['conch', 'flour', 'onions', 'peppers'],
                'allergens' => ['gluten', 'shellfish'],
                'preparation_time' => 20,
                'calories' => 320,
                'popularity_score' => 90,
            ],
            [
                'menu_category_id' => $appetizers->id,
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

            // Salads
            [
                'menu_category_id' => $salads->id,
                'name' => 'Tropical Mango Salad',
                'description' => 'Mixed greens with fresh mango, avocado, candied pecans, and citrus vinaigrette',
                'price' => 13.99,
                'ingredients' => ['mixed greens', 'mango', 'avocado', 'pecans', 'red onion'],
                'allergens' => ['nuts'],
                'preparation_time' => 10,
                'is_vegetarian' => true,
                'is_vegan' => true,
                'is_gluten_free' => true,
                'calories' => 280,
                'popularity_score' => 75,
            ],
            [
                'menu_category_id' => $salads->id,
                'name' => 'Grilled Chicken Caesar',
                'description' => 'Classic Caesar salad with grilled chicken breast and house-made croutons',
                'price' => 15.99,
                'ingredients' => ['romaine lettuce', 'chicken breast', 'parmesan', 'croutons'],
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 15,
                'calories' => 420,
                'popularity_score' => 82,
            ],

            // Soups
            [
                'menu_category_id' => $soups->id,
                'name' => 'Caribbean Pumpkin Soup',
                'description' => 'Creamy roasted pumpkin soup with coconut milk and island spices',
                'price' => 8.99,
                'ingredients' => ['pumpkin', 'coconut milk', 'ginger', 'nutmeg', 'thyme'],
                'allergens' => [],
                'preparation_time' => 12,
                'is_vegetarian' => true,
                'is_vegan' => true,
                'is_gluten_free' => true,
                'calories' => 180,
                'popularity_score' => 70,
            ],

            // Seafood
            [
                'menu_category_id' => $seafood->id,
                'name' => 'Grilled Mahi Mahi',
                'description' => 'Fresh mahi mahi grilled with Caribbean herbs, served with rice and vegetables',
                'price' => 26.99,
                'ingredients' => ['mahi mahi', 'rice', 'bell peppers', 'onions', 'herbs'],
                'allergens' => [],
                'preparation_time' => 25,
                'is_gluten_free' => true,
                'is_featured' => true,
                'calories' => 480,
                'popularity_score' => 92,
            ],
            [
                'menu_category_id' => $seafood->id,
                'name' => 'Lobster Thermidor',
                'description' => 'Caribbean lobster tail in creamy herb sauce, served with garlic mashed potatoes',
                'price' => 38.99,
                'ingredients' => ['lobster', 'cream', 'herbs', 'potatoes', 'garlic'],
                'allergens' => ['shellfish', 'dairy'],
                'preparation_time' => 30,
                'calories' => 650,
                'popularity_score' => 85,
            ],

            // Steaks & Grills
            [
                'menu_category_id' => $steaks->id,
                'name' => 'Ribeye Steak',
                'description' => '12oz prime ribeye grilled to perfection, served with seasonal vegetables',
                'price' => 32.99,
                'ingredients' => ['ribeye steak', 'mixed vegetables', 'herbs', 'butter'],
                'allergens' => ['dairy'],
                'preparation_time' => 20,
                'is_gluten_free' => true,
                'calories' => 720,
                'popularity_score' => 90,
            ],
            [
                'menu_category_id' => $steaks->id,
                'name' => 'Jerk Chicken',
                'description' => 'Authentic Jamaican jerk chicken with rice and peas',
                'price' => 19.99,
                'ingredients' => ['chicken', 'jerk seasoning', 'rice', 'kidney beans', 'coconut milk'],
                'allergens' => [],
                'preparation_time' => 25,
                'is_gluten_free' => true,
                'is_spicy' => true,
                'is_featured' => true,
                'calories' => 580,
                'popularity_score' => 95,
            ],

            // Pasta
            [
                'menu_category_id' => $pasta->id,
                'name' => 'Seafood Linguine',
                'description' => 'Fresh linguine with shrimp, scallops, and mussels in white wine sauce',
                'price' => 24.99,
                'ingredients' => ['linguine', 'shrimp', 'scallops', 'mussels', 'white wine'],
                'allergens' => ['gluten', 'shellfish'],
                'preparation_time' => 20,
                'calories' => 520,
                'popularity_score' => 88,
            ],

            // Caribbean Specialties
            [
                'menu_category_id' => $caribbean->id,
                'name' => 'Curry Goat',
                'description' => 'Traditional Caribbean curry goat with rice and peas',
                'price' => 22.99,
                'ingredients' => ['goat meat', 'curry powder', 'rice', 'kidney beans', 'coconut milk'],
                'allergens' => [],
                'preparation_time' => 35,
                'is_gluten_free' => true,
                'is_spicy' => true,
                'calories' => 620,
                'popularity_score' => 78,
            ],
            [
                'menu_category_id' => $caribbean->id,
                'name' => 'Ackee and Saltfish',
                'description' => 'Jamaica\'s national dish with ackee fruit and salted codfish',
                'price' => 18.99,
                'ingredients' => ['ackee', 'saltfish', 'onions', 'tomatoes', 'peppers'],
                'allergens' => ['fish'],
                'preparation_time' => 20,
                'is_gluten_free' => true,
                'calories' => 380,
                'popularity_score' => 72,
            ],

            // Vegetarian
            [
                'menu_category_id' => $vegetarian->id,
                'name' => 'Quinoa Buddha Bowl',
                'description' => 'Quinoa bowl with roasted vegetables, avocado, and tahini dressing',
                'price' => 16.99,
                'ingredients' => ['quinoa', 'roasted vegetables', 'avocado', 'tahini'],
                'allergens' => ['sesame'],
                'preparation_time' => 15,
                'is_vegetarian' => true,
                'is_vegan' => true,
                'is_gluten_free' => true,
                'calories' => 420,
                'popularity_score' => 68,
            ],

            // Desserts
            [
                'menu_category_id' => $desserts->id,
                'name' => 'Rum Cake',
                'description' => 'Traditional Caribbean rum cake with vanilla ice cream',
                'price' => 9.99,
                'ingredients' => ['rum', 'cake', 'vanilla ice cream', 'raisins'],
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 10,
                'is_featured' => true,
                'calories' => 380,
                'popularity_score' => 85,
            ],
            [
                'menu_category_id' => $desserts->id,
                'name' => 'Key Lime Pie',
                'description' => 'Classic Key lime pie with graham cracker crust',
                'price' => 8.99,
                'ingredients' => ['key limes', 'condensed milk', 'graham crackers', 'butter'],
                'allergens' => ['gluten', 'dairy'],
                'preparation_time' => 8,
                'calories' => 320,
                'popularity_score' => 80,
            ],

            // Beverages
            [
                'menu_category_id' => $beverages->id,
                'name' => 'Piña Colada',
                'description' => 'Classic tropical cocktail with rum, coconut, and pineapple',
                'price' => 12.99,
                'ingredients' => ['rum', 'coconut cream', 'pineapple juice'],
                'allergens' => [],
                'preparation_time' => 5,
                'calories' => 280,
                'popularity_score' => 90,
            ],
            [
                'menu_category_id' => $beverages->id,
                'name' => 'Fresh Coconut Water',
                'description' => 'Fresh coconut water served in the shell',
                'price' => 6.99,
                'ingredients' => ['coconut water'],
                'allergens' => [],
                'preparation_time' => 3,
                'is_vegetarian' => true,
                'is_vegan' => true,
                'is_gluten_free' => true,
                'calories' => 45,
                'popularity_score' => 75,
            ],

            // Kids Menu
            [
                'menu_category_id' => $kids->id,
                'name' => 'Chicken Tenders',
                'description' => 'Crispy chicken tenders with fries and honey mustard',
                'price' => 8.99,
                'ingredients' => ['chicken tenders', 'fries', 'honey mustard'],
                'allergens' => ['gluten'],
                'preparation_time' => 12,
                'calories' => 420,
                'popularity_score' => 85,
            ],
            [
                'menu_category_id' => $kids->id,
                'name' => 'Mini Cheese Pizza',
                'description' => 'Personal size cheese pizza perfect for kids',
                'price' => 7.99,
                'ingredients' => ['pizza dough', 'tomato sauce', 'mozzarella cheese'],
                'allergens' => ['gluten', 'dairy'],
                'preparation_time' => 15,
                'is_vegetarian' => true,
                'calories' => 350,
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
