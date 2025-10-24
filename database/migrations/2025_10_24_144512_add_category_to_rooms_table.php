<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('category', ['Rooms', 'Cottages', 'Event and Dining'])->default('Rooms')->after('type');
        });

        // Update existing data based on type
        // Umbrella Cottage, Bahay Kubo → Cottages
        DB::table('rooms')
            ->where('type', 'LIKE', '%Cottage%')
            ->orWhere('type', 'LIKE', '%Kubo%')
            ->update(['category' => 'Cottages']);

        // Function Hall, Beer Garden, Dining Hall → Event and Dining
        DB::table('rooms')
            ->where('type', 'LIKE', '%Hall%')
            ->orWhere('type', 'LIKE', '%Garden%')
            ->update(['category' => 'Event and Dining']);

        // Everything else (Room, Executive, etc.) → Rooms
        DB::table('rooms')
            ->where('category', '!=', 'Cottages')
            ->where('category', '!=', 'Event and Dining')
            ->update(['category' => 'Rooms']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
