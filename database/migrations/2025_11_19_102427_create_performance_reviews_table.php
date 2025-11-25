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
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained(
                table: 'performance_targets',
                indexName: 'performance_reviews_target_id'
            );
            $table->foreignId('reviewer_id')->constrained(
                table: 'staff',
                indexName: 'performance_reviews_reviewer_id'
            );
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
