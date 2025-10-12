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
        Schema::create('expenditure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('cash_advance_type')->onDelete('CASCADE');
            $table->date('date');
            $table->string('title');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenditure');
    }
};
