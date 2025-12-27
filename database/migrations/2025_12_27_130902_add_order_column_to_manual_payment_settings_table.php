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
            if (!Schema::hasColumn('manual_payment_settings', 'order')) {
                $table->integer('order')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_payment_settings', function (Blueprint $table) {
            if (Schema::hasColumn('manual_payment_settings', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};
