<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom bank info + xendit ke cash_advance
        Schema::table('cash_advance', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('title');
            $table->string('account_number', 50)->nullable()->after('bank_name');
            $table->string('account_holder_name', 100)->nullable()->after('account_number');
            $table->string('xendit_disbursement_id', 100)->nullable()->after('account_holder_name');
            $table->string('xendit_status', 50)->nullable()->after('xendit_disbursement_id');
            $table->timestamp('transfer_at')->nullable()->after('xendit_status');
        });

        // Update enum status untuk mendukung status baru
        // MySQL: modifikasi enum kolom
        DB::statement("ALTER TABLE cash_advance MODIFY COLUMN status ENUM('pending','approved','transferring','transferred','failed','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('cash_advance', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_number',
                'account_holder_name',
                'xendit_disbursement_id',
                'xendit_status',
                'transfer_at',
            ]);
        });

        DB::statement("ALTER TABLE cash_advance MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
