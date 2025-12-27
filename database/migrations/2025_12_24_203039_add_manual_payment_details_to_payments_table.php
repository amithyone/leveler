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
            $table->json('manual_payment_details')->nullable()->after('notes');
        });
        
        // Update enum values for payment_method to include Manual Payment
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('Cash', 'Bank Transfer', 'Mobile Money', 'Card', 'Other', 'Manual Payment') DEFAULT 'Cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
