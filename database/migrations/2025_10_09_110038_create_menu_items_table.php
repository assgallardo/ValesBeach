<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->integer('preparation_time')->default(15); // minutes
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->boolean('is_dairy_free')->default(false);
            $table->boolean('is_spicy')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('calories')->nullable();
            $table->integer('popularity_score')->default(0);
            $table->timestamps();
            
            $table->index(['menu_category_id', 'is_available']);
            $table->index(['is_featured', 'is_available']);
            $table->index(['is_vegetarian', 'is_available']);
            $table->index(['is_vegan', 'is_available']);
            $table->index(['is_gluten_free', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
