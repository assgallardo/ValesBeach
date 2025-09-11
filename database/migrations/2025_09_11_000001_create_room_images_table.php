<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@valesbeach.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active'
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('room_images');
    }
};