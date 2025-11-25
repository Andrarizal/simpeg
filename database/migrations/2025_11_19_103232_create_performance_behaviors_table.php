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
        Schema::create('performance_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained(
                table: 'performance_periods',
                indexName: 'performance_behaviors_period_id'
            );
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'performance_behaviors_staff_id'
            );
            $table->enum('type', ['Integritas', 'Disiplin', 'Komunikasi', 'Kerja Sama', 'Pelayanan']);
            $table->decimal('score', 5, 2);
            $table->foreignId('reviewer_id')->constrained(
                table: 'staff',
                indexName: 'performance_behaviors_reviewer_id'
            );
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_behaviors');
    }
};
