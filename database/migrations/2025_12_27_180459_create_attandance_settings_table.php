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
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();

            $table->time('start_work')->nullable();
            $table->time('end_work')->nullable();

            $table->decimal('alpha', 8, 2)->default(0);
            $table->decimal('izin', 8, 2)->default(0);
            $table->decimal('cuti', 8, 2)->default(0);
            $table->decimal('telat', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attandance_setting');
    }
};
