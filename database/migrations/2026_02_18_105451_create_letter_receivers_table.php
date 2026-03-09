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
        Schema::create('letter_receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained(
                table: 'letters',
                indexName: 'letter_receivers_letter_id'
            )->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'letter_receivers_staff_id'
            )->cascadeOnDelete();
            $table->unsignedTinyInteger('is_attend')->nullable();
            $table->text('outline')->nullable();
            $table->string('content_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_receivers');
    }
};
