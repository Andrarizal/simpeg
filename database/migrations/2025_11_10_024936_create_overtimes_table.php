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
            $table->unsignedTinyInteger('known_by')->nullable();
            $table->foreignId('known_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'overtimes_known_by'
            )->nullOnDelete();
            $table->date('known_at')->nullable();
            $table->unsignedTinyInteger('is_verified')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'overtimes_verified_by'
            )->nullOnDelete();
            $table->date('verified_at')->nullable();
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
