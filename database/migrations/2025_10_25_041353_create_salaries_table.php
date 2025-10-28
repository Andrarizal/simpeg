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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'salaries_staff_id'
            );
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('year');
            $table->unsignedBigInteger('basic_salary');
            $table->unsignedBigInteger('subsidy');
            $table->unsignedBigInteger('deduction');
            $table->unsignedBigInteger('total');
            $table->string('file_slip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
