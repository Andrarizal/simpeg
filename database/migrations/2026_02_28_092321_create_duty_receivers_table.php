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
        Schema::create('duty_receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duty_id')->constrained(
                table: 'duties',
                indexName: 'duty_receivers_duty_id',
            )->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'duty_receivers_staff_id',
            )->cascadeOnDelete();
            $table->text('outline')->nullable();
            $table->unsignedTinyInteger('is_workhour')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedTinyInteger('image_verified')->nullable();
            $table->string('content_path')->nullable();
            $table->unsignedTinyInteger('content_verified')->nullable();
            $table->string('letter_path')->nullable();
            $table->unsignedTinyInteger('letter_verified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duty_receivers');
    }
};
