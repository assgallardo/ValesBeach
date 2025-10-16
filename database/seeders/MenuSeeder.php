<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Menu Categories
        $appetizers = MenuCategory::create([
            'name' => 'Appetizers',
            'description' => 'Start your meal with these delicious appetizers',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $mainCourse = MenuCategory::create([
            'name' => 'Main Course',
            'description' => 'Our signature main dishes',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $seafood = MenuCategory::create([
            'name' => 'Seafood',
            'description' => 'Fresh from the sea',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $desserts = MenuCategory::create([
            'name' => 'Desserts',
            'description' => 'Sweet endings',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $beverages = MenuCategory::create([
            'name' => 'Beverages',
            'description' => 'Refreshing drinks',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        // Create Menu Items

        // Appetizers
        MenuItem::create([
            'menu_category_id' => $appetizers->id,
            'name' => 'Lumpia Shanghai',
            'description' => 'Crispy Filipino spring rolls filled with ground pork and vegetables',
            'price' => 150.00,
            'preparation_time' => 15,
            'calories' => 250,
            'is_available' => true,
            'is_featured' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $appetizers->id,
            'name' => 'Calamares',
            'description' => 'Crispy fried squid rings served with special dipping sauce',
            'price' => 180.00,
            'preparation_time' => 20,
            'calories' => 300,
            'is_available' => true,
            'is_featured' => false,
        ]);

        // Main Course
        MenuItem::create([
            'menu_category_id' => $mainCourse->id,
            'name' => 'Chicken Adobo',
            'description' => 'Traditional Filipino dish with tender chicken in soy sauce and vinegar',
            'price' => 220.00,
            'preparation_time' => 25,
            'calories' => 450,
            'is_available' => true,
            'is_featured' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $mainCourse->id,
            'name' => 'Beef Sinigang',
            'description' => 'Sour beef soup with vegetables in tamarind broth',
            'price' => 250.00,
            'preparation_time' => 30,
            'calories' => 400,
            'is_available' => true,
            'is_featured' => false,
        ]);

        MenuItem::create([
            'menu_category_id' => $mainCourse->id,
            'name' => 'Pork Sisig',
            'description' => 'Sizzling chopped pork with onions and peppers',
            'price' => 200.00,
            'preparation_time' => 20,
            'calories' => 500,
            'is_spicy' => true,
            'is_available' => true,
            'is_featured' => true,
        ]);

        // Seafood
        MenuItem::create([
            'menu_category_id' => $seafood->id,
            'name' => 'Grilled Bangus',
            'description' => 'Grilled milkfish stuffed with tomatoes and onions',
            'price' => 280.00,
            'preparation_time' => 25,
            'calories' => 350,
            'is_available' => true,
            'is_featured' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $seafood->id,
            'name' => 'Prawns in Garlic Butter',
            'description' => 'Fresh prawns cooked in rich garlic butter sauce',
            'price' => 350.00,
            'preparation_time' => 20,
            'calories' => 400,
            'is_available' => true,
            'is_featured' => false,
        ]);

        MenuItem::create([
            'menu_category_id' => $seafood->id,
            'name' => 'Fish Fillet',
            'description' => 'Crispy fried fish fillet with tartar sauce',
            'price' => 230.00,
            'preparation_time' => 18,
            'calories' => 320,
            'is_available' => true,
            'is_featured' => false,
        ]);

        // Desserts
        MenuItem::create([
            'menu_category_id' => $desserts->id,
            'name' => 'Halo-Halo',
            'description' => 'Classic Filipino dessert with shaved ice, beans, fruits, and ice cream',
            'price' => 120.00,
            'preparation_time' => 10,
            'calories' => 380,
            'is_available' => true,
            'is_featured' => true,
            'is_vegetarian' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $desserts->id,
            'name' => 'Leche Flan',
            'description' => 'Creamy caramel custard',
            'price' => 80.00,
            'preparation_time' => 5,
            'calories' => 280,
            'is_available' => true,
            'is_featured' => false,
            'is_vegetarian' => true,
        ]);

        // Beverages
        MenuItem::create([
            'menu_category_id' => $beverages->id,
            'name' => 'Buko Juice',
            'description' => 'Fresh coconut juice',
            'price' => 60.00,
            'preparation_time' => 5,
            'calories' => 45,
            'is_available' => true,
            'is_featured' => true,
            'is_vegetarian' => true,
            'is_vegan' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $beverages->id,
            'name' => 'Mango Shake',
            'description' => 'Fresh mango blended with milk and ice',
            'price' => 80.00,
            'preparation_time' => 5,
            'calories' => 200,
            'is_available' => true,
            'is_featured' => false,
            'is_vegetarian' => true,
        ]);

        MenuItem::create([
            'menu_category_id' => $beverages->id,
            'name' => 'Iced Coffee',
            'description' => 'Chilled coffee with cream',
            'price' => 70.00,
            'preparation_time' => 5,
            'calories' => 150,
            'is_available' => true,
            'is_featured' => false,
            'is_vegetarian' => true,
        ]);

        $this->command->info('Menu categories and items seeded successfully!');
        $this->command->info('Categories: 5');
        $this->command->info('Menu Items: 13');
    }
}
