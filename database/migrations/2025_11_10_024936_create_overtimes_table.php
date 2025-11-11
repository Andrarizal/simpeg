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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'overtimes_staff_id'
            )->cascadeOnDelete();
            $table->date('overtime_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->text('command');
            $table->decimal('hours', 8, 1)->nullable();
            $table->string('month_year');
            $table->unsignedTinyInteger('is_known')->nullable();
            $table->unsignedTinyInteger('is_verified')->nullable();
            $table->timestamps();

            $table->index(['month_year', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
