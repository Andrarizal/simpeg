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
        Schema::create('pre_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nik')->unique();
            $table->string('nip')->unique();
            $table->string('name');
            $table->date('birth_date');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->foreignId('staff_status_id')->nullable()->constrained(
                table: 'staff_statuses',
                indexName: 'pre_staff_staff_status_id'
            )->nullOnDelete();
            $table->foreignId('chair_id')->nullable()->constrained(
                table: 'chairs',
                indexName: 'pre_staff_chair_id'
            )->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained(
                table: 'groups',
                indexName: 'pre_staff_group_id'
            )->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained(
                table: 'units',
                indexName: 'pre_staff_unit_id'
            )->nullOnDelete();
            $table->string('token');
            $table->enum('status', ['Menunggu', 'Diverifikasi', 'Ditolak']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_staff');
    }
};
