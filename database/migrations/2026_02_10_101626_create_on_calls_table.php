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
        Schema::create('on_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'on_calls_staff_id'
            )->cascadeOnDelete();
            $table->date('oncall_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->decimal('hours', 8, 1)->nullable();
            $table->foreignId('period_id')->constrained(
                table: 'monthly_periods',
                indexName: 'on_calls_period_id'
            )->cascadeOnDelete();
            $table->text('command');
            $table->foreignId('command_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'on_calls_command_by'
            )->nullOnDelete();
            $table->unsignedTinyInteger('is_known')->nullable();
            $table->datetime('known_at')->nullable();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('is_verified')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'on_calls_verified_by'
            )->nullOnDelete();
            $table->datetime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('on_calls');
    }
};
