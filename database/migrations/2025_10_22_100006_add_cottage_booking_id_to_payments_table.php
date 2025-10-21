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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cottage_booking_id')->nullable()->after('booking_id')
                  ->constrained()->onDelete('cascade');
            
            $table->index('cottage_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['cottage_booking_id']);
            $table->dropColumn('cottage_booking_id');
        });
    }
};
