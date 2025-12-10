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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'schedules_staff_id'
            )->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained(
                table: 'shifts',
                indexName: 'schedules_shift_id'
            )->cascadeOnDelete();
            $table->date('schedule_date');
            $table->unsignedTinyInteger('is_locked')->default(0);
            $table->timestamps();

            $table->unique(['staff_id', 'schedule_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
