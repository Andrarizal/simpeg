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
        Schema::create('performance_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'performance_targets_staff_id'
            );
            $table->foreignId('period_id')->constrained(
                table: 'performance_periods',
                indexName: 'performance_targets_period_id'
            );
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('value');
            $table->unsignedInteger('weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_targets');
    }
};
