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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('salarie')->onDelete('CASCADE');
            $table->foreignId('user_id')->constrained('users')->onDelete('CASCADE');
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();

            // Periode gaji
            $table->tinyInteger('period_month');  // 1–12
            $table->smallInteger('period_year');  // misal: 2026

            // Komponen gaji
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_cash_advance', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);

            // Rekening tujuan
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();

            // Xendit
            $table->string('xendit_external_id')->nullable()->unique();
            $table->string('xendit_disbursement_id')->nullable();
            $table->string('xendit_status')->nullable(); // PENDING, COMPLETED, FAILED

            // Status internal
            $table->enum('status', ['pending', 'transferred', 'failed'])->default('pending');
            $table->timestamp('transfer_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
