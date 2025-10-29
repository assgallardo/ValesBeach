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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
            $table->date('invoice_date')->nullable()->after('status');
            $table->json('items')->nullable()->after('line_items');
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['amount_paid', 'balance_due', 'invoice_date', 'items', 'created_by']);
        });
    }
};
