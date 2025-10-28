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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'attendances_staff_id'
            );
            $table->date('date_attendance');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
