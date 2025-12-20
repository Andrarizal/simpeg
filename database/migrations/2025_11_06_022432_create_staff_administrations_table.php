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
        Schema::create('staff_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'staff_administration_staff_id'
            )->cascadeOnDelete();
            $table->string('sip')->nullable();
            $table->date('sip_expiry')->nullable();
            $table->string('str')->nullable();
            $table->date('str_expiry')->nullable();
            $table->string('mcu')->nullable();
            $table->date('mcu_expiry')->nullable();
            $table->string('spk')->nullable();
            $table->date('spk_expiry')->nullable();
            $table->string('rkk')->nullable();
            $table->date('rkk_expiry')->nullable();
            $table->string('utw')->nullable();
            $table->date('utw_expiry')->nullable();
            $table->unsignedTinyInteger('is_verified')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_administrations');
    }
};
