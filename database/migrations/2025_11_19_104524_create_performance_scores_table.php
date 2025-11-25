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
        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'performance_scores_staff_id'
            );
            $table->foreignId('period_id')->constrained(
                table: 'performance_periods',
                indexName: 'performance_scores_period_id'
            );
            $table->decimal('kpi_score', 5, 2);
            $table->decimal('behavior_score', 5, 2);
            $table->decimal('total_score', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_scores');
    }
};
