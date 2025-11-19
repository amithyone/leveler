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
            $table->boolean('is_installment')->default(false)->after('package_type');
            $table->integer('installment_number')->nullable()->after('is_installment');
            $table->decimal('total_required', 10, 2)->nullable()->after('amount'); // Total amount required for the package
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->decimal('total_paid', 10, 2)->default(0)->after('available_courses');
            $table->decimal('total_required', 10, 2)->default(0)->after('total_paid'); // Total amount required for their package
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['total_paid', 'total_required']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['is_installment', 'installment_number', 'total_required']);
        });
    }
};
