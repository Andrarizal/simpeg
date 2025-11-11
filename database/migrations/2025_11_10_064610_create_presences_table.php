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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'presences_staff_id'
            )->cascadeOnDelete();
            $table->date('presence_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();

            // Satu staff hanya bisa absen sekali per hari
            $table->unique(['staff_id', 'presence_date'], 'unique_staff_date');

            // Satu device (fingerprint) hanya bisa dipakai sekali per hari
            $table->unique(['fingerprint', 'presence_date'], 'unique_device_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
