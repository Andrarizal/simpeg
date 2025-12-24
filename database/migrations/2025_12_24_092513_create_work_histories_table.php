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
        Schema::create('work_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'work_histories_staff_id',
            )->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained(
                table: 'units',
                indexName: 'work_histories_unit_id',
            )->cascadeOnDelete();
            $table->foreignId('chair_id')->constrained(
                table: 'chairs',
                indexName: 'work_histories_chair_id',
            )->cascadeOnDelete();
            $table->foreignId('staff_status_id')->constrained(
                table: 'staff_statuses',
                indexName: 'work_histories_staff_status_id',
            )->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('decree_number')->nullable();
            $table->date('decree_date')->nullable();
            $table->string('class')->nullable();
            $table->string('decree')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_histories');
    }
};
