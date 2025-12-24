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
        Schema::table('payments', function (Blueprint $table) {
            $table->json('manual_payment_details')->nullable()->after('notes');
        });

        // Update payment_method enum to include 'Manual Payment'
        // Note: MySQL doesn't support modifying ENUM directly, so we'll use ALTER COLUMN
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('Cash', 'Bank Transfer', 'Mobile Money', 'Card', 'Manual Payment', 'Other') DEFAULT 'Cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('manual_payment_details');
        });

        // Revert payment_method enum
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('Cash', 'Bank Transfer', 'Mobile Money', 'Card', 'Other') DEFAULT 'Cash'");
    }
};
