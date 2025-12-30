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
        Schema::table('manual_payment_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('manual_payment_settings', 'payment_instructions')) {
                $table->text('payment_instructions')->nullable()->after('account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_payment_settings', function (Blueprint $table) {
            if (Schema::hasColumn('manual_payment_settings', 'payment_instructions')) {
                $table->dropColumn('payment_instructions');
            }
        });
    }
};
