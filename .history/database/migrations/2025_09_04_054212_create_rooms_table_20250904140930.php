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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->integer('capacity')->default(2);
            $table->boolean('is_visible')->default(true);
            $table->string('room_type');
            $table->string('image_path')->nullable();
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
