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
        Schema::table('cash_advance', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_advance', 'payment_type')) {
                $table->enum('payment_type', ['xendit', 'manual'])->default('xendit')->after('amount');
            }
            if (!Schema::hasColumn('cash_advance', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->default(0)->after('payment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_advance', function (Blueprint $table) {
            if (Schema::hasColumn('cash_advance', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('cash_advance', 'admin_fee')) {
                $table->dropColumn('admin_fee');
            }
        });
    }
};
